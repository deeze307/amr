<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $connection = "amr_prod";
    protected $table = "cgs_materialrequest";
    public $timestamps = false;

}