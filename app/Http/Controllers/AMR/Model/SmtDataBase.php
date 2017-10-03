<?php

namespace IAServer\Http\Controllers\AMR\Model;

use Illuminate\Database\Eloquent\Model;

class SmtDataBase extends Model
{
    protected $connection = "smtdatabase";
    protected $table = "orden_trabajo";
    public $timestamps = false;
}