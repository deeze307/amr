@extends('adminlte/theme')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('title','Low Level Warnings')
@section('body')

    <div class="container" ng-controller="lowLevelController">
        <!-- will be used to show any messages -->
        @if (Session::has('message'))
            <div class="alert alert-info">{{ Session::get('message') }}</div>
        @endif

        <h3>Low Level Warnings <small>alertas de bajo nivel de material</small></h3>
        <h4>Ultima Actualización: @{{materialList.timestamp}}</h4>
        <hr>

        <div ng-hide="mostrarTabla"><h4>Espere por favor, Cargando Datos...</h4></div>
        <div ng-show="mostrarTabla">
            <!-- Apply any bg-* class to to the info-box to color it -->
            <h4>Cantidad de Registros: <span class="label label-danger">@{{materialList.length}}</span></h4>
            <hr>

            <div class="col col-lg-12">
                <div class="panel-group " ng-repeat="linea in lineas">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a data-toggle="collapse" href="#@{{linea}}"><span class="fa fa-plus"></span> @{{linea}}</a>
                            </h3>
                        </div>
                        <div id="@{{linea}}" class="panel-collapse collapse">
                            <div class="panel-body">
                                <table id="tablalowlevel" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th data-sortable="true" class="text-center">Máq</th>
                                        <th data-sortable="true" class="text-center">Ubicación</th>
                                        <th data-sortable="true" class="text-center">PartNumber</th>
                                        <th data-sortable="true" class="text-center">LPN Material</th>
                                        <th data-sortable="true" class="text-center">Placas Restantes</th>
                                        <th data-sortable="true" class="text-center">Minutos Restantes</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat = "material in materialList" ng-if = "material.machine.substring(0,3) == linea">
                                            <td class="text-center @{{material.class}}">@{{material.machine}}</td>
                                            <td class="text-center @{{material.class}}">@{{material.location}}</td>
                                            <td class="text-center @{{material.class}}" >@{{material.partNumber}}</td>
                                            <td class="text-center @{{material.class}}" >@{{material.rawMaterialId}}</td>
                                            <td class="text-center @{{material.class}}" >@{{material.remainingBoards}}</td>
                                            <td class="text-center @{{material.class}}" >@{{material.remainingTimeMinutes}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



    {!! IAScript('assets/adonis/ws.min.js') !!}
    {!! IAScript('assets/moment.min.js') !!}

    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/lowlevel.js') !!}

@endsection