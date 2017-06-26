<?php

namespace IAServer\Http\Controllers\AMR;

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\DeltaMonitor;
use IAServer\Http\Controllers\AMR\Model\HeartBeat;
use IAServer\Http\Controllers\AMR\Model\MaterialRequest;
use IAServer\Http\Controllers\AMR\Model\PIZARRA_DBO_PRODUCCION;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDOS;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_WIP;
use IAServer\Http\Controllers\Email\Email;
use IAServer\Http\Controllers\IAServer\Util;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
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
            $query = MaterialRequest::where('rawMaterial',$material->rawMaterialId)
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
            $lastInsertId = XXE_WMS_COGISCAN_PEDIDOS::WHERE('ITEM_CODE',$material)
                ->WHERE('PROD_LINE',$line)
                ->orderBy('LINEA_ID','DESC')->first();
//            if ( count($lastInsertId ) == 0)
//            {
//                $lastInsertId->LINEA_ID = 0;
//            }
            if (count($lastInsertId) > 0)
            {
                Log::debug('Ultimo pedido para material -> '.$material.' de linea '.$line.' fue el LINEA_ID='.$lastInsertId->LINEA_ID);
                $now = Carbon::now();
                $lastHour = Carbon::parse($lastInsertId->LAST_UPDATE_DATE);
                Log::debug('diferencia en horas entre '.$now.' y '.$lastHour.' es = '. $now->diffInHours($lastHour));
                if ($now->diffInHours($lastHour) <= 2)
                {
                    $toReturn = true;
                    Log::debug('El ultimo pedido para el material '.$material.' de '.$line.' aun no ha sido entregado | last : '.$lastHour);
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
     * @param $batchId La OP que deseamos consultar
     * @param $partNumber El PartNumber que deseamos consultar
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

    public static function requiredInCgsWip($batchId,$partNumber)
    {
        $cgs_wip = XXE_WMS_COGISCAN_WIP::where('OP_NUMBER',$batchId)
                                        ->where('MATERIAL',$partNumber)->get();

        if(count($cgs_wip) == 0)
        {
            AmrController::sendEmail($partNumber,$batchId);
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
                AmrController::sendEmail($partNumber,$batchId);
                return false;
            }
        }
    }



}
