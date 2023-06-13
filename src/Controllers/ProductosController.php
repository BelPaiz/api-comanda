<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Producto;

require '../src/Clases/Producto.php';

class ProductosController
{
    function Prueba(Request $request, Response $response, array $args){
        $response->getBody()->write('Estoy en productos Controller');
        return $response;
    }
    public static function TraerTodos(Request $request, Response $response, array $args){
        $productos = Producto::TraerTodoLosProductos();
        $listado = json_encode(array("ListadoUsuarios"=>$productos));
        $response->getBody()->write($listado);
        return $response;
    }
    public static function InsertarProducto(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];

        $user = new Producto($nombre, $sector);
        $ok = $user->InsertarProducto();
        if($ok != null){
            $retorno = json_encode(array("mensaje" => "Producto creado con exito"));
        }
        else{
            $retorno = json_encode(array("mensaje" => "No se pudo crear"));
        }
        
        $response->getBody()->write($retorno);
        return $response;
    }
}