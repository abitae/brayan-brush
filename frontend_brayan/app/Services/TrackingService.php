<?php

namespace App\Services;

use App\Models\SiteConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingService
{
    /** Estados posibles del seguimiento (system_brayan: REGISTRADO, ENVIADO, RECIBIDO, RETORNADO, ENTREGADO) */
    public const STATUS_REGISTRADO = 'registrado';

    public const STATUS_ENVIADO = 'enviado';

    public const STATUS_RECIBIDO = 'recibido';

    public const STATUS_RETORNADO = 'retornado';

    public const STATUS_ENTREGADO = 'entregado';

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
     * Llama al endpoint externo. Usa ?code= (ej. system_brayan: /api/frontend/tracking?code=ENC-...).
     * Acepta respuestas en formato genérico o system_brayan (estado_encomienda, lugar_origen, lugar_destino, fechas).
     */
    private function callExternalApi(string $baseUrl, string $code): ?array
    {
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        $url = rtrim($baseUrl, '/').$sep.'code='.urlencode($code);

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
     * Soporta formato system_brayan (estado_encomienda, lugar_origen, lugar_destino, fecha_*) o genérico.
     */
    private function normalizeExternalResponse(string $code, array $data): array
    {
        $isSystemBrayan = isset($data['estado_encomienda']) || array_key_exists('lugar_origen', $data);

        if ($isSystemBrayan) {
            return $this->normalizeSystemBrayanResponse($data, $code);
        }

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

    /**
     * Normaliza respuesta de system_brayan: estado_encomienda, lugar_origen, lugar_destino, fecha_*.
     */
    private function normalizeSystemBrayanResponse(array $data, string $code): array
    {
        $status = $this->normalizeStatus($data['estado_encomienda'] ?? self::STATUS_REGISTRADO);
        $origin = $data['lugar_origen'] ?? '';
        $destination = $data['lugar_destino'] ?? '';
        $history = $this->buildHistoryFromFechas($data);

        $currentLocation = match ($status) {
            self::STATUS_REGISTRADO => $origin,
            self::STATUS_ENVIADO => 'En tránsito',
            self::STATUS_RECIBIDO => $destination,
            self::STATUS_RETORNADO => 'En retorno',
            self::STATUS_ENTREGADO => ! empty($data['direccion_envio']) ? $data['direccion_envio'] : $destination,
            default => $origin,
        };

        return [
            'code' => $data['code'] ?? $code,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'current_location' => $currentLocation ?: null,
            'origin' => $origin ?: '—',
            'destination' => $destination ?: '—',
            'estimated_delivery' => null,
            'progress' => $this->progressFromStatus($status),
            'history' => $history,
        ];
    }

    private function buildHistoryFromFechas(array $data): array
    {
        $history = [];
        $formatDate = function ($value) {
            if ($value === null || $value === '') {
                return '';
            }
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return date('d M, H:i', strtotime($value));
            }
            return (string) $value;
        };

        if (! empty($data['fecha_creacion'])) {
            $history[] = ['date' => $formatDate($data['fecha_creacion']), 'location' => $data['lugar_origen'] ?? 'Origen', 'desc' => 'Envío registrado'];
        }
        if (! empty($data['fecha_envio'])) {
            $history[] = ['date' => $formatDate($data['fecha_envio']), 'location' => 'En tránsito', 'desc' => 'Enviado'];
        }
        if (! empty($data['fecha_recepcion'])) {
            $history[] = ['date' => $formatDate($data['fecha_recepcion']), 'location' => $data['lugar_destino'] ?? 'Destino', 'desc' => 'Recibido en sucursal destino'];
        }
        if (! empty($data['fecha_entrega'])) {
            $history[] = ['date' => $formatDate($data['fecha_entrega']), 'location' => $data['direccion_envio'] ?? $data['lugar_destino'] ?? 'Destino', 'desc' => 'Entregado'];
        }
        if (! empty($data['fecha_retorno'])) {
            $history[] = ['date' => $formatDate($data['fecha_retorno']), 'location' => 'Retorno', 'desc' => 'Encomienda retornada'];
        }

        return $history;
    }

    private function normalizeStatus(string $value): string
    {
        $v = strtoupper(trim($value));
        $map = [
            'REGISTRADO' => self::STATUS_REGISTRADO,
            'ENVIADO' => self::STATUS_ENVIADO,
            'RECIBIDO' => self::STATUS_RECIBIDO,
            'RETORNADO' => self::STATUS_RETORNADO,
            'ENTREGADO' => self::STATUS_ENTREGADO,
        ];

        return $map[$v] ?? self::STATUS_REGISTRADO;
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_REGISTRADO => 'Registrado',
            self::STATUS_ENVIADO => 'Enviado',
            self::STATUS_RECIBIDO => 'Recibido',
            self::STATUS_RETORNADO => 'Retornado',
            self::STATUS_ENTREGADO => 'Entregado',
            default => 'Registrado',
        };
    }

    private function progressFromStatus(string $status): int
    {
        return match ($status) {
            self::STATUS_REGISTRADO => 20,
            self::STATUS_ENVIADO => 40,
            self::STATUS_RECIBIDO => 60,
            self::STATUS_RETORNADO => 80,
            self::STATUS_ENTREGADO => 100,
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

        $status = self::STATUS_ENVIADO;
        $history = [
            ['date' => '24 Feb, 08:00', 'location' => 'Lima - Oficina Central', 'desc' => 'Envío registrado'],
            ['date' => '24 Feb, 14:30', 'location' => 'En tránsito', 'desc' => 'Enviado'],
        ];

        if (str_ends_with($codeUpper, '-0')) {
            $status = self::STATUS_REGISTRADO;
            $history = [['date' => date('d M, H:i', strtotime('-1 hour')), 'location' => 'Lima', 'desc' => 'Envío registrado en sistema']];
        } elseif (str_ends_with($codeUpper, '-R')) {
            $status = self::STATUS_ENTREGADO;
            $history[] = ['date' => '25 Feb, 11:00', 'location' => 'Arequipa', 'desc' => 'Entregado'];
        }

        return [
            'code' => $codeUpper,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'current_location' => $status === self::STATUS_ENTREGADO ? 'Entregado' : ($status === self::STATUS_ENVIADO ? 'En tránsito' : 'Oficina Lima'),
            'origin' => 'Puerto del Callao, Lima',
            'destination' => 'Sede Central Arequipa',
            'estimated_delivery' => $status === self::STATUS_ENTREGADO ? null : 'Mañana, 09:00 AM',
            'progress' => $this->progressFromStatus($status),
            'history' => $history,
        ];
    }
}
