@extends('adminlte/theme')
@section('title','AMR - Trazabilidad')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('body')

    <div ng-controller="pedidosProcesadosController">
        <div class="col-lg-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Pedidos Procesados por Abastecimiento P3 <small>(desde ayer)</small></h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>OP</th>
                            <th>Material</th>
                            <th>Lpn Asignado</th>
                            <th>Cant. Solicitada</th>
                            <th>Cant. Asignada</th>
                            <th>Cant. LPN</th>
                            <th>Línea</th>
                            <th>Máquina</th>
                            <th>Ubicación</th>
                            <th>Fecha Solicitud</th>
                            <th>Fecha Procesado</th>
                        </tr>

                        </thead>
                        <tbody>
                        <tr ng-repeat="pedido in pedidosProcesados">
                            <td>@{{pedido.OP_NUMBER}}</td>
                            <td>@{{pedido.ITEM_CODE}}</td>
                            <td>@{{pedido.LPN}}</td>
                            <td>@{{pedido.QUANTITY}}</td>
                            <td>@{{pedido.QUANTITY_ASSIGNED}}</td>
                            <td>@{{pedido.LPN_QUANTITY}}</td>
                            <td>@{{pedido.PROD_LINE}}</td>
                            <td>@{{pedido.MAQUINA}}</td>
                            <td>@{{pedido.UBICACION}}</td>
                            <td>@{{pedido.CREATION_DATE_MOD}}</td>
                            <td>@{{pedido.LAST_UPDATE_DATE_MOD}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('iaserver.common.footer')
    {!! IAScript('assets/moment.min.js') !!}
    {!! IAScript('vendor/amr/pedidosProcesados.js') !!}
@endsection
