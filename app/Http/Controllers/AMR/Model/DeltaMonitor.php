<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class DeltaMonitor extends Model
{
    protected $connection = "amr_prod";
    protected $table = "amr_deltamonitor";
    public $timestamps = false;

}