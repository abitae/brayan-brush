<?php

namespace App\Models\Caja;

use App\Models\Configuration\TipoExitCaja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitCaja extends Model
{
    use HasFactory;
    protected $fillable = [
        'caja_id',
        'monto_exit',
        'description',
        'metodo_pago',
        'tipo_exit_id',
    ];
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
    public function tipoExit()
    {
        return $this->belongsTo(TipoExitCaja::class);
    }
}
