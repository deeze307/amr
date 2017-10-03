@extends('adminlte/theme')
@section('title','AMR - Pedidos Parciales')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('body')

    <div ng-controller="pedidosParcialesController">
        <div class="col-lg-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Pedidos Parciales o con Errores</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>OP</th>
                                <th>Material</th>
                                <th>Cant. Solicitada</th>
                                <th>Línea</th>
                                <th>Ubicación</th>
                                <th>Status</th>
                                <th>Error</th>
                                <th>Fecha Procesado</th>
                            </tr>

                        </thead>
                        <tbody>
                            <tr ng-repeat="pedido in pedidosParciales">
                                <td>@{{pedido.OP_NUMBER}}</td>
                                <td>@{{pedido.ITEM_CODE}}</td>
                                <td>@{{pedido.QUANTITY}}</td>
                                <td>@{{pedido.PROD_LINE}}</td>
                                <td>@{{pedido.MAQUINA}} @{{pedido.UBICACION}}</td>
                                <td>@{{pedido.STATUS}}</td>
                                <td>@{{pedido.ERROR_MESSAGE}}</td>
                                <td>@{{pedido.LAST_UPDATE_DATE}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/pedidosParciales.js') !!}
@endsection
