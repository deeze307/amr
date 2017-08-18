@extends('adminlte/theme')
@section('ng','app')
@section('mini',true)
@section('nobox',true)
@section('noHeaderNav',true)
@section('title','Pizarra de Producción')
@section('body')

    <div class="container-fluid" ng-controller="pizarraController">
        <!-- will be used to show any messages -->
        @if (Session::has('message'))
            <div class="alert alert-info">{{ Session::get('message') }}</div>
        @endif

        <h3> Pizarra de Producción SMD-2 | <i class="fa fa-thumbs-o-up fa-2x text-success text-center"></i> </h3>

        <div class="box">
             <div class="box-body">
                 @include('pizarra.partial.prodchart')
             </div>
         </div>

        <div class="box">
            <div class="box-body" >
                @include('pizarra.partial.linestat')
            </div>
        </div>
    </div>


    {!! IAScript('assets/adonis/ws.min.js') !!}
    {!! IAScript('assets/moment.min.js') !!}

    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/pizarra.js') !!}

@endsection