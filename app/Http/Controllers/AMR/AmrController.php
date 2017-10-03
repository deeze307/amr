<?php

namespace IAServer\Http\Controllers\AMR;

use Carbon\Carbon;
use IAServer\Http\Controllers\Cogiscan\Cogiscan;
use IAServer\Http\Controllers\Cogiscan\CogiscanDB2;
use IAServer\Http\Controllers\CONFIG\Model\CgsConfig;
use IAServer\Http\Controllers\Email\Email;
use IAServer\Http\Controllers\Redis\RedisCached;
use Illuminate\Http\Request;
use IAServer\Console\Commands;
use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

class AmrController extends Controller
{
    public function lowLevelWarnings()
    {
        return view('lowlevel.index');
    }


    public function init()
    {
        $result = self::getLowLevel();
        $redis = new RedisCached('lowlevel','llw');
        $redis->put($result,60);
        $redis->publish("lowlevel",json_encode($result));
        $output = new ConsoleOutput();
        $output->writeln("|| <fg=cyan>A->'LowLevel'</> Ejecutado | " . Carbon::now()->toDateTimeString() ." ||");
    }

    //Obtengo los LowLevelWarning de todas las líneas
    public static function getLowLevel()
    {
        $cogiscan = new Cogiscan();
        $ws = $cogiscan->getLowLevel();
        return $ws;
    }

    public static function initAMRCicle()
    {
        $output = new ConsoleOutput();
        $output->writeln("|| <fg=cyan>AMR->'initAMRCicle'</> Ejecutado | " . Carbon::now()->toDateTimeString() ." ||");
        $enabledLines = self::getEnabledLinesFormated();
        self::getAMRConfig();
        $output->writeln("<fg=yellow> - Lineas Habilitadas:</>");
        if (count($enabledLines) == 0)
        {
            $output->writeln('    - <fg=red>No hay lineas habilitadas para AMR</>');
        }
        foreach( $enabledLines as $eL)
        {
            $output->writeln("    - <fg=green>".$eL."</>");
        }
        $lowLevels = self::getLowLevel();
        $materialsToRequest = array();
        foreach($lowLevels['Tool'] as $key => $low)
        {
            if (in_array(substr($low['attributes']['id'],0,3),$enabledLines))
            {
                self::doTheMarimonia($low);
            }
        }
        CrudAmr::heartBeat();
        $output->writeln("|| <fg=cyan>AMR->'initAMRCicle'</> Finalizado | " . Carbon::now()->toDateTimeString() ." ||");
    }

    public static function getEnabledLines($type)
    {
        $lineas = CgsConfig::where('config_item',$type)->where('enabled','true')->get();
        return $lineas;
    }

    private static function getEnabledLinesFormated()
    {
        $enabledLines = self::getEnabledLines('linea_amr');
        $list = array();
        foreach ($enabledLines as $line)
        {
            //Si la linea es menor a 10, agrego el pefijo "L0", si no, solo agrego el pefijo "L"
            if ($line->value < 10)
            {
                $formated = 'L0'.$line->value;
            }
            else
            {
                $formated = 'L'.$line->value;
            }
            array_push($list,$formated);
        }
        return $list;
    }

    private static function getAMRConfig()
    {
        $delta = CgsConfig::select('value')->where('config_item','intervalo_delta')->get();
        $interval = CgsConfig::select('value')->where('config_item','intervalo_amr')->get();
        $output = new ConsoleOutput();
        $output->writeln("<fg=yellow>- Intervalo de AMR configurado a '".$interval[0]->value."' minutos </>");
        $output->writeln("<fg=yellow>- Delta Monitor configurado a '".$delta[0]->value."' registros</>");

    }

