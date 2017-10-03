app.controller("pedidosProcesadosController",["$scope","$http","pedidosProcesadosFactory","toasty","$log",
    function ($scope,$http,pedidosProcesadosFactory,toasty,$log){
        pedidosProcesadosFactory.getProcessed().then(function(response){
            var tempArray = [];
            angular.forEach(response.data,function(pedido,key){
                pedido.CREATION_DATE_MOD = moment.utc(pedido.CREATION_DATE).format('DD-MM-YYYY HH:mm');
                pedido.LAST_UPDATE_DATE_MOD = moment.utc(pedido.LAST_UPDATE_DATE).format('DD-MM-YYYY HH:mm');
                tempArray.push(pedido);
            });
            $scope.pedidosProcesados = tempArray;

        });
    }

]);