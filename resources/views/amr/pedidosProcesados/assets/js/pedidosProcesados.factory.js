app.factory("pedidosProcesadosFactory",["$http","$location",
    function ($http,$location){
        var absurl = $location.absUrl();
        return{
            getProcessed:function() {
                return $http.get('procesados/todos');
            }
        }
    }
]);