<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Usuario;
use Firebase\JWT\JWT;

require '../src/Clases/Usuario.php';

class UsuariosController
{
    public static function ErrorDatos(Request $request, Response $response, array $args){
        $response->getBody()->write('ERROR!! Carga de datos invalida');
        return $response;
    }
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Usuario::ValidarToken($token, "Admin");
            if($respuesta == "Validado"){
                $usuarios = Usuario::TraerTodoLosUsuarios();
                $usuariosFiltrados = Usuario::FiltrarParaMostrar($usuarios);
                $retorno = json_encode(array("ListadoUsuarios"=>$usuariosFiltrados));
            }
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function POST_InsertarUsuario(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Usuario::ValidarToken($token, "Admin");
            if($respuesta == "Validado")
            {
                $parametros = $request->getParsedBody();
                $nombre = $parametros['nombre'];
                $apellido = $parametros['apellido'];
                $tipo = $parametros['tipo'];
                $email = $parametros['email'];
                $contraseña = $parametros['contraseña'];
                $subTipo = $parametros['subTipo'];
                $sector = $parametros['sector'];
        
                $user = new Usuario($nombre, $apellido, $tipo,$email, $contraseña, $subTipo, $sector);
                $ok = $user->InsertarUsuario();
                if($ok != null){
                    $retorno = json_encode(array("mensaje" => "Usuario creado con exito"));
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
    public static function POST_Login(Request $request, Response $response, array $args){
        $parametros = $request->getParsedBody();

        $email = $parametros['email'];
        $contraseña = $parametros['contraseña'];

        $usuarioEncontrado = null;
        $usuarioEncontrado = Usuario::TraerUnUsuarioEmail($email);

        if($usuarioEncontrado != null){
            if($contraseña == $usuarioEncontrado->password){
                $token = Usuario::Definir_token($usuarioEncontrado->id, $email);
                $jwt = JWT::encode($token, "miClaveSecreta123", "HS256");

                $data = array(
                    "token" => $jwt,
                    "token_exp" => $token["exp"]
                );
                $usuarioEncontrado->ModificarTokenDB($data);
                $retorno = json_encode(array("mensaje" => "Proceso exitoso"));
            }
            else{
                $retorno = json_encode(array("mensaje" => "Contraseña incorrecta"));
            }
        }
        else{
            $retorno = json_encode(array("mensaje" => "Usuario no encontrado"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}