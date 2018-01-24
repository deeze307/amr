<?php

namespace IAServer\Http\Controllers\AMR;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\DeltaMonitor;
use IAServer\Http\Controllers\AMR\Model\MaterialRequest;
use IAServer\Http\Controllers\AMR\Model\Reservas;
use IAServer\Http\Controllers\AMR\Model\SmtDataBase;
use IAServer\Http\Controllers\IAServer\Util;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

class CrudReservas extends Controller
{
    /**
     * @param $material
     */
    public static function insertReserved($material)
    {
        $insert = new Reservas();
        $insert->op = $material->op;
        $insert->linea = $material->linea;
        $insert->maquina = $material->maquina;
        $insert->feeder = $material->feeder;
        $insert->pn = $material->pn;
        $insert->lpn = $material->lpn;
        $insert->cantidad = $material->cantidad;
        $insert->ubicacion = $material;
        $insert->id_pedido = $material->id_pedido;
        $insert->status = $material->locationOnFloor;
        $insert->entregado= 'false';
        $insert->save();
    }

   public static function getReservas($element)
   {
       $reservados = Reservas::where('pn',$element->partNumber)
           ->where('status',$element->locationOnFloor)
           ->where('entregado','false')
           ->where('reserva_activa','true')->get();
       return $reservados;
   }


}
