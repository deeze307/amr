<?php

namespace IAServer\Http\Controllers\IAServer\Model;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $connection = 'amr_prod';
    protected $table = 'cgs_interface_db.menu';
    public $timestamps = false;
}
