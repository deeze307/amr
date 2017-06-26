<?php

namespace IAServer\Http\Controllers\CARGAS;

use Carbon\Carbon;
use IAServer\Http\Controllers\Cogiscan\CogiscanDB2;
use IAServer\Http\Controllers\IAServer\Util;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class CargasErroneas extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public static function getAll()
    {
        $cogiscan = new CogiscanDB2();

        $datePicker = Util::dateRangeFilterEsToday('cargas_erroneas_fecha');
        $desde = Carbon::parse($datePicker->desde)->format('Y-m-d');
        $hasta = Carbon::parse($datePicker->hasta)->format('Y-m-d');
        $cargasErroneas = $cogiscan->getAllLoadErrors($desde,$hasta);

        foreach($cargasErroneas as $carga)
        {
            $item = explode(';',$carga->DESCRIPTION);
            $c = new \stdClass();
            $c->loadedLpn = $item[1];
            $c->loadedPartNumber = $item[2];
            $c->location = $item[3];
            $c->machine = $item[4];
            $c->BOMpartNumber = $item[5];
            $c->productPartNumber = $item[6];
            $carga->DESCRIPTION = $c;
        }

        return view('cargas.erroneas_reporte',["items"=>collect($cargasErroneas)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
