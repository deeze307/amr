<?php

namespace IAServer\Http\Controllers\AMR\view;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class Processed extends Controller
{
    public static function index()
    {
        return view('amr.pedidosProcesados.index');
    }

    public static function getProcessedOnInterface()
    {
        $fechaDesde = Carbon::yesterday();
        $pedidosProcesados = XXE_WMS_COGISCAN_PEDIDOS::select(DB::raw('CP.OP_NUMBER,CP.ITEM_CODE,CPL.LPN,CP.QUANTITY,CP.QUANTITY_ASSIGNED,CPL.QUANTITY_ASSIGNED AS LPN_QUANTITY,CP.PROD_LINE,CP.MAQUINA,CP.UBICACION,CP.STATUS,CP.CREATION_DATE,CP.LAST_UPDATE_DATE,CP.INSERT_ID'))
                                                    ->from('XXE_WMS_COGISCAN_PEDIDOS as CP')
                                                    ->leftjoin('XXE_WMS_COGISCAN_PEDIDO_LPNS as CPL','CP.LINEA_ID','=','CPL.LINEA_ID')
                                                    ->where('CP.STATUS','PROCESSED')
                                                    ->where('CP.CREATION_DATE','>',$fechaDesde)
                                                    ->orderBy('CP.CREATION_DATE','DESC')
                                                    ->get();
        return $pedidosProcesados;
    }
}
