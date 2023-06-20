<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Pedido;
use Usuario;

require '../src/Clases/Pedido.php';
require_once '../src/Clases/Usuario.php';

class PedidosController
{
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $pedidos = Pedido::TraerTodoLosPedidos();
        $pedidosMapp = Pedido::MapearParaMostrar($pedidos);
        $listado = json_encode(array("Listado_de_productos"=>$pedidosMapp));
        $response->getBody()->write($listado);
        return $response;
    }
    public static function POST_AltaPedido(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Usuario::ValidarToken($token, "Empleado" ,"Mozo");
            if($respuesta == "Validado")
            {
                $pedido = new Pedido();
                $parametros = $request->getParsedBody();
                $cadena_items = $parametros['items'];
                $elementos = explode(",", $cadena_items);
                print_r($elementos);
                foreach($elementos as $i){
                    echo $i;
                    $pedido->Cargar_item_nuevo($i);
                }
                $id_insertado = $pedido->Alta_pedido();
                if($id_insertado != null){
                    $retorno = json_encode(array("mensaje" => "Pedido creado con exito"));
                }
                else{
                    $retorno = json_encode(array("mensaje" => "No se pudo crear"));
                }        
            }       
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_cambiar_estado_pedido(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $parametros = $request->getParsedBody();
            if(!isset($parametros['numero_pedido'], $parametros['id_producto'], $parametros['estado'])){
                $retorno = json_encode(array("mensaje" => "Error en la carga de datos"));
            }
            else{
                $numero_pedido = $parametros['numero_pedido'];
                $id_producto = $parametros['id_producto'];
                $estado = $parametros['estado'];
                $tiempoOK = 1;
                $pedido = Pedido::TraerUnPedido_Numero_pedido($numero_pedido);
                if($pedido == null){
                    $retorno = json_encode(array("mensaje" => "El numero de pedido es invalido"));
                }
                else{
                    $sector = $pedido->Cambiar_estado_item($id_producto, $estado);
                    if($estado == 1){
                        $tiempoOK = 0;
                        if(!isset($parametros['tiempo'])){
                            $retorno = json_encode(array("mensaje" => "ingrese el tiempo de elaboracion"));
                        }
                        else{
                            $tiempo = $parametros['tiempo'];
                            if(!($pedido->Agregar_tiempo_item($id_producto, $tiempo))){
                                $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
                            }
                            else{
                                $tiempoOK = 1;
                            }
                        }
                    }
                    else{
                        if($estado == 2){
                            $pedido->Agregar_tiempo_item($id_producto, 0);
                        }
                    }
                    if($sector == null){
                        $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
                    }
                    else{
                        $subtipo = null;
                        switch($sector){
                            case 1:
                                $subtipo = "Bartender";
                            break;
                            case 2:
                                $subtipo = "Cervecero";
                            break;
                            default:
                                $subtipo = "Cocinero";
                            break;
                        }
                        $token = $param['token'];
                        $respuesta = Usuario::ValidarToken($token, "Empleado" ,$subtipo);
                        if($respuesta == "Validado"){
                            if($tiempoOK == 1){
                                $pedido->Actualizar_items_BD();
                                $retorno = json_encode(array("mensaje" => "Estado actualizado con exito"));
                            }
                            else{
                                $retorno = json_encode(array("mensaje" => "ingrese el tiempo de elaboracion"));
                            }
                        }
                        else{
                            $retorno = json_encode(array("mensaje" => $respuesta));
                        }
                    }
                }
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}

