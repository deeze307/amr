<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class CgsLanes extends Model
{
    protected $connection = "amr_prod";
    protected $table = "cgs_lanes";
    public $timestamps = false;

}