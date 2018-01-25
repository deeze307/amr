<?php

namespace IAServer\Http\Controllers\AMR;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\DeltaMonitor;
use IAServer\Http\Controllers\AMR\Model\HeartBeat;
use IAServer\Http\Controllers\AMR\Model\MaterialRequest;
use IAServer\Http\Controllers\AMR\Model\PIZARRA_DBO_PRODUCCION;
use IAServer\Http\Controllers\AMR\Model\SmtDataBase;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_WIP;
use IAServer\Http\Controllers\Email\Email;
use IAServer\Http\Controllers\IAServer\Util;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

class CrudAmr extends Controller
{
    /**
     * @param $material
     */
    public static function insertDeltaMonitor($material)
    {
        $insert = new DeltaMonitor();
        $insert->batchId = $material->batchId;
        $insert->idMaquina = $material->idMaquina;
        $insert->laneNumber = $material->laneNumber;
        $insert->location = $material->location;
        $insert->minutos = $material->remainingMinutes;
        $insert->partNumber = $material->partNumber;
        $insert->productPartNumber = $material->productPartNumber;
        $insert->rawMaterialId = $material->rawMaterialId;
        $insert->remainingBoards = $material->remainingBoards;
        $insert->timeStampRegistro = Carbon::now()->format('d/m/Y H:i:s');
        $insert->valueqtyPerASSYFinal = $material->qtyToRequest;
        $insert->remainingQtyToRequest = $material->remainingQtyToRequest;
        $insert->qtyMath = $material->math;
        $insert->save();
    }

    /**
     * Chequea si un material existe en la tabla Material Request y de ser necesario lo inserta
     *
     * Mas informacion {@url http://www.google.com}
     * @param \stdClass $material array con propiedades del material a Verificar
     * @param bool $insert Validacion para saber si debe insertar en Material Request
     * @return int|mixed
     * @example Code
     * Ej 1
     * <php>
     * $insert = new delta_monitor();
     * $insert->batchId = $material->batchId;
     * $insert->idMaquina = $material->idMaquina;
     * </php>
     */


    public static function checkMaterialRequest($material,$insert = false)
    {
        try
        {
            $toReturn = 0;
            $query = MaterialRequest::where('op',$material->batchId)
                ->where('rawMaterial',$material->rawMaterialId)
                ->where('PROD_LINE',$material->laneNumber)->first();
            if($insert)
            {
                if (count($query) == 0)
                {
                    $matReq = new MaterialRequest();
                    $matReq->op = $material->batchId;
                    $matReq->linMatWip = $material->operationSeq;
                    $matReq->rawMaterial = $material->rawMaterialId;
                    $matReq->codMat = $material->partNumber;
                    $matReq->uniMedMat = $material->UOM;
                    $matReq->cantASolic = $material->qtyToRequest;
                    $matReq->ubicacionOrigen = $material->ubicacionOrigen;
                    $matReq->PROD_LINE = $material->laneNumber;
                    $matReq->MAQUINA = $material->idMaquina;
                    $matReq->UBICACION = $material->location;
//                    $matReq->reserva_lpn = $material->reservaLpn;
                    $matReq->save();
                    Log::info(' +[MR]+ P#: '.$material->partNumber.' | LPN: '.$material->rawMaterialId.' | Linea '.$material->laneNumber.' MaterialRequest | ID -> '.$matReq->id);
                    $output = new ConsoleOutput();
                    $output->writeln(' +<fg=yellow>[MR]</>+ <fg=yellow>P#: '.$material->partNumber.' | LPN: '.$material->rawMaterialId.' | Linea '.$material->laneNumber.'</> | ID -> '.$matReq->id);
                    $toReturn = $matReq->id;
                }
            }
            else
            {
                $toReturn = $query;
            }

            return $toReturn;
        }
        catch(Exception $e)
        {
            Log::error('No se pudo insertar '.$material->rawMaterialId.' | '.$e->getMessage());
            $output = new ConsoleOutput();
            $output->writeln('<fg=red>No se pudo insertar '.$material->rawMaterialId.' | '.$e->getMessage().'</>');
            return $toReturn;
        }
    }

