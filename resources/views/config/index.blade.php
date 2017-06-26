@extends('adminlte/theme')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('title','Low Level Warnings')
@section('head')
    {!! IAScript('assets/bootstrap-slider/js/bootstrap-slider.min.js') !!}
    {!! IAStyle('assets/bootstrap-slider/css/bootstrap-slider.min.css') !!}
    {!! IAScript('assets/slider.js') !!}
@endsection
@section('body')

    <div class="container" ng-controller="configController">
        @if (Session::has('message'))
            <div class="alert alert-info">{{ Session::get('message') }}</div>
        @endif
        <h3>Configuración de AMR <small>solo Admin.</small></h3>
        <hr>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="col col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Pedido Automático
                            </h3>
                        </div>
                        <div class="panel-body">


                            <!-- Apply any bg-* class to to the info-box to color it -->
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="info-box bg-blue">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Intervalo de tiempo (minutos)</span>
                                    <span class="info-box-number">
                                        <span>1</span>
                                        <input id="SliderAMR" ng-model="sliderValAMR" data-slider-id='red' type="text" data-slider-min="1" data-slider-max="45" data-slider-step="1" data-slider-value='30' />
                                        <span>45</span>
                                    </span>
                                <span class="progress-description">
                                  <span id="ex6CurrentSliderValLabelAMR">Intervalo Actual de Tiempo para AMR: <strong><span id="ex6SliderValAMR" ng-bind="sliderValAMR"></span></strong></span>
                                </span>
                                        </div><!-- /.info-box-content -->
                                    </div><!-- /.info-box -->
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-success" ng-click="setAMR('intervalo_amr')">Modificar Intervalo</button>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="info-box bg-blue">
                                        <span class="info-box-icon"><i class="fa fa-files-o"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Cantidad de Registros para Delta Monitor</span>
                                    <span class="info-box-number">
                                        <span>1</span>
                                        <input id="SliderDELTA" ng-model="sliderValDelta" data-slider-id='red' type="text" data-slider-min="1" data-slider-max="45" data-slider-step="1" data-slider-value='30' />
                                        <span>45</span>
                                    </span>
                                <span class="progress-description">
                                  <span id="ex6CurrentSliderValLabelDELTA">Registros Actuales: <strong><span id="ex6SliderValDELTA" ng-bind="sliderValDELTA"></span></strong></span>
                                </span>
                                        </div><!-- /.info-box-content -->
                                    </div><!-- /.info-box -->
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-success" ng-click="setAMR('intervalo_delta')">Modificar Cantidad de Registros</button>
                                </div>
                            </div>

                            <hr>

                            <div class="box box-primary collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Líneas Habilitadas</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div><!-- /.box-tools -->
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <table id="tablaAMR" class="table table-condensed">
                                        <thead>
                                        <tr >
                                            <th data-sortable="true" class="text-center">Línea</th>
                                            <th data-sortable="true" class="text-center">Habilitada</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="linea in lineasAMR">
                                            <td class="text-center">@{{linea.linea}}</td>
                                            <td class="text-center"> <button class="@{{linea.class}}" ng-click="updateStatus(linea)">@{{linea.habilitada}}</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>

                    </div>

                </div>
                <div class="col col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Inicialización de Materiales
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <!-- Apply any bg-* class to to the info-box to color it -->
                                    <div class="info-box bg-blue">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Intervalo de tiempo (minutos)</span>
                                    <span class="info-box-number">
                                        <span>1</span>
                                        <input id="SliderAMI" ng-model="sliderValAMI" data-slider-id='red' type="text" data-slider-min="1" data-slider-max="45" data-slider-step="1" data-slider-value='30' />
                                        <span>45</span>
                                    </span>
                                <span class="progress-description">
                                  <span id="ex6CurrentSliderValLabelAMI">Intervalo Actual de Tiempo: <strong><span id="ex6SliderValAMI" ng-bind="sliderValAMI"></span></strong></span>
                                </span>
                                        </div><!-- /.info-box-content -->
                                    </div><!-- /.info-box -->
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-success" ng-click="setAMR('intervalo_ami')">Modificar Intervalo</button>
                                </div>
                            </div>

                            <hr>

                            <div class="box box-primary collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Líneas Habilitadas</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div><!-- /.box-tools -->
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <table id="tablaAMI" class="table table-condensed">
                                        <thead>
                                        <tr >
                                            <th data-sortable="true" class="text-center">Línea</th>
                                            <th data-sortable="true" class="text-center">Habilitada</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="linea in lineasAMI">
                                            <td class="text-center">@{{linea.linea}}</td>
                                            <td class="text-center"> <button class="@{{linea.class}}" ng-click="updateStatus(linea)">@{{linea.habilitada}}</button></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->




                        </div>


                    </div>
                </div>
            </div>
        </div>

            <toasty></toasty>
    </div>
    {!! IAScript('assets/adonis/ws.min.js') !!}
    {!! IAScript('assets/moment.min.js') !!}


    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/config.js') !!}

@endsection