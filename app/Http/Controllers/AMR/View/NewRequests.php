<?php

namespace IAServer\Http\Controllers\AMR\view;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\CrudAmr;
use IAServer\Http\Controllers\AMR\Model\CgsLanes;
use IAServer\Http\Controllers\AMR\Model\MaterialRequest;
use IAServer\Http\Controllers\AMR\Model\MaterialRequest_DESA;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS_DESA;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class NewRequests extends Controller
{
    public static function index()
    {
        return view('amr.pedidosNuevos.index');
    }

    public static function getNewRequestsOnInterface()
    {
        $pedidosNuevos = XXE_WMS_COGISCAN_PEDIDOS::where('STATUS','NEW')
                                                ->orderBy('CREATION_DATE','DESC')->get();
        return $pedidosNuevos;
    }

    public static function getRequestsLanes()
    {
        $lanes = CgsLanes::all();
        return $lanes;
    }

    public static function store(Request $request)
    {
        $fecha = Carbon::now();

        $opSeq = CrudAmr::getOperationSeq($request->input('op_number'));
        $operationSeq = $opSeq->OPERATION_SEQ_NUM;

        $newInsert = new MaterialRequest();
//        $newInsert = new MaterialRequest_DESA();
        $newInsert->op = strtoupper($request->input('op_number'));
        $newInsert->linMatWip = $operationSeq;
        $newInsert->rawMaterial = "M".$fecha;
        $newInsert->codMat = strtoupper($request->input('item_code'));
        $newInsert->uniMedMat = "EA";
        $newInsert->cantASolic = strtoupper($request->input('quantity'));
        $newInsert->cantTareas = "0";
        $newInsert->cantTransfer = "0";
        $newInsert->estadoLinea = "0";
        $newInsert->linDest = "0";
        $newInsert->ubicacionOrigen = "5";
        $newInsert->status = "127";
        $newInsert->delta = "0";
        $newInsert->insertId = "0";
        $newInsert->PROD_LINE = strtoupper($request->input('prod_line'));
        $newInsert->MAQUINA = strtoupper($request->input('maquina'));
        $newInsert->UBICACION = strtoupper($request->input('ubicacion'));
        $newInsert->save();


        $newid = $newInsert->id;


        $newrequest = new XXE_WMS_COGISCAN_PEDIDOS();
//        $newrequest = new XXE_WMS_COGISCAN_PEDIDOS_DESA();

        $newrequest->OP_NUMBER = strtoupper($request->input('op_number'));
        $newrequest->ORGANIZATION_CODE = "UP3";
        $newrequest->OPERATION_SEQ = $operationSeq;
        $newrequest->ITEM_CODE = strtoupper($request->input('item_code'));
        $newrequest->ITEM_UOM_CODE = "EA";
        $newrequest->QUANTITY = strtoupper($request->input('quantity'));
        $newrequest->PROD_LINE = strtoupper($request->input('prod_line'));
        $newrequest->MAQUINA = strtoupper($request->input('maquina'));
        $newrequest->UBICACION = strtoupper($request->input('ubicacion'));
        $newrequest->CREATION_DATE = $fecha;
        $newrequest->STATUS = "NEW";
        $newrequest->INSERT_ID = $newid;

        $newrequest->save();

        return redirect('amr/pedidos/nuevos');
    }
}
