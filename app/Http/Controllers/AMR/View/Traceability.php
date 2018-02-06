<?php

namespace IAServer\Http\Controllers\AMR\view;

use IAServer\Http\Controllers\AMR\CrudAmr;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class Traceability extends CrudAmr
{
    //
    public static function index()
    {
        return view('amr.trazabilidad.index');
    }

    public static function getTraceability(Request $request)
    {
        $material = $request->material;
        $tipo_busqueda = $request->tipo_busqueda;
        $datos=[];

        //si el tipo de busqueda es por Código de Material
        if($tipo_busqueda == 'pn')
        {
            // Para Part# muestro los últimos 5 pedidos de materialRequest con sus respectivos LPN's en la interfáz
            $materialRequest=self::getMaterialRequestForTrace($material,$tipo_busqueda);
            $ebs=[];
            if(count($materialRequest) > 0)
            {
                $datos = compact('materialRequest');
                foreach($materialRequest as $mat)
                {
                    // Si el pedido se hizo a Abastecimiento por AMR o pedido Manual
                    if($mat->ubicacionOrigen == 2 || $mat->ubicacionOrigen == 5)
                    {
                        $array = self::getOnInterfaceForTrace($mat->id);
                        foreach($array as $a)
                        {
                            array_push($ebs,$a);
                        }
                    }
                }
                if(count($ebs) > 0)
                {
                    $datos = compact('materialRequest','ebs');
                }
            }
            else
            {
                $noData="";
                $datos = compact('noData');
            }
        }
        //Si el tipo de busqueda es por LPN
        else
        {

            //Muestro todos los registros de deltaMonitor, pedidos en materialRequest
            $deltaMonitor = self::getDeltaMonitorForTrace($material,$tipo_busqueda);
            if(count($deltaMonitor) > 0)
            {
                $datos = compact('deltaMonitor');
            }
            $materialRequest=self::getMaterialRequestForTrace($material,$tipo_busqueda);
            $ebs=[];
            if(count($materialRequest) > 0)
            {
                $datos = compact('deltaMonitor','materialRequest');
                foreach($materialRequest as $mat)
                {
                    // Si el pedido se hizo a Abastecimiento por AMR o pedido Manual
                    if($mat->ubicacionOrigen == 2 || $mat->ubicacionOrigen == 5)
                    {
                        $array = self::getOnInterfaceForTrace($mat->id);
                        foreach($array as $a)
                        {
                            array_push($ebs,$a);
                        }
                    }
                }
                if(count($ebs) > 0)
                {
                    $datos = compact('deltaMonitor','materialRequest','ebs');
                }
            }
            else
            {
                $noData="";
                if(isset($datos["deltaMonitor"]))
                {
                    $datos = compact('deltaMonitor','noData');
                }
                else
                {
                    $datos = compact('noData');
                }
            }
        }

        return view('amr.trazabilidad.index',['datos'=>$datos]);
    }
}
