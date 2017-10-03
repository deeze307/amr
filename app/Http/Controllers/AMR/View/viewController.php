<?php

namespace IAServer\Http\Controllers\AMR\view;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class viewController extends Controller
{

    public static function newIndex()
    {
        return view('amr.pedidosNuevos.index');
    }

    public static function processedIndex()
    {
        return view('amr.pedidosProcesados.index');
    }

    public static function partialsIndex()
    {
        return view('amr.pedidosParciales.index');
    }

    public static function traceIndex()
    {
        return view('amr.trazabilidad.index');
    }

    // ******************* //
    // ****  Reservas **** //
    // ******************* //

    public static function reservedIndex()
    {
        return view('amr.reservas.index');
    }

    public static function transitIndex()
    {
        return view('amr.reservas.transito.index');
    }

    public static function warehouseIndex()
    {
        return view('amr.reservas.almacenIA.index');
    }

    // ******************* //
    // ******************* //
    // ******************* //

}
