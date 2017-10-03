app.controller("pedidosNuevosController",["$scope","$http","pedidosNuevosFactory","toasty","$location","$log",
    function ($scope,$http,pedidosNuevosFactory,toasty,$location,$log){
        pedidosNuevosFactory.getNewRequests().then(function(response){
            $scope.pedidosNuevos = response.data;
        });

        var absurl = $location.absUrl();
        $scope.toggle = function(modalstate,item_code){
            $log.log('Cargado');
            pedidosNuevosFactory.getLanes().then(function(response){
                $scope.lineas = response.data;
                $log.log("lineas cargadas");
                $log.log($scope.lineas);
            });
            $scope.modalstate = modalstate;

            switch (modalstate){
                case 'add':
                    $scope.form_title = "add new partnumber";
                    break;
                case 'edit':
                    $scope.form_title = "pedir de nuevo";
                    $scope.item_code = item_code;
                    $http.get('insertar/' + item_code)
                        .success(function(response){
                            console.log(response);
                            $scope.CogiscanPedidos= response;
                        });
                    break;
                default:
                    break;
            }
            $('#myModal').modal('show');
        };
        $scope.save = function(modalstate, item_code){

            var url = "/insertar";
            if (modalstate === 'edit'){
                url += "/" + item_code;
            }
            $http({
                method: 'POST',
                url: url,
                data: $.param($scope.XXE_WMS_COGISCAN_PEDIDOS),
                headers: {'Content-type':'application/x-www-form-urlencoded'}
            }).success(function(response){
                console.log(response);
                //location.reload();
            }).error(function(response){
                console.log(response);
                alert('algo salio como el orto!!!!!');
            });
        }


    }

]);