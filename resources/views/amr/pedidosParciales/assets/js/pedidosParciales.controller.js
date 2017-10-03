app.controller("pedidosParcialesController",["$scope","$http","pedidosParcialesFactory","toasty","$log",
    function ($scope,$http,pedidosParcialesFactory,toasty,$log){
        pedidosParcialesFactory.getPartials().then(function(response){
            $log.log(response.data);
            $scope.pedidosParciales = response.data;
        });
    }

]);