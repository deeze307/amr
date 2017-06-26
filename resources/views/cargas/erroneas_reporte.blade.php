@extends('cargas.index')
@section('head')
    {!! IAScript('adminlte/plugins/datatables/jquery.dataTables.min.js') !!}
    {!! IAScript('adminlte/plugins/datatables/dataTables.bootstrap.min.js') !!}
    {!! IAStyle('adminlte/plugins/datatables/dataTables.bootstrap.css') !!}
    {!! IAScript('assets/moment.min.js') !!}
    {!! IAScript('assets/moment.locale.es.js') !!}
@endsection
@section('body')

    <div class="container">
        <!-- will be used to show any messages -->
        <h3>Reporte de Cargas Erroneas de Materiales</h3>
        <hr>
        <div class="col-lg-4">
            <form method="GET" action="{{url('cargas/erroneas/all')}}" class="navbar-form navbar-left">
                <div class="form-group">
                    <input type="text" name="cargas_erroneas_fecha" value="{{ Session::get('cargas_erroneas_fecha') }}" placeholder="Seleccionar fecha" class="form-control"/>
                </div>
                <button type="submit" class="btn btn-info"><i class="fa fa-calendar"></i> Buscar</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="text" style="display: none;" name="export" value=false>
            </form>
        </div>

        <!-- DATE RANGE PICKER -->
        <script type="text/javascript">
            $(function() {
                $('input[name="cargas_erroneas_fecha"]').daterangepicker({
                    locale: {
                        format: 'DD/MM/YYYY',
                        customRangeLabel: 'Definir rango'
                    },
                    ranges: {
                        'Hoy': [moment(), moment()],
                        'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Ultimos 7 dias': [moment().subtract(6, 'days'), moment()],
                        'Ultimos 30 dias': [moment().subtract(29, 'days'), moment()],
                        'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                        'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    autoApply: true
                });
            });

            moment.locale("es");

        </script>
        <!-- Include Date Range Picker -->

        {!! IAScript('assets/jquery/daterangepicker/daterangepicker.js') !!}
        {!! IAStyle('assets/jquery/daterangepicker/daterangepicker.css') !!}
    </div>


    {{--<div class="col-lg-6">--}}
        {{--<hr>--}}
        {{--<h4>Cantidad de Elementos inicializados en el rango especificado: <span class="label label-danger">{{$total->count()}}</span></h4>--}}
        {{--<hr>--}}
    {{--</div>--}}

<div class="col-lg-12">
    {{--{!! $items->render() !!}--}}
    <table id="tablaCargas" class="table table-striped">
        <thead>
        <tr>
            <th data-sortable="true" >Usuario</th>
            <th data-sortable="true" >LPN Cargado</th>
            <th data-sortable="true" >Part Number Cargado</th>
            <th data-sortable="true" >Maquina</th>
            <th data-sortable="true" >Ubicacion</th>
            <th data-sortable="true" >BOM Part Number</th>
            <th data-sortable="true" >Programa</th>
            <th data-sortable="true" >Fecha</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
               <tr>
                   <td >{{$item->USER_ID}}</td>
                   <td >{{$item->DESCRIPTION->loadedLpn}}</td>
                   <td >{{$item->DESCRIPTION->loadedPartNumber}}</td>
                   <td >{{$item->DESCRIPTION->machine}}</td>
                   <td >{{$item->DESCRIPTION->location}}</td>
                   <td >{{$item->DESCRIPTION->BOMpartNumber}}</td>
                   <td >{{$item->DESCRIPTION->productPartNumber}}</td>
                   <td >{{$item->EVENT_TMST}}</td>
               </tr>
                @endforeach
        </tbody>
    </table>
    {{--{!! $items->render() !!}--}}
</div>
    </div>

    {!! IAScript('assets/adonis/ws.min.js') !!}
    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/initrawmaterials.js') !!}

@endsection

