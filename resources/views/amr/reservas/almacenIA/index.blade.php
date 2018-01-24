<div class="box-body">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Orden de Producción</th>
            <th>Material</th>
            <th>Lpn Asignado</th>
            <th>Cantidad Solicitada</th>
            <th>Línea</th>
            <th>Ubicación AlmacénIA</th>
            <th>Fecha Solicitud</th>
            <th class="text-center">Acción</th>
        </tr>

        </thead>
        <tbody>
        <tr ng-repeat="w in warehouse">
            <td>@{{w.op}}</td>
            <td>@{{w.pn}}</td>
            <td>@{{w.lpn}}</td>
            <td>@{{w.cantidad}}</td>
            <td>@{{w.linea}}</td>
            <td>@{{w.ubicacion}}</td>
            <td>@{{w.timestamp}}</td>
            <td class="text-center"><button class="btn btn-info btn-xs">entregar</button><td>
        </tr>
        </tbody>
    </table>
</div>