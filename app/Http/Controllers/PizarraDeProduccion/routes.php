<?php
Route::group([
    'prefix'=>'pizarra',
    'namespace'=>'PizarraDeProduccion'],function(){

    Route::get('/',[
        'as'=>'pizarra.home',
        'uses'=>'PizarraController@index'
    ]);

});
