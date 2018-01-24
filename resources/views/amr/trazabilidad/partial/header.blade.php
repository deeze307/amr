<div class="well" style="height: 70px;">
    <div class="row">
        <form method="POST" action="{{route('amr.trazabilidad.find')}}" >
            <div class="col-sm-3">
                <input type="text" name="material" class="form-control" placeholder="Ingresar Material"/>
            </div>
            <div class="col-sm-2">
                <select name="tipo_busqueda" class="form-control">
                    <option value ="pn">Número de Parte</option>
                    <option value ="lpn">LPN (contenedor)</option>
                </select>
            </div>
            <div class="col-sm-4">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-info"><i class="glyphicon glyphicon-search"></i> Información de Material</button>
                </span>
            </div>

        </form>
    </div>
</div>
