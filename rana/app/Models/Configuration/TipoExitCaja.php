<?php

namespace App\Models\Configuration;

use App\Models\Caja\ExitCaja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoExitCaja extends Model
{
    /** @use HasFactory<\Database\Factories\Configuration\TipoExitCajaFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'is_active',
    ];
    public function exits()
    {
        return $this->hasMany(ExitCaja::class);
    }
}
