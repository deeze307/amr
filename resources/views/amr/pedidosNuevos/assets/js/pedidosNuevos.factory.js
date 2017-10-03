app.factory("pedidosNuevosFactory",["$http","$location",
    function ($http,$location){
        var absurl = $location.absUrl();
        return{
            getNewRequests:function() {
                return $http.get('nuevos/todos');
            },
            getLanes:function(){
                return $http.get('nuevos/lineas');
            }
        }
    }
]);