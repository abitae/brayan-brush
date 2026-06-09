<?php

namespace App\Http\Controllers\BrayanBrush;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiciosController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('brayan-brush/servicios', [
            'services' => Service::listForFront()->values()->all(),
        ]);
    }

    public function show(Service $service): Response
    {
        return Inertia::render('brayan-brush/servicio-detalle', [
            'service' => Service::listForFront()->firstWhere('id', (string) $service->id),
            'otherServices' => Service::listForFront()
                ->filter(fn (array $s) => $s['id'] !== (string) $service->id)
                ->take(3)
                ->values()
                ->all(),
        ]);
    }
}
