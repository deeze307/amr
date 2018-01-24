<?php
namespace IAServer\Http\Controllers\Cogiscan;

ini_set("default_socket_timeout", 120);

use Carbon\Carbon;
use IAServer\Http\Controllers\AMR\Model\XXE_WIP_OT;
use IAServer\Http\Controllers\Node\RestDB2CGS;
use IAServer\Http\Controllers\Node\RestDB2CGSDW;
use IAServer\Http\Requests;
use IAServer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class CogiscanDB2 extends Controller
{
    protected $nodeRestHost = '10.30.10.90';
    protected $nodeRestPort = 1337;

    /**
     * Ejecuta los metodos sin necesidad de definir las rutas
     *
     * @return mixed
     */
    protected function dynamicCommands(){
        $command = Request::segment(3);
        $attributes= array_except( Request::segments() , [0,1,2]);

        $output = "";
        if(method_exists($this,$command))
        {
            $attributes = $this->normalizeAttributes($attributes);
            $output = call_user_func_array(array($this, $command), $attributes);
        } else
        {
            $output = array('error'=>'El metodo no existe');
        }

        return Response::multiple($output);
    }

    private function normalizeAttributes($attributes)
    {
        foreach($attributes as $index => $att)
        {
            $attributes[$index] = urldecode($att);
        }

        return $attributes;
    }

    private function services()
    {
        $class = 'IAServer\Http\Controllers\Cogiscan\CogiscanDB2';

        $array1 = get_class_methods($class);
        if($parent_class = get_parent_class($class)){
            $array2 = get_class_methods($parent_class);
            $array3 = array_diff($array1, $array2);
        }else{
            $array3 = $array1;
        }

        $output = array();

        foreach($array3 as $method)
        {
            $r = new \ReflectionMethod($class, $method);
            $params = $r->getParameters();

            $modifier = head(\Reflection::getModifierNames($r->getModifiers()));

            if($modifier=='public')
            {
                $output[$method] = null;
                foreach ($params as $param) {
                    $output[$method][] = $param->getName() . (($param->isOptional() == true) ? ' (opcional) ' : '');
                }
            }
        }

        return $output;
    }
    /*
     *  Estos metodos usaban un warper creado en .Net para conectar al DB2
     *  ahora se usa un REST ejecutado en un servidor NodeJs
     *
    private function query($db, $query) {
        $debug = new Debug($this,false,'db2',false);

        $ip = Request::server('REMOTE_ADDR');
        $host = getHostByAddr(Request::server('REMOTE_ADDR'));

        $cmd =  '%cd%\bin\db2wrapper\DB2Wrapper.exe '.$db.' '.$query;
        $debug->put($cmd.' IP: '.$ip.' HOST: '.$host);

        $output = array();
        exec($cmd,$output);
        $output = implode("",$output);
		
        return self::toJson($output);
    }
    private function toJson($xml)
    {
        try {
            $arr_xml = (array) simplexml_load_string($xml);
            $string_json = json_encode($arr_xml);
            $json_normalized = str_replace('@','',$string_json);
            $arr_json = json_decode($json_normalized,true);

            return $arr_json;
        } catch(\Exception $ex )
        {
             dd($ex->getMessage(),$xml);
        }
    }
    */
    /////////////////////////////////////////////////////////////////////////////
    //                          COGISCAN NODE REST API
    /////////////////////////////////////////////////////////////////////////////
    private function query($query)
    {
        $rest = new RestDB2CGS();

        //$rows = Input::get('rows');
        //if($rows!=null) {
        //    return $rest->paginate($query,$rows);
        //} else {
            return $rest->get($query);
        //}
    }

    private function queryDW($query)
    {
        $restDW = new RestDB2CGSDW();

        return $restDW->get($query);
    }

    public function materialLoadedAt($fecha_desde = "",$fecha_hasta = "")
    {
        // Si no defino hasta
        if(empty($fecha_hasta)) {
            // y no defino desde
            if (empty($fecha_desde)) {
                $fecha_hasta = Carbon::now()->toDateString();
            } else {
                $fecha_hasta = $fecha_desde;
            }
        }

        if(empty($fecha_desde)) {
            $fecha_desde = Carbon::now()->toDateString();
        }

        $query = "
        SELECT
        INF.PART_NUMBER,
        INF.ITEM_TYPE_NAME,
        INF.ITEM_ID,
        INF.INITIAL_QUANTITY,

        VARCHAR_FORMAT(INF.INIT_TMST, 'YYYY-MM-DD HH24:MI:ss') as INIT_TMST,
        VARCHAR_FORMAT(INF.LOAD_TMST, 'YYYY-MM-DD HH24:MI:ss') as LOAD_TMST,
        VARCHAR_FORMAT(INF.UNLOAD_TMST, 'YYYY-MM-DD HH24:MI:ss') as UNLOAD_TMST,

        VARCHAR_FORMAT(INF.INIT_TMST, 'YYYY-MM-DD HH24') as INIT_TMST_hora,
        VARCHAR_FORMAT(INF.LOAD_TMST, 'YYYY-MM-DD HH24') as LOAD_TMST_hora,
        VARCHAR_FORMAT(INF.UNLOAD_TMST, 'YYYY-MM-DD HH24') as UNLOAD_TMST_hora,

        INF.LOAD_USER_ID,IT.ITEM_ID AS FEEDER_ITEM_ID,ITKT.ITEM_ID AS TOP_ITEM_ID,ITK.ITEM_ID AS CURR_ITEM_ID FROM CGS.ITEM_INFO INF LEFT OUTER JOIN CGS.ITEM IT ON (IT.ITEM_KEY = INF.CNTR_KEY) LEFT OUTER JOIN CGS.ITEM ITKT ON (ITKT.ITEM_KEY =INF.TOP_TOOL_KEY) LEFT OUTER JOIN CGS.ITEM ITK ON (ITK.ITEM_KEY = INF.TOOL_KEY) LEFT OUTER JOIN CGS.TOOL_FOR_LINE TFL ON (TFL.TOOL_KEY = INF.TOOL_KEY)
        WHERE

        DATE(INF.LOAD_TMST) >= '$fecha_desde' AND
        DATE(INF.LOAD_TMST) <= '$fecha_hasta' AND
        INF.UNLOAD_TMST IS NULL
        ORDER BY INF.LOAD_TMST asc
        ";

        return self::query($query);
    }

    public function materialLoadedByUserAt($fecha_desde = "",$fecha_hasta = "")
    {
        $respuesta = $this->materialLoadedAt($fecha_desde,$fecha_hasta);

        if(isset($respuesta["error"]))
        {
            return $respuesta;
        } else
        {
            $handle = collect($respuesta);
            return $handle->groupBy('LOAD_USER_ID');
        }
    }
    /////////////////////////////////////////////////////////////////////////////
    //                          COGISCAN WEBSERVICES
    /////////////////////////////////////////////////////////////////////////////
    public function itemInfoByKey($itemKey)
    {
        $query = "select * from CGS.ITEM_INFO where ITEM_KEY = $itemKey LIMIT1";

        return self::query($query);
    }

    public function itemInfoByToolKey($toolKey)
    {
        $query = "select b.BATCH_ID,p.* from CGSPCM.PRODUCT p inner join CGSPCM.PRODUCT_BATCH b on b.BATCH_KEY = p.BATCH_KEY where p.TOOL_KEY = $toolKey LIMIT 1";
        return self::query($query);
    }

    public function itemInfoByComplex($itemId)
    {
        $query = "select b.BATCH_ID,p.* from CGSPCM.PRODUCT p inner join CGSPCM.PRODUCT_BATCH b on b.BATCH_KEY = p.BATCH_KEY inner join CGS.ITEM i on i.ITEM_ID = '".$itemId."' where p.TOOL_KEY = i.ITEM_KEY LIMIT 1";
        return self::query($query);
    }

    public function itemInfoById($itemId)
    {
        $query = "select * from CGS.ITEM_INFO where CGS.ITEM_INFO.ITEM_ID = '$itemId' LIMIT 1 ";

        return self::query($query);
    }

    public function itemInfoInQuarentine($quarentine='Y')
    {
        $query = "select * from CGS.ITEM_INFO where QUARANTINE_LOCKED = '$quarentine' ";

        return self::query($query);
    }

    public function partNumber($partNumber,$itemId)
    {
        $query = "select * from CGS.PART_NUMBER p left join CGS.ITEM i on i.ITEM_ID = '$itemId'  where p.PART_NUMBER = '$partNumber' ";

        return self::query($query);
    }

    public function posicionesPorUbicacion($partNumber,$itemId,$location="")
    {
        $query = "select * from CGSLSC.TOOL_SETUP where PRODUCT_PN_KEY = (select PART_NUMBER_KEY from CGS.PART_NUMBER where PART_NUMBER = '$partNumber' limit 1) and TOOL_KEY = (select ITEM_KEY from CGS.ITEM where ITEM_ID = '$itemId' LIMIT 1) ";
        if ($location != "")
        {
            $query = "$query AND LOCATION = '$location'";
        }

        return self::query($query);
    }

    public function itemByComplex($itemId)
    {
        $query = "select * from CGS.ITEM where ITEM_ID = '$itemId'";

        return self::query($query);
    }

    public function toolSetup($PRODUCT_PN_KEY,$TOOL_KEY)
    {
        $query = "select * from CGSLSC.TOOL_SETUP where PRODUCT_PN_KEY = $PRODUCT_PN_KEY and TOOL_KEY = $TOOL_KEY";

        return self::query($query);
    }

    public function opByPartNumber($partNumber)
    {
        $query = "select * from CGSPCM.product_batch where product_pn_key = (select part_number_key from CGS.part_number where part_number = '$partNumber')";

        return self::query($query);
    }

    public function opByComplexTool($complexId)
    {
        $arr = new \stdClass();

//        $arr->item_id = '';
//        $arr->op = '';
//        $arr->semielaborado = '';
//        $arr->cantidad_inicial = '';
//        $arr->cantidad_completada = '';
//        $arr->descripcion = '';

        if($complexId ==="all"){
            $query = "SELECT pb.BATCH_ID,ii.ITEM_ID FROM CGSPCM.PRODUCT p
                  LEFT JOIN cgs.ITEM_INFO ii ON ii.ITEM_KEY = p.TOOL_KEY
                  LEFT JOIN cgspcm.PRODUCT_BATCH pb ON pb.batch_key = p.batch_key
                  WHERE ii.ITEM_ID like 'SMT%' OR ii.ITEM_ID like 'L%'";
        }else{
            $query = "SELECT pb.BATCH_ID,ii.ITEM_ID FROM CGSPCM.PRODUCT p
                  LEFT JOIN cgs.ITEM_INFO ii ON ii.ITEM_KEY = p.TOOL_KEY
                  LEFT JOIN cgspcm.PRODUCT_BATCH pb ON pb.batch_key = p.batch_key
                  WHERE ii.ITEM_ID = 'SMT$complexId' OR ii.ITEM_ID like 'L$complexId-NPM-D%'
                  ORDER BY p.LAST_STATUS_CHANGE_TMST LIMIT 1";
        }
        $query = self::query($query);
        if(count($query) > 0)
        {
            $semielaborado = XXE_WIP_OT::select('WIP_ENTITY_NAME','START_QUANTITY','QUANTITY_COMPLETED','SEGMENT1','DESCRIPTION')
                ->WHERE('WIP_ENTITY_NAME',$query[0]->BATCH_ID)->first();

            if (count($semielaborado) > 0)
            {
                $arr->item_id = $query[0]->ITEM_ID;
                $arr->op = $query[0]->BATCH_ID;
                $arr->semielaborado = $semielaborado->SEGMENT1;
                $arr->cantidad_inicial = $semielaborado->START_QUANTITY;
                $arr->cantidad_completada = $semielaborado->QUANTITY_COMPLETED;
                $arr->descripcion = $semielaborado->DESCRIPTION;
            }
            else
            {
                $arr->item_id = $query[0]->ITEM_ID;
                $arr->op = $query[0]->BATCH_ID;
                $arr->semielaborado = "";
                $arr->cantidad_inicial = "";
                $arr->cantidad_completada = "";
                $arr->descripcion = "";
            }
        }

        return $arr;
    }

    public function getFromAlmIA($partNumber="",$notEqualTo=[])
    {
        $query="SELECT ITEM_KEY,ITEM_ID,PART_NUMBER,QUANTITY,CNTR_KEY,LOCATION_IN_CNTR,INIT_TMST,LAST_LOAD_TMST,QUARANTINE_LOCKED,LOAD_USER_ID
                FROM CGS.ITEM_INFO
                WHERE CNTR_KEY=1284469";
        if($partNumber!="")
        {
            $query = "$query AND PART_NUMBER = '$partNumber'";
        }
        if(count($notEqualTo) > 0)
        {
            foreach ($notEqualTo as $key=>$item)
            {
                $query = "$query AND ITEM_ID <> '$item->lpn'";
            }
        }

        $query = "$query FETCH FIRST 1 ROWS ONLY";

        return self::query($query);
    }

    public function getFromTransitIA($partNumber="",$prod_line="",$notEqualTo=[])
    {
        $query="SELECT ITEM_KEY,ITEM_ID,PART_NUMBER,QUANTITY,CNTR_KEY,LOCATION_IN_CNTR,INIT_TMST,LAST_LOAD_TMST,QUARANTINE_LOCKED,LOAD_USER_ID
                FROM CGS.ITEM_INFO
                WHERE CNTR_KEY=1622409";
        if($partNumber!="")
        {
            $query = "$query AND PART_NUMBER = '$partNumber'";
        }
        if ($prod_line !="")
        {
            $query = "$query AND LOCATION_IN_CNTR LIKE '$prod_line%'";
        }

        // chequeo si hay lpns que no esten en las reservas de AMR
        if(count($notEqualTo) > 0)
        {
            foreach ($notEqualTo as $key=>$item)
            {
                $query = "$query AND ITEM_ID <> '$item->lpn'";
            }
        }
        $query = "$query FETCH FIRST 1 ROWS ONLY";
        return self::query($query);
    }
    public function getFromDefaultStorage($partNumber="")
    {
        $query="SELECT ITEM_KEY,ITEM_ID,PART_NUMBER,QUANTITY,CNTR_KEY,LOCATION_IN_CNTR,INIT_TMST,LAST_LOAD_TMST,QUARANTINE_LOCKED,LOAD_USER_ID
                FROM CGS.ITEM_INFO
                WHERE CNTR_KEY=1084719";
        if($partNumber!="")
        {
            $query = "$query AND PART_NUMBER = '$partNumber'";
        }

        $query = "$query FETCH FIRST 1 ROWS ONLY";
        return self::query($query);
    }

    public function getFromQuarantine()
    {
        $query="SELECT P.PART_NUMBER,Q.ITEM_ID,Q.CREATE_TMST,Q.CREATE_USER_ID,Q.TITLE
                FROM CGS.QUARANTINE_RULE Q
                LEFT JOIN CGS.PART_NUMBER P ON P.PART_NUMBER_KEY = Q.PART_NUMBER_KEY";
        return self::query($query);
    }

    public function getAllLoadErrors($desde,$hasta)
    {
        $query = "SELECT * FROM CGS.EVENT_HIST_ALL where event_type = 'WRONG PART NUMBER' AND event_date between '$desde' and '$hasta' ORDER BY event_tmst DESC;";
        return self::queryDW($query);
    }
}