    /**
     * @param $material
     */
    public static function insertOnEBS($material)
    {
        try
        {
            $xxe_cogiscan_pedidos = new XXE_WMS_COGISCAN_PEDIDOS();
            $xxe_cogiscan_pedidos->OP_NUMBER = $material->batchId;
            $xxe_cogiscan_pedidos->ORGANIZATION_CODE = 'UP3';
            $xxe_cogiscan_pedidos->OPERATION_SEQ = $material->operationSeq;
            $xxe_cogiscan_pedidos->ITEM_CODE = $material->partNumber;
            $xxe_cogiscan_pedidos->ITEM_UOM_CODE = $material->UOM;
            $xxe_cogiscan_pedidos->QUANTITY = $material->qtyToRequest;
            $xxe_cogiscan_pedidos->PROD_LINE = $material->laneNumber;
            $xxe_cogiscan_pedidos->MAQUINA = $material->idMaquina;
            $xxe_cogiscan_pedidos->UBICACION = $material->location;
            $xxe_cogiscan_pedidos->STATUS = 'NEW';
            $xxe_cogiscan_pedidos->CREATION_DATE = Carbon::now()->format('Y-m-d H:i:s');
            $xxe_cogiscan_pedidos->INSERT_ID = $material->insertId;
            $xxe_cogiscan_pedidos->save();

            Log::info('+[I]+ Se inserto '.$material->rawMaterialId.' en la interfaz |');
            $output = new ConsoleOutput();
            $output->writeln(' +<fg=yellow>[I]</>+ <fg=yellow>P#: '.$material->partNumber.' LPN: '.$material->rawMaterialId.'</> | ID -> '.$xxe_cogiscan_pedidos->id);
        }
        catch(Exception $ex)
        {
            Log::error('No se inserto '.$material->rawMaterialId.' en la interfaz | '.$ex->getMessage());
            $output = new ConsoleOutput();
            $output->writeln('<fg=red>Error: P#: '.$material->partNumber.' | LPN: '.$material->rawMaterialId.' en Interfaz | '.$ex->getMessage().'</>');
        }
    }

    /**
     * Se guarda un pulso que indica que la aplicación está corriendo
     */
    public static function heartBeat()
    {
        $output = new ConsoleOutput();
        try
        {
            $hearthbreath = new HeartBeat();
            $hearthbreath->beat = '1';
            $hearthbreath->host = Util::getClientHost();
            $hearthbreath->save();
            $output->writeln('<fg=magenta> **  </>Heart Beat <fg=magenta> **  </>');
        }
        catch(Exception $ex)
        {
            Log::error('Ocurrió un error al intentar guardar el pulso de AMR | Mensaje -> '.$ex->getMessage());
            $output->writeln('');
        }

    }

    /**
     * Chequea en la interfaz si hay pedidos para un P# en particular que aun no han llegado a piso de producción.
     * @param $material Material a consultar
     * @param $line Linea a consultar
     * @return bool Si tiene materiales pendientes devuelve TRUE, sino FALSE
     */
    public static function hasRequestIncomplete($material,$line)
    {
        $toReturn = false;
        try
        {
            $lastInsertId = XXE_WMS_COGISCAN_PEDIDOS::where('ITEM_CODE',$material)
                ->where('PROD_LINE',$line)
                ->whereNotNull('LAST_UPDATE_DATE')
                ->orderBy('LINEA_ID','DESC')->first();
//            if ( count($lastInsertId ) == 0)
//            {
//                $lastInsertId->LINEA_ID = 0;
//            }
            if (count($lastInsertId) > 0)
            {
                $now = Carbon::now();
                $lastHour = Carbon::parse($lastInsertId->LAST_UPDATE_DATE);
                if ($now->diffInHours($lastHour) <= 2)
                {
                    $toReturn = true;
                }
                //LOGICA PARA CUANDO TENGAMOS LOS LPNS DEL PEDIDO
//                $lpns = XXE_WMS_COGISCAN_PEDIDO_LPNS::WHERE('LINEA_ID',$lastInsertId->LINEA_ID)->get();
//                if (count($lpns) > 0)
//                {
//                    $cogiscan = new Cogiscan();
//                    foreach ($lpns as $lpn)
//                    {
//                        $result = $cogiscan->queryItem($lpn->LPN);
//                    }
//                }
//                else
//                {
//                    Log::debug('No hay LPNS para el LINEA_ID = '.$lastInsertId->LINEA_ID.' con INSERT_ID = '.$lastInsertId->INSERT_ID);
//                }
            }
        }
        catch(Exception $ex)
        {
            Log::error('No se pudieron obtener las solicitudes incompletas | Error -> '.$ex->getMessage());
        }

        return $toReturn;
    }

    public static function chkDeltaMonitor($material)
    {
        $delta = 0;
        $delta =  DeltaMonitor::where('batchId',$material->batchId)
            ->where('laneNumber',$material->laneNumber)
            ->where('rawMaterialId',$material->rawMaterialId)
            ->orderBy('id','desc')->get();

        return $delta;
    }

    /**
     * Se checkear la cantidad disponible de un material para pedir a una OP especifica
     * @param $batchId
     * @param $partNumber
     * @return mixed
     */
    public static function chkAvailableQtyToRequest($batchId,$partNumber)
    {
        return XXE_WMS_COGISCAN_WIP::where('OP_NUMBER',$batchId)
                                    ->where('MATERIAL',$partNumber)->first();
    }

    public static function getOperationSeq($batchId)
    {
        return XXE_WMS_COGISCAN_WIP::SELECT('OPERATION_SEQ_NUM')->where('OP_NUMBER',$batchId)->first();
    }

