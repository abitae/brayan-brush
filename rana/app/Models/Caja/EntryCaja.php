<?php

namespace App\Models\Caja;

use App\Models\Configuration\TipoEntryCaja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryCaja extends Model
{
    use HasFactory;
    protected $fillable = [
        'caja_id',
        'monto_entry',
        'description',
        'metodo_pago',
        'tipo_entry_id',
    ];
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
    public function tipoEntry()
    {
        return $this->belongsTo(TipoEntryCaja::class);
    }
}
