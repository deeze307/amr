<?php
namespace IAServer\Http\Controllers\PedidoXPull;


use Illuminate\Routing\Controller;
use IAServer\Http\Controllers\PedidoXPull\Model\pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class controllerPedido extends Controller
{
    public function getAllPedidosXPull()
    {
        $query =   pedido::SELECT(DB::RAW(' p.id,
                                            p.id_usuario,
                                            p.feeder,
                                            p.material,
                                            p.hora_pedido,
                                            p.linea,
                                            p.estado,
                                            p.hora_entregado,
                                            p.usuario_entregado,
                                            p.cygnus,
                                            p.prioridad,
                                            p.material_entregado,
                                            o.nombre,
                                            u.nombre as nombre_entregador'))
            ->FROM('pedido AS p')
            ->LEFTJOIN('operadores AS o','p.id_usuario','=','o.id')
            ->LEFTJOIN('usuario AS u','p.usuario_entregado','=','u.Id')
            ->WHERE('p.hora_pedido','>','2017-04-25 05:00:00.037')
            ->ORDERBY('linea')
            ->GET();
        $byLine = [];
        $groupByLine = $query->groupBy('linea');
        $groupByUser = $query->groupBy('nombre');

        foreach($groupByUser as $nombre => $detalleUsuario)
        {
                $byUser[$nombre] = [
                    'cantidadXUsuario' => $detalleUsuario->count()
                ];
        }

        foreach ($groupByLine as $linea => $detalle)
        {
            foreach($detalle as $index => $pedido)
            {
                $byLine[$linea] = [
                    'totalPedidosXDia' => $detalle->count(),
                    'partnumber' => $detalle,
                ];
            }
        }
      $output = compact('byLine','byUser');
      return  Response::multiple($output,'pedidosxpull.pedidosxpull');

    }
}