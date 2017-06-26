<?php


Route::group(['prefix' => 'cargas'], function()
{

    /****** ERRONEAS ******/

    Route::get('/erroneas/all',[
        'as'=>'cargas.erroneas.get',
        'uses'=>'CARGAS\CargasErroneas@getAll'
    ]);

    /**********************/

});