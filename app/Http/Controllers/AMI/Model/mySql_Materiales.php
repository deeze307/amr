<?php

namespace IAServer\Http\Controllers\AMI\Model;

use Illuminate\Database\Eloquent\Model;

class mySql_Materiales extends Model
{
    protected $connection = "amr_prod";
    protected $table = "cgs_init_material";
    public $timestamps = false;

}