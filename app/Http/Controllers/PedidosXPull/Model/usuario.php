<?php

namespace IAServer\Http\Controllers\PedidoXPull\Model;

use Illuminate\Database\Eloquent\Model;

class usuario extends Model
{
    protected $connection = 'pizarra';
    protected $table = 'usuario';

    public $timestamps = false;

}
