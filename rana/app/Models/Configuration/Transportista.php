<?php

namespace App\Models\Configuration;

use App\Models\Package\Encomienda;
use App\Models\Package\RutaSucursal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    use HasFactory;
    protected $fillable = [
        'type_code',
        'licencia',
        'dni',
        'name',
        'tipo',
        'isActive',
    ];
    public function encomiendas()
    {
        return $this->hasMany(Encomienda::class);
    }
    public function sucursal_configurations()
    {
        return $this->hasMany(SucursalConfiguration::class);
    }
    public function rutas_sucursal()
    {
        return $this->hasMany(RutaSucursal::class);
    }
}
