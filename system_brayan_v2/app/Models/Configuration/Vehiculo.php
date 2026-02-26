<?php

namespace App\Models\Configuration;

use App\Models\Package\Encomienda;
use App\Models\Package\RutaSucursal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'marca',
        'modelo',
        'tipo',
        'color',
        'largo',
        'ancho',
        'alto',
        'pesoBruto',
        'pesoNeto',
        'mtc',
        'placa',
        'nroCirculacion',
        'codEmisor',
        'nroAutorizacion',
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
