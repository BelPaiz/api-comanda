<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;

require '../src/Clases/Usuario.php';

class UsuariosController
{
    function Prueba(Request $request, Response $response, array $args){
        $response->getBody()->write('Estoy en usuario Controller');
        return $response;
    }
    public static function TraerTodos(Request $request, Response $response, array $args){
        $usuarios = Usuario::TraerTodoLosUsuarios();
        $listado = json_encode(array("ListadoUsuarios"=>$usuarios));
        $response->getBody()->write($listado);
        return $response;
    }
    public static function InsertarUsuario(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $tipo = $parametros['tipo'];
        $subTipo = $parametros['subTipo'];
        $sector = $parametros['sector'];

        $user = new Usuario($nombre, $apellido, $tipo, $subTipo, $sector);
        $ok = $user->InsertarUsuario();
        if($ok != null){
            $retorno = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else{
            $retorno = json_encode(array("mensaje" => "No se pudo crear"));
        }
        
        $response->getBody()->write($retorno);
        return $response;
    }
}