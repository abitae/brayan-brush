<?php

namespace App\Services;

use App\Models\SiteConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingService
{
    /** Estados posibles del seguimiento */
    public const STATUS_REGISTRADO = 'registrado';

    public const STATUS_EN_CAMINO = 'en_camino';

    public const STATUS_EN_ALMACEN = 'en_almacen';

    public const STATUS_RECOGIDO = 'recogido';

    /**
     * Consulta el estado de un envío por código.
     * Si está configurada la URL del servicio externo, la llama; si no, devuelve datos ficticios.
     *
     * @return array{code: string, status: string, status_label: string, current_location: string|null, origin: string, destination: string, estimated_delivery: string|null, progress: int, history: array<int, array{date: string, location: string, desc: string}>}
     */
    public function track(string $code): array
    {
        $code = trim($code);
        $url = null;
        try {
            $config = SiteConfig::default();
            $url = $config->tracking_api_url ?? null;
        } catch (\Throwable) {
            // Tabla o columna no disponible; usar datos ficticios
        }

        if (! empty($url) && is_string($url)) {
            $result = $this->callExternalApi($url, $code);
            if ($result !== null) {
                return $result;
            }
        }

        return $this->mockData($code);
    }

    /**
     * Llama al endpoint externo. Espera GET con ?codigo= o ?code=
     * Respuesta esperada (ejemplo): { "code": "BB-001", "status": "en_camino", "current_location": "...", "origin": "...", "destination": "...", "estimated_delivery": "...", "history": [{ "date": "...", "location": "...", "desc": "..." }] }
     */
    private function callExternalApi(string $baseUrl, string $code): ?array
    {
        $url = str_contains($baseUrl, '?') ? $baseUrl.'&codigo='.urlencode($code) : rtrim($baseUrl, '/').'?codigo='.urlencode($code);

        try {
            $response = Http::timeout(10)->get($url);

            if (! $response->successful()) {
                Log::warning('Tracking API error', ['url' => $url, 'status' => $response->status()]);

                return null;
            }

            $data = $response->json();
            if (! is_array($data)) {
                return null;
            }

            return $this->normalizeExternalResponse($code, $data);
        } catch (\Throwable $e) {
            Log::warning('Tracking API exception', ['url' => $url, 'message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Normaliza la respuesta del servicio externo al formato interno.
     * Acepta campos en español o inglés.
     */
    private function normalizeExternalResponse(string $code, array $data): array
    {
        $status = $this->normalizeStatus($data['status'] ?? $data['estado'] ?? self::STATUS_REGISTRADO);
        $history = [];
        foreach ($data['history'] ?? $data['historial'] ?? [] as $h) {
            $history[] = [
                'date' => $h['date'] ?? $h['fecha'] ?? '',
                'location' => $h['location'] ?? $h['ubicacion'] ?? '',
                'desc' => $h['desc'] ?? $h['descripcion'] ?? '',
            ];
        }

        return [
            'code' => $data['code'] ?? $data['codigo'] ?? $code,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'current_location' => $data['current_location'] ?? $data['ubicacion_actual'] ?? null,
            'origin' => $data['origin'] ?? $data['origen'] ?? 'Origen',
            'destination' => $data['destination'] ?? $data['destino'] ?? 'Destino',
            'estimated_delivery' => $data['estimated_delivery'] ?? $data['entrega_estimada'] ?? null,
            'progress' => $this->progressFromStatus($status),
            'history' => $history,
        ];
    }

    private function normalizeStatus(string $value): string
    {
        $v = strtolower(trim($value));
        $map = [
            'registrado' => self::STATUS_REGISTRADO,
            'en camino' => self::STATUS_EN_CAMINO,
            'en_camino' => self::STATUS_EN_CAMINO,
            'en almacen' => self::STATUS_EN_ALMACEN,
            'en_almacen' => self::STATUS_EN_ALMACEN,
            'almacen' => self::STATUS_EN_ALMACEN,
            'recogido' => self::STATUS_RECOGIDO,
            'entregado' => self::STATUS_RECOGIDO,
        ];

        return $map[$v] ?? self::STATUS_REGISTRADO;
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_REGISTRADO => 'Registrado',
            self::STATUS_EN_CAMINO => 'En camino',
            self::STATUS_EN_ALMACEN => 'En almacén',
            self::STATUS_RECOGIDO => 'Recogido',
            default => 'Registrado',
        };
    }

    private function progressFromStatus(string $status): int
    {
        return match ($status) {
            self::STATUS_REGISTRADO => 15,
            self::STATUS_EN_CAMINO => 50,
            self::STATUS_EN_ALMACEN => 85,
            self::STATUS_RECOGIDO => 100,
            default => 0,
        };
    }

    /**
     * Datos ficticios para pruebas cuando no hay URL configurada o falla el servicio externo.
     */
    private function mockData(string $code): array
    {
        $codeUpper = strtoupper($code);
        if ($codeUpper === '') {
            $codeUpper = 'BB-2024-DEMO';
        }

        $status = self::STATUS_EN_CAMINO;
        $history = [
            ['date' => '24 Feb, 08:00', 'location' => 'Lima - Oficina Central', 'desc' => 'Envío registrado y aceptado'],
            ['date' => '24 Feb, 14:30', 'location' => 'Centro de Consolidación - Huachipa', 'desc' => 'Paquete en camino al almacén'],
            ['date' => '25 Feb, 09:15', 'location' => 'Centro Distribución - Huachipa', 'desc' => 'En ruta hacia destino final'],
        ];

        if (str_ends_with($codeUpper, '-R')) {
            $status = self::STATUS_RECOGIDO;
            $history[] = ['date' => '25 Feb, 11:00', 'location' => 'Arequipa - Sede Central', 'desc' => 'Envío recogido por el destinatario'];
        } elseif (str_ends_with($codeUpper, '-A')) {
            $status = self::STATUS_EN_ALMACEN;
            $history[] = ['date' => '25 Feb, 08:00', 'location' => 'Almacén Arequipa', 'desc' => 'Paquete en almacén, listo para recoger'];
        } elseif (str_ends_with($codeUpper, '-0')) {
            $status = self::STATUS_REGISTRADO;
            $history = [
                ['date' => date('d M, H:i', strtotime('-1 hour')), 'location' => 'Lima', 'desc' => 'Envío registrado en sistema'],
            ];
        }

        return [
            'code' => $codeUpper,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'current_location' => $status === self::STATUS_EN_CAMINO ? 'Centro Distribución - Huachipa, Lima' : ($status === self::STATUS_EN_ALMACEN ? 'Almacén Arequipa' : ($status === self::STATUS_RECOGIDO ? 'Entregado' : 'Oficina Lima')),
            'origin' => 'Puerto del Callao, Lima',
            'destination' => 'Sede Central Arequipa',
            'estimated_delivery' => $status === self::STATUS_RECOGIDO ? null : 'Mañana, 09:00 AM',
            'progress' => $this->progressFromStatus($status),
            'history' => $history,
        ];
    }
}
