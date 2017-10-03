app.factory("trazabilidadFactory",["$http","$location",
    function ($http,$location){
        var absurl = $location.absUrl();
        return{
            getDelta:function() {
                return $http.get('trazabilidad/delta');
            },
            getMatReq:function(){
                return $http.get('trazabilidad/matreq');
            },
            getReqOnInterface:function(){
                return $http.get('trazabilidad/interfaz');
            }
        }
    }
]);