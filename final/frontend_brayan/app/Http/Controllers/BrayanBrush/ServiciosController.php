<?php

namespace App\Http\Controllers\BrayanBrush;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiciosController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('brayan-brush/servicios', [
            'services' => Service::listForFront()->values()->all(),
        ]);
    }
}
