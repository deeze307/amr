<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest_DESA extends Model
{
    protected $connection = "amr_prod_desa";
    protected $table = "cgs_materialrequest";
    public $timestamps = false;

}