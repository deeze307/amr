<?php

namespace IAServer\Http\Controllers\PedidoXPull\Model;

use Illuminate\Database\Eloquent\Model;

class pedido extends Model
{
    protected $connection = 'pizarra';
    protected $table = 'pedido';

    public $timestamps = false;

}
