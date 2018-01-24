<?php

namespace IAServer\Http\Controllers\AMI;

use IAServer\Http\Controllers\IAServer\Util;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use IAServer\Http\Controllers\AMI\Model\mySql_Materiales;
use IAServer\Http\Controllers\AMR\Model\XXE_WMS_COGISCAN_PEDIDO_LPNS;
use IAServer\Http\Controllers\Cogiscan\Cogiscan;
use Illuminate\Http\Request;

use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;

class AmiController extends Controller
{

    public static function initRawMaterials()
    {
        $output = new ConsoleOutput();
        $output->writeln("|| <fg=cyan>A->'InitRawMaterials'</> Ejecutado | " . Carbon::now()->toDateTimeString() ." ||");
        $pedidos = XXE_WMS_COGISCAN_PEDIDO_LPNS::WHERE('STATUS','NEW')->get();
        if (count($pedidos) == 0)
        {
            $output->writeln('| No hay materiales Nuevos en la interfaz | ' . Carbon::now()->toDateTimeString() .' |');
        }
        else
        {
            $output->writeln('| <fg=green> "'. count($pedidos) . '"</> -> Materiales En la interfaz | ' . Carbon::now()->toDateTimeString() .' |');
        }
        foreach ($pedidos as $key => $pedido )
        {
            $cogiscan = new Cogiscan();
            try{
                // Si los campos máquina y ubicación están vacios es porque es una asignación por PULL
                // y tengo que invertir los campos QUANTITY_ASSIGNED y LPN_QUANTITY
                if(empty($pedido->MAQUINA))
                {
                    $qty = $pedido->QUANTITY_ASSIGNED;
                }
                else
                {
                    $qty = $pedido->LPN_QUANTITY;
                }

                //consulto información de P# antes de inicializar para saber el tipo de material y el MSL
                $partNumber = self::queryPartNumber($pedido->ITEM_CODE);

                $init = $cogiscan->initializeRawMaterial($pedido->LPN,$pedido->ITEM_CODE,'1',$partNumber->containerType,$qty);
            }
            catch (Exception $ex){
                Log::error('Ocurrió un error al inicializar el material '.$pedido->LPN .' | '.$ex->getMessage());
            }

            if (count($init)== 0 || empty($init))
            {
                try{
                    $cogiscan->load($pedido->LPN,'DEFAULTSTORAGE','',false,false);
                    $cogiscan->unload($pedido->LPN,'DEFAULTSTORAGE');
                }
                catch(Exception $ex2){
                    Log::error('Ocurrió un error al Cargar el material '.$pedido->LPN .' a la ubicación DEFAULTSTORAGE | '.$ex2->getMessage());
                }
            }

            self::insertData($pedido);
        }
    }

    private static function insertData($pedido)
    {
        $consulta = mySql_Materiales::WHERE('lpn',$pedido->LPN);
        if ($consulta->count() == 0)
        {
            try{
                $data = new mySql_Materiales();
                $data->part_number= $pedido->ITEM_CODE;
                $data->prod_line = $pedido->PROD_LINE;

                // Checkeo si la asignación se hizo a travéz del AMR o por PULL
                // Si los campos máquina y ubicación están vacios, es porque es una asignacíon por PULL
                // y tengo que invertir los campos QUANTITY_ASSIGNED y LPN_QUANTITY
//                if(empty($pedido->MAQUINA))
//                {
//                    $data->quantity= $pedido->QUANTITY_ASSIGNED;
//                }
//                else
//                {
//                    $data->quantity= $pedido->LPN_QUANTITY;
//                }
                $data->quantity = $pedido->QUANTITY_ASSIGNED;

                $data->op_number = $pedido->OP_NUMBER;
                $data->lpn = $pedido->LPN;
                $data->creation_date = $pedido->CREATION_DATE;
                $data->save();

                //Update en SQL XXE_WMS_COGISCAN_PEDIDO_LPNS
                self::updateRequestOnSQL($pedido);
            }catch (Exception $ex){
                Log::error('Ocurrió un error al intentar insertar el material '. $pedido->LPN .' en la base de datos');
            }
        }

    }

    private static function updateRequestOnSQL($pedido)
    {
        XXE_WMS_COGISCAN_PEDIDO_LPNS::where('LPN',$pedido->LPN)
            ->update(['STATUS'=>'DONE']);
    }

    public function viewInitRawMaterials()
    {
        $arr = new \stdClass();
        $datePicker = Util::dateRangeFilterEsToday('initrawmaterials_fecha');
        $arr->desde = Carbon::parse($datePicker->desde)->format('Y-m-d 00:00:00');
        $arr->hasta = Carbon::parse($datePicker->hasta)->format('Y-m-d 23:59:59');
        $consulta = self::paginado($arr);
        $total = self::total($arr);
        return view('initrawmaterials.reporte',["items"=>$consulta,"total"=>$total]);
    }

    //Obtengo la información de un part number
    public static function queryPartNumber($partNumber)
    {
        $cogiscan = new Cogiscan();
        $part = new \stdClass();
        $ws = $cogiscan->queryPartNumber($partNumber);
        $part->containerType = 'REEL';
        if (!isset($ws['attributes']['message']))
        {
            try
            {
                foreach($ws['ItemTypeList']['ItemType'] as $key => $type)
                {
                    if(isset($type['attributes']))
                    {
                        if ($type['attributes']['name'] == 'TRAY')
                        {
                            $part->containerType = 'TRAY';
                        }
                    }
                    else
                    {
                        if ($type['name'] == 'TRAY')
                        {
                            $part->containerType = 'TRAY';
                        }
                    }

                }
            }
            catch (Exception $ex)
            {

            }

        }

        return $part;
    }

    private function total($arr)
    {
        $total = mySql_Materiales::selectRaw('
            id_material,
            part_number,
            prod_line,
            quantity,
            op_number,
            lpn,
            creation_date,
            timestamp')->whereBetween('timestamp',array($arr->desde,$arr->hasta))->get();
        return $total;
    }
    private function paginado($arr)
    {
        $consulta = mySql_Materiales::selectRaw('
            id_material,
            part_number,
            prod_line,
            quantity,
            op_number,
            lpn,
            creation_date,
            timestamp')->whereBetween('timestamp',array($arr->desde,$arr->hasta))->paginate(50);
        return $consulta;
    }

}
