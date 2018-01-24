<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class Reservas extends Model
{
    protected $connection = "amr_prod";
    protected $table = "reservas";
    public $timestamps = false;

}