<div class="box-body">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Orden de Producción</th>
            <th>Material</th>
            <th>Lpn Asignado</th>
            <th>Cantidad Solicitada</th>
            <th>Línea</th>
            <th>Ubicación</th>
            <th>Ubicación AlmacénIA</th>
            <th>Fecha Solicitud</th>
        </tr>

        </thead>
        <tbody>
        <tr ng-repeat="w in warehouse">
            <td>@{{w.op}}</td>
            <td>@{{w.codMat}}</td>
            <td></td>
            <td>@{{w.cantASolic}}</td>
            <td>@{{w.PROD_LINE}}</td>
            <td>@{{w.MAQUINA}} @{{w.UBICACION}}</td>
            <td></td>
            <td>@{{w.timestamp}}</td>
        </tr>
        </tbody>
    </table>
</div>