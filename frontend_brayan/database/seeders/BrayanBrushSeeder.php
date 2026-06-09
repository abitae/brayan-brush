<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\CalculatorCity;
use App\Models\PricingRoute;
use App\Models\ProhibitedCategory;
use App\Models\Service;
use App\Models\SiteConfig;
use Illuminate\Database\Seeder;

class BrayanBrushSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::count() === 0) {
            Service::insert([
                ['title' => 'Courier Nacional', 'description' => 'Envíos rápidos de sobres y paquetería menor a 10kg.', 'icon_type' => 'Box', 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Mudanza Corporativa', 'description' => 'Traslados integrales con embalaje profesional.', 'icon_type' => 'Home', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Carga Pesada', 'description' => 'Logística B2B para envíos de gran tonelaje.', 'icon_type' => 'Package', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ]);

            $cat1 = ProhibitedCategory::create(['title' => 'Materiales Peligrosos', 'sort_order' => 0]);
            foreach (['Explosivos y materiales inflamables', 'Sustancias tóxicas o corrosivas', 'Gases comprimidos'] as $i => $label) {
                $cat1->items()->create(['label' => $label, 'sort_order' => $i]);
            }

            $cat2 = ProhibitedCategory::create(['title' => 'Artículos Ilegales', 'sort_order' => 1]);
            foreach (['Drogas y estupefacientes', 'Armas y municiones', 'Mercancía de contrabando'] as $i => $label) {
                $cat2->items()->create(['label' => $label, 'sort_order' => $i]);
            }

            $cat3 = ProhibitedCategory::create(['title' => 'Restricciones Especiales', 'sort_order' => 2]);
            foreach (['Dinero en efectivo', 'Joyas y valores', 'Documentos confidenciales'] as $i => $label) {
                $cat3->items()->create(['label' => $label, 'sort_order' => $i]);
            }
        }

        if (CalculatorCity::count() === 0) {
            $cities = [
                ['name' => 'Lima', 'can_origin' => true, 'can_destination' => true, 'sort_order' => 0],
                ['name' => 'Callao', 'can_origin' => true, 'can_destination' => false, 'sort_order' => 1],
                ['name' => 'Trujillo', 'can_origin' => true, 'can_destination' => true, 'sort_order' => 2],
                ['name' => 'Arequipa', 'can_origin' => false, 'can_destination' => true, 'sort_order' => 3],
                ['name' => 'Cusco', 'can_origin' => false, 'can_destination' => true, 'sort_order' => 4],
                ['name' => 'Piura', 'can_origin' => false, 'can_destination' => true, 'sort_order' => 5],
            ];

            foreach ($cities as $city) {
                CalculatorCity::create([...$city, 'is_active' => true]);
            }

            SiteConfig::default()->update([
                'calculator_default_origin' => 'Lima',
                'calculator_default_destination' => 'Arequipa',
            ]);
        }

        if (Agency::count() === 0) {
            $agencies = [
                ['name' => 'Sede Central Lima', 'address' => 'Av. Javier Prado Este 1234, San Isidro', 'city' => 'Lima', 'phone' => '+51 1 700 1234', 'lat' => -12.0917, 'lng' => -77.027],
                ['name' => 'Centro Logístico Callao', 'address' => 'Av. Argentina 4500', 'city' => 'Callao', 'phone' => '+51 1 700 5678', 'lat' => -12.0433, 'lng' => -77.1],
                ['name' => 'Agencia Arequipa', 'address' => 'Av. Parra 102', 'city' => 'Arequipa', 'phone' => '+51 54 203040', 'lat' => -16.409, 'lng' => -71.5375],
                ['name' => 'Sede Norte Trujillo', 'address' => 'Av. Larco 880', 'city' => 'Trujillo', 'phone' => '+51 44 304050', 'lat' => -8.116, 'lng' => -79.03],
            ];
            foreach ($agencies as $i => $row) {
                Agency::create([...$row, 'sort_order' => $i, 'is_active' => true]);
            }
        }

        if (PricingRoute::count() > 0) {
            return;
        }

        $now = now();
        $routes = [
            ['origin' => 'Lima', 'destination' => 'Arequipa', 'base_fee' => 25, 'included_kg' => 5, 'price_per_kg' => 1.4, 'volumetric_factor' => 5000],
            ['origin' => 'Lima', 'destination' => 'Cusco', 'base_fee' => 28, 'included_kg' => 5, 'price_per_kg' => 1.5, 'volumetric_factor' => 5000],
            ['origin' => 'Lima', 'destination' => 'Piura', 'base_fee' => 22, 'included_kg' => 5, 'price_per_kg' => 1.3, 'volumetric_factor' => 5000],
            ['origin' => 'Lima', 'destination' => 'Trujillo', 'base_fee' => 20, 'included_kg' => 5, 'price_per_kg' => 1.2, 'volumetric_factor' => 5000],
            ['origin' => 'Callao', 'destination' => 'Arequipa', 'base_fee' => 25, 'included_kg' => 5, 'price_per_kg' => 1.4, 'volumetric_factor' => 5000],
            ['origin' => 'Callao', 'destination' => 'Cusco', 'base_fee' => 28, 'included_kg' => 5, 'price_per_kg' => 1.5, 'volumetric_factor' => 5000],
            ['origin' => 'Trujillo', 'destination' => 'Lima', 'base_fee' => 20, 'included_kg' => 5, 'price_per_kg' => 1.2, 'volumetric_factor' => 5000],
            ['origin' => 'Trujillo', 'destination' => 'Arequipa', 'base_fee' => 24, 'included_kg' => 5, 'price_per_kg' => 1.35, 'volumetric_factor' => 5000],
        ];

        foreach ($routes as $route) {
            PricingRoute::create([...$route, 'created_at' => $now, 'updated_at' => $now]);
        }
    }
}
