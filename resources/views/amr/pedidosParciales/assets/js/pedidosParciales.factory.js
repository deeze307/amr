app.factory("pedidosParcialesFactory",["$http","$location",
    function ($http,$location){
        var absurl = $location.absUrl();
        return{
            getPartials:function() {
                return $http.get('parciales/todos');
            }
        }
    }
]);