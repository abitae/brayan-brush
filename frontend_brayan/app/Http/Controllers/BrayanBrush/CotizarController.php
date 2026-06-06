<?php

namespace App\Http\Controllers\BrayanBrush;

use App\Http\Controllers\Controller;
use App\Models\CalculatorCity;
use App\Models\SiteConfig;
use Inertia\Inertia;
use Inertia\Response;

class CotizarController extends Controller
{
    public function __invoke(): Response
    {
        $config = SiteConfig::default();
        $cities = CalculatorCity::listForFront()->values()->all();
        $origins = collect($cities)->where('can_origin', true)->pluck('name')->values()->all();
        $destinations = collect($cities)->where('can_destination', true)->pluck('name')->values()->all();

        $defaultOrigin = $config->calculator_default_origin;
        if (! $defaultOrigin || ! in_array($defaultOrigin, $origins, true)) {
            $defaultOrigin = $origins[0] ?? 'Lima';
        }

        $defaultDestination = $config->calculator_default_destination;
        if (! $defaultDestination || ! in_array($defaultDestination, $destinations, true)) {
            $defaultDestination = $destinations[0] ?? 'Arequipa';
        }

        return Inertia::render('brayan-brush/cotizar', [
            'calculatorDefaults' => [
                'default_mode' => $config->calculator_default_mode ?? 'weight',
                'default_weight' => $config->calculator_default_weight !== null ? (int) $config->calculator_default_weight : 5,
                'default_length' => $config->calculator_default_length !== null ? (int) $config->calculator_default_length : 30,
                'default_width' => $config->calculator_default_width !== null ? (int) $config->calculator_default_width : 30,
                'default_height' => $config->calculator_default_height !== null ? (int) $config->calculator_default_height : 30,
                'default_origin' => $defaultOrigin,
                'default_destination' => $defaultDestination,
                'base_fee' => $config->calculator_base_fee !== null ? (float) $config->calculator_base_fee : 25,
                'included_kg' => $config->calculator_included_kg !== null ? (int) $config->calculator_included_kg : 5,
                'excess_price_per_kg' => $config->calculator_excess_price_per_kg !== null ? (float) $config->calculator_excess_price_per_kg : 1.4,
                'express_multiplier' => $config->calculator_express_multiplier !== null ? (float) $config->calculator_express_multiplier : 1.5,
            ],
            'calculatorCities' => $cities,
        ]);
    }
}
