@extends('adminlte/theme')
@section('title','AMR - Trazabilidad')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('body')
    <div class="col-lg-12">

    <div ng-controller="trazabilidadController">
        <h3>Trazabilidad de Materiales </h3>
        <p>Ustéd puede ingresar un <strong>Número de Parte</strong> o <strong>LPN</strong> para realizar la búsqueda</p>
        @include('amr.trazabilidad.partial.header')
        @if(isset($datos["noData"]))
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> Atención!</h4>
                No se han encontrado datos en la busqueda especificada.
            </div>
        @else
            @if(isset($datos["deltaMonitor"]))
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Registro de consumo para Pedido por AMR <small>(cantidad = <strong>{{count($datos["deltaMonitor"])}}</strong>)</small></h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>OP</th>
                                <th>Línea</th>
                                <th>Máquina</th>
                                <th>Ubicación</th>
                                <th class="text-center">Part Number</th>
                                <th class="text-center">Cant. Solicitada</th>
                                <th class="text-center">LPN de Solicitud</th>
                                <th class="text-center">Placas Restantes</th>
                                <th class="text-center">Fecha Procesado</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($datos["deltaMonitor"] as $delta)
                                <tr>
                                    <td>{{$delta->batchId}}</td>
                                    <td>{{$delta->laneNumber}}</td>
                                    <td>{{$delta->idMaquina}}</td>
                                    <td class="text-center">{{$delta->location}}</td>
                                    <td class="text-center">{{$delta->partNumber}}</td>
                                    <td class="text-center">{{$delta->valueqtyPerASSYFinal}}</td>
                                    <td class="text-center"><button class="btn btn-default btn-xs">{{$delta->rawMaterialId}}</button></td>
                                    <td class="text-center">{{$delta->remainingBoards}}</td>
                                    <td class="text-center">{{$delta->timeStampRegistro}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(isset($datos["materialRequest"]))
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pedidos por AMR <small>(cantidad = <strong>{{count($datos["materialRequest"])}}</strong>)</small></h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th class="text-center">OP</th>
                                <th class="text-center">Línea</th>
                                <th class="text-center">LPN de Solicitud</th>
                                <th class="text-center">Material</th>
                                <th class="text-center">Cant. Pedido</th>
                                <th class="text-center">Pedido</th>
                                <th class="text-center">Máquina</th>
                                <th class="text-center">Ubicación</th>
                                <th class="text-center">LPN Reservado</th>
                                <th class="text-center">Fecha Procesado</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($datos["materialRequest"] as $mr)
                                <tr>
                                    <td>{{$mr->id}}</td>
                                    <td class="text-center">{{$mr->op}}</td>
                                    <td class="text-center">{{$mr->PROD_LINE}}</td>
                                    <td class="text-center"><button class="btn btn-default btn-xs">{{$mr->rawMaterial}}</button></td>
                                    <td class="text-center">{{$mr->codMat}}</td>
                                    <td class="text-center">{{$mr->cantASolic}}</td>
                                    <td class="text-center">{{$mr->descUbicacionOrigen}}</td>
                                    <td class="text-center">{{$mr->MAQUINA}}</td>
                                    <td class="text-center">{{$mr->UBICACION}}</td>
                                    <td class="text-center">{{$mr->reserva_lpn}}</td>
                                    <td class="text-center">{{Carbon\Carbon::parse($mr->timestamp)->format('d/m/Y h:m:s')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(isset($datos["ebs"]))
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pedidos a Abastecimiento <small>(cantidad = <strong>{{count($datos["ebs"])}}</strong>)</small></h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th class="text-center">OP</th>
                                <th class="text-center">Material</th>
                                <th class="text-center">Cant. Pedido</th>
                                <th class="text-center">Asignado</th>
                                <th class="text-center">LPN Asignado</th>
                                <th class="text-center">Cant. LPN</th>
                                <th class="text-center">Máquina</th>
                                <th class="text-center">Ubicación</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Mensaje</th>
                                <th class="text-center">Fecha Procesado</th>
                                <th class="text-center">Nro Pedido</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($datos["ebs"] as $ebs)
                                <tr>
                                    <td>{{$ebs->LINEA_ID}}</td>
                                    <td class="text-center">{{$ebs->OP_NUMBER}}</td>
                                    <td class="text-center">{{$ebs->ITEM_CODE}}</td>
                                    <td class="text-center">{{$ebs->QUANTITY}}</td>
                                    <td class="text-center">{{$ebs->QUANTITY_ASSIGNED}}</td>
                                    <td class="text-center">{{$ebs->LPN}}</td>
                                    <td class="text-center">{{$ebs->LPN_QUANTITY}}</td>
                                    <td class="text-center">{{$ebs->MAQUINA}}</td>
                                    <td class="text-center">{{$ebs->UBICACION}}</td>
                                    <td class="text-center">{{$ebs->STATUS}}</td>
                                    <td class="text-center">{{$ebs->ERROR_MESSAGE}}</td>
                                    <td class="text-center">{{Carbon\Carbon::parse($ebs->CREATION_DATE)->format('d/m/Y h:m:s')}}</td>
                                    <td class="text-center">{{$ebs->INSERT_ID}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            </div>
        @endif
    </div>

    @include('iaserver.common.footer')
    {!! IAScript('assets/moment.min.js') !!}
    {!! IAScript('vendor/amr/trazabilidad.js') !!}
@endsection