    private static function doTheMarimonia($materiales)
    {
        foreach ($materiales['PartLowWarning'] as $key=>$mat)
        {
            try
            {
                // Si la cantidad de low levels es 1, entro a un nivel diferente al nivel si tuviera más de un material
                if (count($materiales['PartLowWarning']) > 1)
                { $mat = $mat['attributes'];}
                if ($mat['remainingBoards'] != '0')
                {

                    $matArray = new \stdClass();
                    $line = preg_replace('/(^L0|^L)/','',substr($materiales['attributes']['id'],0,3));
                    $matArray->batchId = self::getPO($line);
                    $matArray->laneNumber ="SMT - ".$line;
                    $matArray->idMaquina = $materiales['attributes']['id'];
                    $matArray->location = $mat['location'];
                    $matArray->productPartNumber = $mat['productPartNumber'];
                    $matArray->partNumber = $mat['partNumber'];
                    $matArray->rawMaterialId = self::formatLPN($mat['rawMaterialId']);
                    $matArray->remainingMinutes = $mat['remainingTimeMinutes'];
                    $matArray->remainingBoards = $mat['remainingBoards'];
                    $matArray->qtyPerLocation = self::qtyPerLocation($materiales['attributes']['id'],$mat);
                    // Chequeo si el material existe en la lista WIP y existe, si es un reemplazo habilitado
                    if (CrudAmr::requiredInCgsWip($matArray->batchId,$matArray->partNumber))
                    {
                        //Si el material es una bandeja duplicada, no la agrego a la lista y no calculo la cantidad a pedir.
                        if ($matArray->qtyPerLocation != null && $matArray->qtyPerLocation > 0)
                        {
                            $toRequest = self::qtyToRequest($line,$matArray->qtyPerLocation,$mat['remainingBoards'],$matArray->batchId,$mat['partNumber']);
                            $matArray->remainingQtyToRequest = $toRequest['remainingQtyToRequest'];
                            $matArray->qtyToRequest = $toRequest['toRequest'];
                            $matArray->math = $toRequest['math'];
                            $matArray->UOM = $toRequest['uom'];
                            $matArray->operationSeq = $toRequest['operationSeq'];
                            if ($matArray->qtyToRequest != 0)
                            {
                                self::checkDeltaMonitor($matArray);
                            }
                        }
                    }
                    else
                    {

                    }
                }
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
            }
        }
    }

    private static function formatLPN($rawMat)
    {
        $split = explode('.S',$rawMat);
        if(count($split) > 1)
        {
            $rawMat = $split[0];
        }
        return $rawMat;
    }
    private static function getPO($line)
    {
        try
        {
            $op = "";
            $redisOp = new RedisCached('amr_op:'.$line,'amr');
            $opParcial = $redisOp->get('amr_op:'.$line);

            if ($opParcial == null || $opParcial == '')
            {
                $cogiscanDB2 = new CogiscanDB2();
                $opParcial = $cogiscanDB2->opByComplexTool($line);
                if($opParcial == "" || $opParcial == null)
                {
                    $opParcial = 'SIN-OP';
                }
                else
                {
                    $opParcial = $opParcial->op;
                }
                $opParcial = explode('-',$opParcial);
                $op = $opParcial[0]."-".$opParcial[1];
                $redisOp->put($op,300);
            }
            else
            {
                $op = $opParcial->scalar;
            }
        }
        catch(Exception $e)
        {
            $op = "SIN-OP";
        }

        return $op;
    }

    private static function qtyPerLocation($toolId,$material)
    {
        try
        {
            $cogiscandb2 = new CogiscanDB2();
            $pos = $cogiscandb2->posicionesPorUbicacion($material['productPartNumber'],$toolId,$material['location']);
            if(count($pos) > 0)
            {
                $pos = $pos[0]->QUANTITY;
            }
            else
            {
                $pos = 0;
            }

        }
        catch(Exception $e)
        {
            $pos = 0;
        }
        return $pos;
    }

    private static function qtyToRequest($line,$qtyPerLocation,$remainingBoards,$batchId,$partNumber)
    {
        $pizarra_dev = CrudAmr::getExpectedProdFromPizarra($batchId,$line);
        //Obtengo la secuencia de Operacion
        $opSeq = CrudAmr::getOperationSeq($batchId);
        $operationSeq = $opSeq->OPERATION_SEQ_NUM;

        //Cantidad que debería usar en 3 horas de producción
        // Si no está configurado el reporte en
        if (!isset($pizarra_dev->paneles_hora))
        {
            $output = new ConsoleOutput();
            $output->writeln('<fg=magenta> Orden de Producción </> '.$batchId.' <fg=magenta>para linea  </> '.$line.' <fg=magenta> No Existe en Pizarra  </>');
            $pizarra_dev = new \stdClass();
            $pizarra_dev->paneles_hora = 50;
        }
        $shouldUse = ($qtyPerLocation * $pizarra_dev->paneles_hora) * 3;

        //Cantidad disponible para pedir en la OP
        $cogiscan_wip = CrudAmr::chkAvailableQtyToRequest($batchId,$partNumber);
        if(count($cogiscan_wip) == 0)
        {
            $remainingQtyToRequest = 0;
            $uom = null;
        }
        else
        {
            $uom = $cogiscan_wip->UOM_CODE;
            $remainingQtyToRequest = $cogiscan_wip->REQUIRED_QUANTITY - $cogiscan_wip->QUANTITY_ISSUED;
        }

        //Cantidad a solicitar
        $toRequest = $shouldUse - ($remainingBoards * $qtyPerLocation);
//        $toRequest = $remainingBoards * $qtyPerLocation;
        if ($toRequest < 0)
        {
            $toRequest = 0;
        }
        if ($remainingQtyToRequest < $toRequest)
        {
            $toRequest = $remainingQtyToRequest;
        }
        return (["toRequest"=>$toRequest,"remainingQtyToRequest"=>$remainingQtyToRequest,"math"=> '"P->"'.$shouldUse.' - "C->" ('.$remainingBoards.'*'.$qtyPerLocation.')',"uom"=>$uom,"operationSeq"=>$operationSeq]);
    }

