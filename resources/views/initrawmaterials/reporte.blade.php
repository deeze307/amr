@extends('initrawmaterials.index')
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
        <h3>Reporte de Inicialización de Materiales <small>desde interfaz de pedidos</small></h3>
        {{--<h4>Ultima Actualización: @{{materialList.timestamp}}</h4>--}}
        <hr>

        <div class="col-lg-4">
            <form method="GET" action="{{url('/init')}}" class="navbar-form navbar-left">
                <div class="form-group">
                    <input type="text" name="initrawmaterials_fecha" value="{{ Session::get('initrawmaterials_fecha') }}" placeholder="Seleccionar fecha" class="form-control"/>
                </div>
                <button type="submit" class="btn btn-info"><i class="fa fa-calendar"></i> Buscar</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="text" style="display: none;" name="export" value=false>
            </form>
        </div>

        <!-- DATE RANGE PICKER -->
        <script type="text/javascript">
            $(function() {
                $('input[name="initrawmaterials_fecha"]').daterangepicker({
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


    <div class="col-lg-6">
        <hr>
        <h4>Cantidad de Elementos inicializados en el rango especificado: <span class="label label-danger">{{$total->count()}}</span></h4>
        <hr>
    </div>

<div class="col-lg-12">
    {!! $items->render() !!}
    <table id="tablainitrawmaterials" class="table table-striped">
        <thead>
        <tr>
            <th data-sortable="true" class="text-center">Part Number</th>
            <th data-sortable="true" class="text-center">Cantidad</th>
            <th data-sortable="true" class="text-center">LPN</th>
            <th data-sortable="true" class="text-center">Fecha de Creación de LPN</th>
            <th data-sortable="true" class="text-center">Fecha de Inicialización</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
               <tr>
                   <td class="text-center">{{$item->part_number}}</td>
                   <td class="text-center">{{$item->quantity}}</td>
                   <td class="text-center">{{$item->lpn}}</td>
                   <td class="text-center">{{$item->creation_date}}</td>
                   <td class="text-center">{{$item->timestamp}}</td>
               </tr>
                @endforeach
        </tbody>
    </table>
    {!! $items->render() !!}
</div>
    </div>

    {!! IAScript('assets/adonis/ws.min.js') !!}
    @include('iaserver.common.footer')
    {!! IAScript('vendor/amr/initrawmaterials.js') !!}

@endsection

