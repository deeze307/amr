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
            <th>Ubicación EnTransito</th>
            <th>Fecha Solicitud</th>
        </tr>

        </thead>
        <tbody>
        <tr ng-repeat="t in transit">
            <td>@{{t.op}}</td>
            <td>@{{t.codMat}}</td>
            <td></td>
            <td>@{{t.cantASolic}}</td>
            <td>@{{t.PROD_LINE}}</td>
            <td>@{{t.MAQUINA}} @{{t.UBICACION}}</td>
            <td></td>
            <td>@{{t.timestamp}}</td>
        </tr>
        </tbody>
    </table>
</div>