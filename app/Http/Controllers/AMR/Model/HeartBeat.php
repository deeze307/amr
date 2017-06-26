<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class HeartBeat extends Model
{
    protected $connection = "amr_prod";
    protected $table = "amr_heartbeat";
    public $timestamps = false;

}