    /**
     * Devuelve el Ultimo reporte cargado en la Pizarra para poder calcular la meta para 3hs de producción
     * @param $line Linea a consultar
     * @return mixed
     */
    public static function getLastReportFromPizarra($line)
    {
        return PIZARRA_DBO_PRODUCCION::
        where('linea',$line)
            ->where('anio',Carbon::now()->format('Y'))
            ->whereNotNull('proyectado')
            ->orderBy('id','desc')->first();
    }

    public static function getExpectedProdFromPizarra($batchId,$line)
    {
        $lado = 1;
        if(ends_with($batchId,'B'))
        {
            $lado = 2;
        }
        $smtdatabase = SmtDataBase::where('op',$batchId)->first();

        if ($lado == 2)
        {
            return PIZARRA_DBO_PRODUCCION::from('dbo.panel')->select('paneles_hora')
                ->where('modelo',$smtdatabase->modelo)
                ->where('panel',$smtdatabase->panel)
                ->where('id_linea',$line)
                ->where('lado',$lado)->first();

        }
        else
        {
            return PIZARRA_DBO_PRODUCCION::from('dbo.panel')->select('paneles_hora')
                ->where('modelo',$smtdatabase->modelo)
                ->where('panel',$smtdatabase->panel)
                ->where('id_linea',$line)
                ->where(function($query){
                    $query->where('lado',1);
                    $query->orWhere('lado',3);})->first();
        }
    }

    /***
     * Chequea si el partNumber existe en la OP solicitada
     * @param $batchId
     * @param $partNumber
     * @return bool
     */
    public static function requiredInCgsWip($batchId,$partNumber)
    {
        $cgs_wip = XXE_WMS_COGISCAN_WIP::where('OP_NUMBER',$batchId)
                                        ->where('MATERIAL',$partNumber)->get();

        if(count($cgs_wip) == 0)
        {
//            AmrController::sendEmail($partNumber,$batchId);
            return false;
        }
        else
        {
            if($cgs_wip[0]->REQUIRED_QUANTITY > 0)
            {
                return true;
            }
            else
            {
//                AmrController::sendEmail($partNumber,$batchId);
                return false;
            }
        }
    }

    public static function getDeltaMonitorForTrace($material,$tipo)
    {
        if($tipo == 'lpn')
        {
            $delta = DeltaMonitor::where('rawMaterialId',$material)->get();
        }
        return $delta;
    }

    public static function getMaterialRequestForTrace($material,$tipo)
    {

        if ($tipo == 'lpn')
        {
            $materialRequest = MaterialRequest::selectRaw("
            id,
            op,
            rawMaterial,
            codMat,
            cantASolic,
            timestamp,
            PROD_LINE,
            MAQUINA,
            UBICACION,
            reserva_lpn,
            ubicacionOrigen,
            CASE ubicacionOrigen
                WHEN '0' then 'Aguardando Pedido Anterior'
                WHEN '1' then 'Almacén IA'
                WHEN '2' then 'Abastecimiento'
                WHEN '3' then 'Tránsito en la Línea'
            END as descUbicacionOrigen")
                ->where('rawMaterial',$material)->take(10)->get();
        }
        else
        {
            $materialRequest = MaterialRequest::selectRaw("
            id,
            op,
            rawMaterial,
            codMat,
            cantASolic,
            timestamp,
            PROD_LINE,
            MAQUINA,
            UBICACION,
            reserva_lpn,
            ubicacionOrigen,
            CASE ubicacionOrigen
                WHEN '0' then 'Aguardando Pedido Anterior'
                WHEN '1' then 'Almacén IA'
                WHEN '2' then 'Abastecimiento'
                WHEN '3' then 'Tránsito en la Línea'
            END as descUbicacionOrigen")
                ->where('codMat',$material)
                ->orderBy('timestamp','desc')->take(10)->get();
        }
        return $materialRequest;
    }
    public static function getOnInterfaceForTrace($insertId)
    {
        $ebs = DB::connection('cgs_prod')->table('XXE_WMS_COGISCAN_PEDIDOS AS cp')
            ->selectRaw("
           cp.[LINEA_ID]
          ,cp.[OP_NUMBER]
          ,cp.[ITEM_CODE]
          ,cp.[QUANTITY]
          ,lpns.[LPN]
          ,lpns.[QUANTITY_ASSIGNED] as 'LPN_QUANTITY'
          ,cp.[QUANTITY_ASSIGNED]
          ,cp.[PROD_LINE]
          ,cp.[MAQUINA]
          ,cp.[UBICACION]
          ,cp.[STATUS]
          ,cp.[ERROR_MESSAGE]
          ,cp.[CREATION_DATE]
          ,cp.[LAST_UPDATE_DATE]
          ,cp.[INSERT_ID] ")
            ->leftJoin('XXE_WMS_COGISCAN_PEDIDO_LPNS AS lpns','cp.LINEA_ID','=','lpns.LINEA_ID')
            ->where('cp.INSERT_ID',$insertId)
            ->orderBy('CREATION_DATE','desc')->take(10)->get();
        return $ebs;
    }

}
