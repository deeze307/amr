app.controller("reservasController",["$scope","$http","reservasFactory","toasty","$log",function(t,e,r,a,n){r.getTransitAll().then(function(e){t.transit=e.data}),r.getWareHouseAll().then(function(e){t.warehouse=e.data})}]),app.factory("reservasFactory",["$http","$location",function(t,e){e.absUrl();return{getTransitAll:function(){return t.get("reservas/transito/all")},getWareHouseAll:function(){return t.get("reservas/almacenia/all")}}}]);