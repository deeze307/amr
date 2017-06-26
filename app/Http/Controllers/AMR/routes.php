<?php
// GetLowLevel
Route::get('/llw',[
    'as'=>'amr.llw',
    'uses'=>'AMR\AmrController@lowLevelWarnings'
]);

//****** INICIALIZACION DE MATERIALES ********//
Route::get('/init/create',[
    'as'=>'amr.init.create',
    'uses'=>'AMI\AmiController@initRawMaterials'
]);
Route::get('/init',[
    'as'=>'amr.init',
    'uses'=>'AMI\AmiController@viewInitRawMaterials'
]);
//**********************************************//

/**** CONFIG ****/

Route::get('/config',[
    'as'=>'amr.config',
    'uses'=>'CONFIG\configController@config'
]);

Route::get('/config/get/{type}',[
   'as'=>'amr.config.get',
    'uses'=>'CONFIG\configController@getConfigVal'
]);
Route::get('/config/set/{field}/{type}/{valueToModify}/{value}',[
    'as'=>'amr.config.set',
    'uses'=>'CONFIG\configController@setConfigVal'
]);
/****************/

// ***** AMR ***** //
Route::get('/requests/getEnabledLines/{type}',[
    'as'=>'amr.requests.getenabledlines',
    'uses'=>'AMR\AmrController@getEnabledLines'
]);

Route::get('/requests/initamrcicle',[
    'as'=>'amr.requests.init',
    'uses'=>'AMR\AmrController@initAMRCicle'
]);

// *************** //

//** DOCUMENTACION **//

Route::get('/documentacion', ['as' =>'amr.documentacion', 'uses' => 'AMR\Documentacion\Documentacion@index']);

//*******************//
