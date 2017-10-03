app.controller("reservasController",["$scope","$http","reservasFactory","toasty","$log",
    function ($scope,$http,reservasFactory,toasty,$log){
        reservasFactory.getTransitAll().then(function(response){
            $scope.transit = response.data;
        });

        reservasFactory.getWareHouseAll().then(function(response){
            $scope.warehouse = response.data;
        });

    }

]);