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
                        <h3 class="box-title">Delta Monitor <small>(cantidad = <strong>{{count($datos["deltaMonitor"])}}</strong>)</small></h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>OP</th>
                                <th>Línea</th>
                                <th>Máquina</th>
                                <th>Ubicación</th>
                                <th>Part Number</th>
                                <th>Cant. Solicitada</th>
                                <th>LPN de Solicitud</th>
                                <th>Placas Restantes</th>
                                <th>Fecha Procesado</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($datos["deltaMonitor"] as $delta)
                                <tr>
                                    <td>{{$delta->batchId}}</td>
                                    <td>{{$delta->laneNumber}}</td>
                                    <td>{{$delta->idMaquina}}</td>
                                    <td>{{$delta->location}}</td>
                                    <td>{{$delta->partNumber}}</td>
                                    <td>{{$delta->valueqtyPerASSYFinal}}</td>
                                    <td>{{$delta->rawMaterialId}}</td>
                                    <td>{{$delta->remainingBoards}}</td>
                                    <td>{{$delta->timeStampRegistro}}</td>
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
                        <h3 class="box-title">Material Request</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>OP</th>
                                <th>Línea</th>
                                <th>LPN</th>
                                <th>Material</th>
                                <th>Cant. Pedido</th>
                                <th>Pedido</th>
                                <th>Máquina</th>
                                <th>Ubicación</th>
                                <th>LPN Reservado</th>
                                <th>Fecha Procesado</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($datos["materialRequest"] as $mr)
                                <tr>
                                    <td>{{$mr->id}}</td>
                                    <td>{{$mr->op}}</td>
                                    <td>{{$mr->PROD_LINE}}</td>
                                    <td>{{$mr->rawMaterial}}</td>
                                    <td>{{$mr->codMat}}</td>
                                    <td>{{$mr->cantASolic}}</td>
                                    <td>{{$mr->descUbicacionOrigen}}</td>
                                    <td>{{$mr->MAQUINA}}</td>
                                    <td>{{$mr->UBICACION}}</td>
                                    <td>{{$mr->reserva_lpn}}</td>
                                    <td>{{$mr->timestamp}}</td>
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
                        <h3 class="box-title">Abastecimiento</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>OP</th>
                                <th>Material</th>
                                <th>Cant. Pedido</th>
                                <th>Asignado</th>
                                <th>LPN Asignado</th>
                                <th>Cant. LPN</th>
                                <th>Máquina</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>Mensaje</th>
                                <th>Fecha Procesado</th>
                                <th>Nro Pedido</th>
                            </tr>

                            </thead>
                            <tbody>
                            @foreach($datos["ebs"] as $ebs)
                                <tr>
                                    <td>{{$ebs->LINEA_ID}}</td>
                                    <td>{{$ebs->OP_NUMBER}}</td>
                                    <td>{{$ebs->ITEM_CODE}}</td>
                                    <td>{{$ebs->QUANTITY}}</td>
                                    <td>{{$ebs->QUANTITY_ASSIGNED}}</td>
                                    <td>{{$ebs->LPN}}</td>
                                    <td>{{$ebs->LPN_QUANTITY}}</td>
                                    <td>{{$ebs->MAQUINA}}</td>
                                    <td>{{$ebs->UBICACION}}</td>
                                    <td>{{$ebs->STATUS}}</td>
                                    <td>{{$ebs->ERROR_MESSAGE}}</td>
                                    <td>{{$ebs->CREATION_DATE}}</td>
                                    <td>{{$ebs->INSERT_ID}}</td>
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
