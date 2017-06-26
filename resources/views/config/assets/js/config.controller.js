app.controller("configController",["$scope","$http","configFactory","toasty",
    function ($scope,$http,configFactory,toasty){
        $scope.lineasAMI = [];
        $scope.lineasAMR= [];
        obtenerConfig('intervalo_amr');
        obtenerConfig('intervalo_ami');
        obtenerConfig('intervalo_delta');

        obtenerInfoStatus('linea_amr');
        obtenerInfoStatus('linea_ami');
        $("#SliderAMR").slider().on("slide", function(slideEvt) {
            $scope.sliderValAMR = slideEvt.value;
        });
        $("#SliderAMI").slider().on("slide", function(slideEvt) {
            $scope.sliderValAMI = slideEvt.value;
        });
        $("#SliderDELTA").slider().on("slide", function(slideEvt) {
            $scope.sliderValDELTA = slideEvt.value;
        });

        $scope.setAMR = function(tipo){
            var valor;
            if(tipo =='itervalo_amr'){
                valor = $scope.sliderValAMR;
            }else if (tipo =='intervalo_ami'){
                valor = $scope.sliderValAMI;
            }
            else if(tipo == 'intervalo_delta')
            {
                valor = $scope.sliderValDELTA;
            }
            console.log(tipo);
            configFactory.setAMR('config_item',tipo,'value',valor).then(function(response){
                if (response.data === 'OK')
                {
                    toasty.success({
                        title: 'Configuración Guardada!'
                    });
                }
                else{
                    toasty.error({
                        title: 'Ocurrió un Error!',
                        shake:true
                    });
                }

            });
        };

        $scope.updateStatus = function(linea){
            var enabled = true;
            if(linea.habilitada == 'Habilitada'){
                enabled = false;
            }
            configFactory.setAMR('id_config',linea.id_config,'enabled',enabled).then(function(response){
                obtenerInfoStatus(linea.config_item);
            })
        };

        function createArrayForLines(tipo,response){
            if(tipo == 'linea_amr'){
                if ($scope.lineasAMR.length > 0){
                    $scope.lineasAMR = [];
                }
            }else {
                if ($scope.lineasAMI.length > 0){
                    $scope.lineasAMI = [];
                }
            }
            angular.forEach(response,function(data){
                var arr = {
                    id_config: data.id_config,
                    config_item: data.config_item,
                    linea: data.value,
                    habilitada: (data.enabled =='true')? 'Habilitada' : 'Deshabilitada',
                    class: (data.enabled =='true')? 'btn btn-xs btn-success' : 'btn btn-xs btn-danger'
                };
                if(tipo == 'linea_amr') {
                    $scope.lineasAMR.push(arr);
                }else{
                    $scope.lineasAMI.push(arr);
                }
            });
        }

        function obtenerInfoStatus(tipo){
            configFactory.getConfigVal(tipo).then(function(response){
                createArrayForLines(tipo,response.data);
            });
        }

        function obtenerConfig(tipo){
            configFactory.getConfigVal(tipo).then(function(response){
                if(tipo =='intervalo_amr'){
                    $scope.sliderValAMR = response.data[0].value;
                }else if(tipo =='intervalo_ami'){
                    $scope.sliderValAMI = response.data[0].value;
                }
                else if (tipo =='intervalo_delta'){
                    $scope.sliderValDELTA = response.data[0].value;
                }
            });
        }


    }
]);