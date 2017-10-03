app.factory("reservasFactory",["$http","$location",
    function ($http,$location){
        var absurl = $location.absUrl();
        return{
            getTransitAll:function() {
                return $http.get('reservas/transito/all');
            },
            getWareHouseAll:function(){
                return $http.get('reservas/almacenia/all')
            }
        }
    }
]);