    private static function checkDeltaMonitor($material)
    {
        try
        {
            $delta = CrudAmr::chkDeltaMonitor($material);

            //Cantidad de registros en delta monitor para insertar en MaterialRequest e Interfaz
            if(count($delta) < 5)
            {
                if(count($delta) != 0)
                {
                    if(collect($delta)->first()->remainingBoards != $material->remainingBoards)
                    {
                        CrudAmr::insertDeltaMonitor($material);
                    }
                }
                else
                {
                    CrudAmr::insertDeltaMonitor($material);
                }
            }
            else
            {
                $sumToRequest = 0;
                foreach ($delta as $d)
                {
                    $sumToRequest = $sumToRequest + $d->valueqtyPerASSYFinal;
                }

                // se calcula el promedio de los registros en Delta Monitor para calcular lo que debería pedir
                $material->qtyToRequest = round($sumToRequest / count($delta));

                //Chequeo que el LPN no esté en materialRequest para no consultar sin sentido.
                $onMatReq = CrudAmr::checkMaterialRequest($material,false);

                if (count($onMatReq) == 0)
                {
                    //Chequeo si el material se encuentra en piso de producción para poder reservarlo
                    $onFloor = self::onFloor($material);
                    $material->ubicacionOrigen = "";
                    switch($onFloor){
                        case 0:
                            $material->ubicacionOrigen = 0; // Hay un Pedido procesado que aún no ha llegado
                            break;
                        case 1:
                            $material->ubicacionOrigen = 1; //
                            break;
                        case 2:
                            $material->ubicacionOrigen = 2;
                            break;
                        case 3:
                            $material->ubicacionOrigen = 3;
                            break;
                    }
                    $material->insertId = CrudAmr::checkMaterialRequest($material,true);
                    if($material->insertId != 0 && $material->ubicacionOrigen == 2)
                    {
                        //Si el id de inserción es distinto a 0 inserto en la interfaz
                        CrudAmr::insertOnEBS($material);
                    }
                }
            }
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
        }
    }

    /**
     * Chequea si el material solicitado se encuentra en piso de produccion y no tiene pedidos esperando a ser entregados
     * @param $material
     * @return boolean
     */
    private static function onFloor($material)
    {
        $obj = new \stdClass();
        $obj->solicitado = $material->qtyToRequest;
        $obj->transito = 0;
        $obj->almacenia = 0;
        $obj->interfaz = 0;

        $ubicacionOrigen = 2;
        $cogiscanDB2 = new CogiscanDB2();
        $linea = Array();
        $linea = explode('-',$material->idMaquina);
        if (count($cogiscanDB2->getFromTransitIA($material->partNumber,$linea[0])) > 0)
        {
            $ubicacionOrigen = 3;
        }
        else if(count($cogiscanDB2->getFromAlmIA($material->partNumber)) > 0)
        {
            $ubicacionOrigen = 1;
        }
        else if (CrudAmr::hasRequestIncomplete($material->partNumber,$material->laneNumber))
        {
            $ubicacionOrigen = 0;
        }

        return $ubicacionOrigen;
    }

    public static function sendEmail($partNumber, $batchId)
    {
        $arr = new \stdClass();
        $arr->partNumber = $partNumber;
        $arr->batchId = $batchId;
        $users = ['dmaidana'];
        $emails = ['diego.maidana@newsan.com.ar'];
        $email = new Email();
        $email->send($users,$emails,'AMR - Numero de Parte No Encontrado',['arr'=>$arr],'emails.amrpartnotfound');
    }

}
