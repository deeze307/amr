<?php

namespace IAServer\Http\Controllers\AMR\view;

use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class Traceability extends Controller
{
    //
    public static function index()
    {
        return view('amr.trazabilidad.index');
    }

    public static  function getTraceability($item, Request $request)
    {
        if($item == 'lpn')
        {
            //Muestro todos los registros de deltaMonitor, pedidos en materialRequest

        }
        else
        {
            // Para Part# muestro los últimos 5 pedidos de materialRequest con sus respectivos LPN's en la interfáz

        }
        //obtengo datos

    }
}
