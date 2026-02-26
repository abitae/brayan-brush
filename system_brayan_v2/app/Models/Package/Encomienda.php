<?php

namespace App\Models\Package;

use App\Models\Configuration\Sucursal;
use App\Models\Configuration\Transportista;
use App\Models\Configuration\Vehiculo;
use App\Models\Facturacion\Despatche;
use App\Models\Facturacion\Invoice;
use App\Models\Facturacion\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encomienda extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', //codigo de la encomienda
        'user_id', //usuario que crea la encomienda
        'transportista_id', //transportista
        'vehiculo_id', //vehiculo
        'customer_id', //remitente
        'sucursal_id', //sucursal remitente
        'customer_dest_id', //destinatario
        'sucursal_dest_id', //sucursal destinatario
        'customer_fact_id', //facturacion
        'cantidad', //cantidad de paquetes
        'monto', //monto total
        'monto_descuento', //monto de descuento
        'motivo_descuento', //motivo de descuento

        'doc_ticket', //documento de traslado
        'doc_guia', //documento de guia
        'doc_factura', //documento de factura
        //Fechas
        'fecha_creacion', //fecha de creacion
        'fecha_envio', //fecha de envio
        'fecha_recepcion', //fecha de recepcion
        'fecha_entrega', //fecha de entrega
        'fecha_retorno', //fecha de retorno

        'estado_pago', //ENVIO PAGADO , CONTRA ENTREGA
        'tipo_pago', // Contado, Credito
        'metodo_pago', // Efectivo, Yape, Tarjeta, Cheque, Transferencia, Otro
        'tipo_comprobante', // TICKET, FACTURA, BOLETA

        'estado_credito', // Pendiente, Cancelado
        'docsTraslado', //json de documentos de traslado
        'glosa', //glosa de la encomienda
        'observation', //observacion de la encomienda
        'estado_encomienda', //REGISTRADO, ENVIADO,RECIBIDO,RETORNADO, ENTREGADO
        'pin', //pin de la encomienda
        'isTransbordo', //si es transbordo
        'isHome', //si es home
        'direccion_envio', //direccion de envio
        'isReturn', //si es retorno
        'isActive', //si esta activa
        'ruta_id', //id de la ruta
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_envio' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_retorno' => 'datetime',
        'isTransbordo' => 'boolean',
        'isHome' => 'boolean',
        'isReturn' => 'boolean',
        'isActive' => 'boolean',
    ];

    public function ruta()
    {
        return $this->belongsTo(RutaSucursal::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function transportista()
    {
        return $this->belongsTo(Transportista::class);
    }
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
    public function remitente()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function sucursal_remitente()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'id');
    }
    public function destinatario()
    {
        return $this->belongsTo(Customer::class, 'customer_dest_id', 'id');
    }
    public function facturacion()
    {
        return $this->belongsTo(Customer::class, 'customer_fact_id', 'id');
    }
    public function sucursal_destinatario()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_dest_id', 'id');
    }
    public function paquetes()
    {
        return $this->hasMany(Paquete::class);
    }
    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function despatche()
    {
        return $this->hasOne(Despatche::class);
    }
}
