<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    public const STATUS_PENDING = 'pendiente';

    public const STATUS_IN_PROGRESS = 'en_proceso';

    public const STATUS_RESOLVED = 'resuelto';

    protected $fillable = [
        'code',
        'nombre',
        'documento',
        'telefono',
        'email',
        'direccion',
        'tipo',
        'detalle',
        'status',
        'admin_notes',
    ];

    public static function generateCode(int $id): string
    {
        return 'LR-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}
