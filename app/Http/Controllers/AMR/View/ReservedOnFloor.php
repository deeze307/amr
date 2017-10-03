<?php

namespace IAServer\Http\Controllers\AMR\view;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\MaterialRequest;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;

class ReservedOnFloor extends Controller
{


    public static function getTransitAll()
    {
        $ayer = Carbon::yesterday();
        $transit = MaterialRequest::where('ubicacionOrigen',3)
                                    ->where('timestamp','>',$ayer)
                                    ->orderBy('timestamp','desc')
                                    ->get();
        return $transit;
    }

    public static function getWareHouseAll()
    {
        $ayer = Carbon::yesterday();
        $transit = MaterialRequest::where('ubicacionOrigen',1)
            ->where('timestamp','>',$ayer)
            ->orderBy('timestamp','desc')
            ->get();
        return $transit;
    }
}
