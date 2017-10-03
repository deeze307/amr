@extends('adminlte/theme')
@section('title','AMR - Reservas')
@section('ng','app')
@section('mini',false)
@section('collapse',false)
@section('body')
    <div class="container-fluid" ng-controller="reservasController">
        <h3>Reservas</h3>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#enTransito" data-toggle="tab">En Transito</a></li>
                            <li><a href="#almacenIA" data-toggle="tab">Almacén IA</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="enTransito">
                                @include('amr.reservas.transito.index')
                            </div>
                            <div class="tab-pane" id="almacenIA">
                                @include('amr.reservas.almacenIA.index')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('iaserver.common.footer')
    {!! IAScript('assets/moment.min.js') !!}
    {!! IAScript('vendor/amr/reservas.js') !!}
@endsection
