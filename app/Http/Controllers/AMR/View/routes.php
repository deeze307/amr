<?php

Route::group([
'prefix'=>'amr',
'namespace'=>'AMR\View'],
function(){

    // Pedidos Nuevos
    Route::group(['prefix' => 'pedidos/nuevos'],
    function()
    {
        Route::get('/',[
            'as'=>'amr.pedidos.nuevos',
            'uses'=>'viewController@newIndex'
        ]);

        Route::get('/todos',[
            'as'=>'amr.pedidos.nuevos.todos',
            'uses' => 'NewRequests@getNewRequestsOnInterface'
        ]);

        Route::post('/insertar','NewRequests@store');

        Route::get('/lineas',[
            'as'=>'amr.pedidos.nuevos.lineas',
            'uses'=>'NewRequests@getRequestsLanes'
        ]);
    });

    // Pedidos Parciales
    Route::group([
    'prefix' => 'pedidos/parciales',
    'namespace'],
    function()
    {
        Route::get('/',[
            'as'=>'amr.pedidos.parciales',
            'uses'=>'viewController@partialsIndex'
        ]);

        Route::get('/todos',[
            'as'=>'amr.pedidos.nuevos.todos',
            'uses' => 'Partials@getPartialsOnInterface'
        ]);
    });

    // Pedidos Procesados
    Route::group([
    'prefix' => 'pedidos/procesados',
    'namespace'],
    function()
    {
        Route::get('/',[
            'as'=>'amr.pedidos.procesados',
            'uses'=>'viewController@processedIndex'
        ]);

        Route::get('/todos',[
            'as'=>'amr.pedidos.procesados.todos',
            'uses' => 'Processed@getProcessedOnInterface'
        ]);
    });

    // Pedidos Reservados a AlmacenIA | Transito
    Route::group([
    'prefix' => 'pedidos/reservas',
    'namespace'],
    function()
    {
        Route::get('/',[
            'as'=>'amr.pedidos.reservas',
            'uses'=>'viewController@reservedIndex'
        ]);

        Route::get('/transito',[
            'as'=>'amr.pedidos.reservas.transito',
            'uses'=>'viewController@transitIndex'
        ]);

        Route::get('/transito/all',[
            'as'=>'amr.pedidos.reservas.transito.all',
            'uses'=>'ReservedOnFloor@getTransitAll'
        ]);

        Route::get('/almacenia',[
            'as'=>'amr.pedidos.reservas.almacenia',
            'uses'=>'viewController@warehouseIndex'
        ]);

        Route::get('/almacenia/all',[
            'as'=>'amr.pedidos.reservas.almacenia',
            'uses'=>'ReservedOnFloor@getWareHouseAll'
        ]);
    });

    // Piso de Producción
    Route::group([
        'prefix' => 'amr/piso_produccion',
        'namespace'],
        function()
        {

        });


    // Trazabilidad
    Route::group([
    'prefix' => 'amr/trazabilidad',
    'namespace'],
    function()
    {
        Route::get('/',[
            'as'=>'amr.trazabilidad.index',
            'uses'=>'viewController@index'
        ]);

    });


});


