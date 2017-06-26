<?php

namespace IAServer\Http\Controllers\CONFIG\Model;

use Illuminate\Database\Eloquent\Model;

class CgsConfig extends Model
{
    protected $connection = "amr_prod";
    protected $table = "cgs_config";
    public $timestamps = false;
    protected $primaryKey = 'id_config';

}