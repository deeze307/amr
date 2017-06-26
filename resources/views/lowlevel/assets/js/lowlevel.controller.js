app.controller("lowLevelController",["$scope","$http",
function ($scope,$http){
    $scope.lowlevels = "";
    const io = ws('localhost:3333',{});

    //Init Job
    $http.get('http://localhost/amr/public/init');
    const client = io.channel('lowlevel');
    client.connect(function(error, connected){
        if(error){
            console.log(error);
            return
        }
        console.log('LowLevelMonitor Conectado');
        client.emit('LowLevelSubscribe');
        client.emit('LowLevelEvent');
    });

    client.on('disconnect',function() {
        console.log('LowLevel Desconectado');
    });
    client.on('LowLevelEventResponse', function (message) {
        $scope.estado = message;
        //client.emit('LowLevelEvent');
        $scope.$apply();
    });

    client.on('LowLevelChannelRedis', function (message) {
        $scope.lowlevels = JSON.parse(message);
        $scope.machineList =[];
        $scope.materialList=[];
        $scope.lineas = [];
        $scope.mostrarTabla= false;
        angular.forEach($scope.lowlevels,function(maquinas,keyMaq){
            angular.forEach(maquinas,function(maquina,keyMat){
                var matSubs = maquina.attributes.id.split('-');
                if ($scope.lineas.indexOf(matSubs[0])=== -1){
                    $scope.lineas.push(matSubs[0]);
                }
                angular.forEach(maquina.PartLowWarning,function(material,key3){
                    var newMat;
                    if (material.attributes !== undefined) {
                        material.attributes.machine = maquina.attributes.id;
                        if (material.attributes.status === 'Empty'){
                            material.attributes.class = 'danger';
                        }
                        newMat = material.attributes;
                    }
                    else {
                        material.machine = maquina.attributes.id;
                        if (material.status === 'Empty'){
                            material.class = 'danger';
                        }
                        newMat = material;
                    }
                    $scope.materialList.push(newMat);
                });
                $scope.mostrarTabla = true;
            });
        });
        $scope.materialList.timestamp = moment().format('LTS');
        $scope.$apply();
    });
}
]);