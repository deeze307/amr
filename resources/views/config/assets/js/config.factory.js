app.factory("configFactory",["$http","$location",
    function ($http,$location){
        var absurl = $location.absUrl();
        return{
            getConfigVal:function(type){
                return $http.get('config/get/'+type);
            },
            setAMR:function(field,type,valueToModify,value){
                return $http.get('config/set/'+field+'/'+type+'/'+valueToModify+'/'+value);
            }
        }
    }
]);