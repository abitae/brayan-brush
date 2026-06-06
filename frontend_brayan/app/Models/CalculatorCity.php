<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CalculatorCity extends Model
{
    protected $fillable = [
        'name',
        'can_origin',
        'can_destination',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'can_origin' => 'boolean',
        'can_destination' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @return Collection<int, array{id: int, name: string, can_origin: bool, can_destination: bool}>
     */
    public static function listForFront(): Collection
    {
        return self::active()->ordered()->get()->map(fn (self $city) => [
            'id' => $city->id,
            'name' => $city->name,
            'can_origin' => (bool) $city->can_origin,
            'can_destination' => (bool) $city->can_destination,
        ]);
    }

    /**
     * @return Collection<int, array{id: int, name: string, can_origin: bool, can_destination: bool, is_active: bool, sort_order: int}>
     */
    public static function listForAdmin(): Collection
    {
        return self::ordered()->get()->map(fn (self $city) => [
            'id' => $city->id,
            'name' => $city->name,
            'can_origin' => (bool) $city->can_origin,
            'can_destination' => (bool) $city->can_destination,
            'is_active' => (bool) $city->is_active,
            'sort_order' => (int) $city->sort_order,
        ]);
    }
}
