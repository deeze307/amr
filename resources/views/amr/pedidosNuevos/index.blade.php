@extends('adminlte/theme')
@section('title','AMR - Pedidos Nuevos')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('body')

    <div ng-controller="pedidosNuevosController">
        <div class="col-lg-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Pedidos Nuevos a Abastecimiento P3</h3>
                    @if(isAdmin() || isMaterialsManager())
                        <button id="btn-add" class="btn btn-danger btn-xs pull-right" ng-click="toggle('add',0)"><i class="fa fa-plus" aria-hidden="true"></i> Pedido Manual</button>
                    @endif
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Orden de Producción</th>
                                <th>Material</th>
                                <th>Cantidad Solicitada</th>
                                <th>Cantidad Asignada</th>
                                <th>Línea</th>
                                <th>Ubicación</th>
                                <th>Fecha Solicitud</th>
                            </tr>

                        </thead>
                        <tbody>
                            <tr ng-repeat="pedido in pedidosNuevos">
                                <td>@{{pedido.OP_NUMBER}}</td>
                                <td>@{{pedido.ITEM_CODE}}</td>
                                <td>@{{pedido.QUANTITY}}</td>
                                <td>@{{pedido.QUANTITY_ASSIGNED}}</td>
                                <td>@{{pedido.PROD_LINE}}</td>
                                <td>@{{pedido.MAQUINA}} @{{pedido.UBICACION}}</td>
                                <td>@{{pedido.CREATION_DATE}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="myModalLabel">Nuevo Pedido</h4>
                    </div>

                    <div class="modal-body">
                        <form name="frmRequest" method="post" action="{{url('amr/pedidos/nuevos/insertar')}}" class="form-horizontal" novalidate="">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                            <div class="form-group error">
                                <label for="inputOp" class="col-sm-3 control-label">OP</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control has-error" id="op_number" name="op_number" placeholder="OP" autocomplete="off"
                                           ng-model="XXE_WMS_COGISCAN_PEDIDOS.OP_NUMBER" ng-required="true" style="text-transform:uppercase">
                                        <span class="help-inline"
                                              ng-show="frmRequest.op_number.$invalid && frmRequest.op_number.$touched">campo obligatorio</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputItemCode" class="col-sm-3 control-label">PartNumber</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="item_code" name="item_code" placeholder="PARTNUMBER" autocomplete="off"
                                           ng-model="XXE_WMS_COGISCAN_PEDIDOS.ITEM_CODE" ng-required="true" style="text-transform:uppercase">
                                        <span class="help-inline"
                                              ng-show="frmRequest.item_code.$invalid && frmRequest.item_code.$touched">campo obligatorio</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputQuantity" class="col-sm-3 control-label">Quantity</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="quantity" name="quantity" placeholder="CANTIDAD" autocomplete="off"
                                           ng-model="XXE_WMS_COGISCAN_PEDIDOS.QUANTITY" ng-required="true">
                                    <span class="help-inline"
                                          ng-show="frmRequest.quantity.$invalid && frmRequest.quantity.$touched">campo obligatorio</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputProdline" class="col-sm-3 control-label">Linea</label>
                                <div class="col-sm-9">
                                    <select CLASS="form-control" name="prod_line">
                                        <option ng-repeat = "linea in lineas" name="prod_line" ng-model="XXE_WMS_COGISCAN_PEDIDOS.PROD_LINE" >@{{linea.lNom}}</option>
                                    </select>
                                    {{--<input type="text" class="form-control" id="prod_line" name="prod_line" placeholder="LINEA" autocomplete="off"--}}
                                    {{--ng-Model="XXE_WMS_COGISCAN_PEDIDOS.PROD_LINE" ng-required="true">--}}
                                    <span class="help-inline"
                                          ng-show="frmRequest.prod_line.$invalid && frmRequest.prod_line.$touched">campo obligatorio</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputMaquina" class="col-sm-3 control-label">Maquina</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="maquina" name="maquina" placeholder="MAQUINA" autocomplete="off"
                                           ng-model="XXE_WMS_COGISCAN_PEDIDOS.MAQUINA" ng-required="true" style="text-transform:uppercase">
                                    <span class="help-inline"
                                          ng-show="frmRequest.maquina.$invalid && frmRequest.maquina.$touched">campo obligatorio</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputUbicacion" class="col-sm-3 control-label">Ubicacion</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="UBICACION" autocomplete="off"
                                           ng-model="XXE_WMS_COGISCAN_PEDIDOS.UBICACION" ng-required="true" style="text-transform:uppercase">
                                    <span class="help-inline"
                                          ng-show="frmRequest.ubicacion.$invalid && frmRequest.ubicacion.$touched">campo obligatorio</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" ng-disabled="frmRequest.$invalid">ENVIAR PEDIDO</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/pedidosNuevos.js') !!}
@endsection
