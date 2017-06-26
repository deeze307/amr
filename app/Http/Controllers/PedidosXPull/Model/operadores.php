<?php

namespace IAServer\Http\Controllers\PedidoXPull\Model;

use Illuminate\Database\Eloquent\Model;

class operadores extends Model
{
    protected $connection = 'pizarra';
    protected $table = 'operadores';

    public $timestamps = false;

}
