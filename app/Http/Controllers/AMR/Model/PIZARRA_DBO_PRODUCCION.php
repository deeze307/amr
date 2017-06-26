<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class PIZARRA_DBO_PRODUCCION extends Model
{
    protected $connection = "pizarra";
    protected $table = "dbo.produccion";
    public $timestamps = false;

}