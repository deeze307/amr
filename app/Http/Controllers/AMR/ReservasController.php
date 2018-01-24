<?php

namespace IAServer\Http\Controllers\AMR;

use IAServer\Http\Controllers\AMR\Model\MaterialRequest;
use IAServer\Http\Controllers\AMR\Model\Reservas;
use IAServer\Http\Controllers\CONFIG\Model\CgsConfig;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class ReservasController extends CrudReservas
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $reservas;

    function __construct()
    {
        $this->reservas = "";
    }

    public function index()
    {
        $reservasPendientes = self::getOnFloor();
        return $reservasPendientes;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static function getPointer()
    {
        $puntero = CgsConfig::select('value')
                            ->where('config_item','puntero_reservas_mat_request')
                            ->first();
        return $puntero->value;
    }
    public function getOnFloor()
    {
        $puntero = self::getPointer();
        $this->reservas = MaterialRequest::where('id','>',$puntero)
                                    ->where(function($query){
                                        $query->where('ubicacionOrigen',1);
                                        $query->orWhere('ubicacionOrigen',3);
                                    })->get();
        return $this->reservas;
    }

    /**
     * Consulta si un material se encuentra reservado en una ubicación en particular (Transito, AlmacenIA)
     * @param $partNumber
     * @param $locationOnFloor
     */
    public static function checkReservedRawMaterial($element)
    {
        self::getReservas($element);
    }

    public function findFirstRawMaterial($partNumber,$prodLine,$onFloorLocation)
    {
        $firstPartNumber = "";
        return $firstPartNumber;
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
