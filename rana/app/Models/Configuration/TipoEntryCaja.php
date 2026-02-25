<?php

namespace App\Models\Configuration;

use App\Models\Caja\EntryCaja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEntryCaja extends Model
{
    /** @use HasFactory<\Database\Factories\Configuration\TipoEntryCajaFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'is_active',
    ];
    public function entries()
    {
        return $this->hasMany(EntryCaja::class);
    }
}
