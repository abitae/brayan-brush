<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Agency extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'lat',
        'lng',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return Collection<int, array{id: string, name: string, address: string, city: string, phone: string, lat: float, lng: float}>
     */
    public static function listForFront(): Collection
    {
        return self::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (self $a) => [
                'id' => (string) $a->id,
                'name' => $a->name,
                'address' => $a->address,
                'city' => $a->city,
                'phone' => $a->phone,
                'lat' => (float) $a->lat,
                'lng' => (float) $a->lng,
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function listForAdmin(): Collection
    {
        return self::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (self $a) => [
                'id' => $a->id,
                'name' => $a->name,
                'address' => $a->address,
                'city' => $a->city,
                'phone' => $a->phone,
                'lat' => (float) $a->lat,
                'lng' => (float) $a->lng,
                'sort_order' => (int) $a->sort_order,
                'is_active' => (bool) $a->is_active,
            ]);
    }
}
