<?php


Route::group(['prefix' => 'pedidos'], function()
{
    Route::get('/pull','PedidoXPull\controllerPedido@getAllPedidosXPull');
});
