<?php

namespace IAServer\Http\Controllers\CONFIG;

use IAServer\Http\Controllers\CONFIG\Model\cgs_config;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

class configController extends Controller
{
    public static function config()
    {
        return view('config.index');
    }

    public static function getConfigVal($type)
    {
        $config = cgs_config::WHERE('config_item',$type)->orderBy('value','ASC')->get();
        return $config;
    }

    public static function setConfigVal($field,$type,$valueToModify,$value)
    {
        try
        {
            $setConfig = cgs_config::WHERE($field,$type)->first();
            $setConfig->$valueToModify= $value;
            $setConfig->save();
            $estado = 'OK';
        }
        catch(Exception $ex)
        {
            $estado = 'NG';
        }
        return $estado;
    }


}
