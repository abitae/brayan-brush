<?php

namespace App\Services;

use App\Exceptions\TrackingNotFoundException;
use App\Exceptions\TrackingServerException;
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
     * Consulta el estado de un envío por código y documento contra system_brayan_v1.
     *
     * @return array{code: string, status: string, status_label: string, current_location: string|null, origin: string, destination: string, estimated_delivery: string|null, progress: int, history: array<int, array{date: string, location: string, desc: string}>, is_home?: bool, delivery_address?: string|null, name_origen?: string, name_destino?: string}
     */
    public function track(string $code, string $document = ''): array
    {
        $code = trim($code);
        $document = trim($document);

        if ($document === '') {
            throw new TrackingServerException('El DNI o RUC es obligatorio para consultar el seguimiento.');
        }

        $url = $this->resolveTrackingApiUrl();
        if ($url === null) {
            throw new TrackingServerException('Servicio de rastreo no configurado. Contacta al administrador.');
        }

        $result = $this->callExternalApi($url, $code, $document);
        if ($result === null) {
            throw new TrackingServerException('Error al consultar el seguimiento. Intenta de nuevo.');
        }

        return $result;
    }

    private function resolveTrackingApiUrl(): ?string
    {
        $url = null;
        try {
            $config = SiteConfig::default();
            $url = $config->tracking_api_url ?? null;
        } catch (\Throwable) {
            // Tabla no disponible
        }

        if (! empty($url) && is_string($url)) {
            return rtrim($url, '/');
        }

        $envUrl = config('services.system_brayan.tracking_api_url');

        return is_string($envUrl) && $envUrl !== '' ? rtrim($envUrl, '/') : null;
    }

    /**
     * Llama al endpoint de system_brayan_v1: GET ?code=&document=
     */
    private function callExternalApi(string $baseUrl, string $code, string $document): ?array
    {
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        $url = $baseUrl.$sep
            .'code='.urlencode($code)
            .'&document='.urlencode($document);

        try {
            $response = Http::timeout(15)
                ->withOptions(['verify' => (bool) config('services.system_brayan.verify_ssl', true)])
                ->get($url);

            if (! $response->successful()) {
                $status = $response->status();
                Log::warning('Tracking API error', ['url' => $baseUrl, 'status' => $status]);
                if ($status === 404) {
                    throw new TrackingNotFoundException('Encomienda no encontrada.');
                }
                if ($status >= 500) {
                    throw new TrackingServerException('Error al consultar el seguimiento. Intenta de nuevo.');
                }

                return null;
            }

            $data = $response->json();
            if (! is_array($data)) {
                return null;
            }

            return $this->normalizeExternalResponse($code, $data);
        } catch (TrackingNotFoundException|TrackingServerException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Tracking API exception', ['url' => $baseUrl, 'message' => $e->getMessage()]);

            return null;
        }
    }

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
            'origin' => $data['origin'] ?? $data['origen'] ?? '—',
            'destination' => $data['destination'] ?? $data['destino'] ?? '—',
            'estimated_delivery' => $data['estimated_delivery'] ?? $data['entrega_estimada'] ?? null,
            'progress' => $this->progressFromStatus($status),
            'history' => $history,
        ];
    }

    private function normalizeSystemBrayanResponse(array $data, string $code): array
    {
        $status = $this->normalizeStatus($data['estado_encomienda'] ?? self::STATUS_REGISTRADO);
        $nameOrigen = trim((string) ($data['name_origen'] ?? ''));
        $nameDestino = trim((string) ($data['name_destino'] ?? ''));
        $lugarOrigen = trim((string) ($data['lugar_origen'] ?? ''));
        $lugarDestino = trim((string) ($data['lugar_destino'] ?? ''));
        $direccionEnvio = $data['direccion_envio'] ?? null;
        $isHome = ! empty($data['isHome']);

        $origin = $this->formatOriginDestination($nameOrigen, $lugarOrigen);
        $destination = $this->formatOriginDestination($nameDestino, $lugarDestino);
        $deliveryAddress = $isHome && $direccionEnvio ? (string) $direccionEnvio : null;

        $currentLocation = match ($status) {
            self::STATUS_REGISTRADO => $origin,
            self::STATUS_ENVIADO => 'En tránsito hacia '.$nameDestino,
            self::STATUS_RECIBIDO => $isHome && $deliveryAddress ? $deliveryAddress : $destination,
            self::STATUS_RETORNADO => 'En retorno a '.$nameOrigen,
            self::STATUS_ENTREGADO => $isHome && $deliveryAddress ? $deliveryAddress : $destination,
            default => $origin,
        };

        $history = $this->buildHistoryFromFechas($data);

        return [
            'code' => $data['code'] ?? $code,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'current_location' => $currentLocation ?: null,
            'origin' => $origin ?: '—',
            'destination' => $destination ?: '—',
            'name_origen' => $nameOrigen ?: null,
            'name_destino' => $nameDestino ?: null,
            'estimated_delivery' => null,
            'progress' => $this->progressFromStatus($status),
            'history' => $history,
            'is_home' => $isHome,
            'delivery_address' => $deliveryAddress,
        ];
    }

    private function formatOriginDestination(string $name, string $address): string
    {
        if ($name !== '' && $address !== '') {
            return $name.' – '.$address;
        }
        if ($address !== '') {
            return $address;
        }

        return $name ?: '—';
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

        $locOrigen = $this->formatOriginDestination($data['name_origen'] ?? '', $data['lugar_origen'] ?? '') ?: 'Origen';
        $locDestino = $this->formatOriginDestination($data['name_destino'] ?? '', $data['lugar_destino'] ?? '') ?: 'Destino';
        $locEntrega = ! empty($data['direccion_envio']) ? $data['direccion_envio'] : $locDestino;

        if (! empty($data['fecha_creacion'])) {
            $history[] = ['date' => $formatDate($data['fecha_creacion']), 'location' => $locOrigen, 'desc' => 'Envío registrado'];
        }
        if (! empty($data['fecha_envio'])) {
            $history[] = ['date' => $formatDate($data['fecha_envio']), 'location' => 'En tránsito', 'desc' => 'Enviado'];
        }
        if (! empty($data['fecha_recepcion'])) {
            $history[] = ['date' => $formatDate($data['fecha_recepcion']), 'location' => $locDestino, 'desc' => 'Recibido en sucursal destino'];
        }
        if (! empty($data['fecha_entrega'])) {
            $history[] = ['date' => $formatDate($data['fecha_entrega']), 'location' => $locEntrega, 'desc' => 'Entregado'];
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
}
