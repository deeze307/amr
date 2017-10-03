<?php

namespace IAServer\Http\Controllers\AMR\view;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class Partials extends Controller
{
    public static function getPartialsOnInterface()
    {
        $fechaDesde = Carbon::yesterday();
        Log::debug('fecha_desde '.$fechaDesde);
        $pedidosParciales = XXE_WMS_COGISCAN_PEDIDOS::where('STATUS','ERROR')
            ->where('LAST_UPDATE_DATE','>',$fechaDesde)->get();
        return $pedidosParciales;
    }
}
