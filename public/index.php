<?php

use App\Controllers\MesasController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use App\Controllers\UsuariosController;
use App\Controllers\ProductosController;
use App\Controllers\PedidosController;

require __DIR__ . '/../vendor/autoload.php';
require '../src/AccesoDatos.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  if(!isset($_GET['accion'])){
    $group->post('[/]', UsuariosController::class . ':ErrorDatos');
    $group->get('[/]', UsuariosController::class . ':ErrorDatos');
  }
  else{
    switch($_GET['accion']){
      case "login":
        $group->post('[/]', UsuariosController::class . ':POST_Login');
      break;
      case "listar":
        $group->get('[/]', UsuariosController::class . ':GET_TraerTodos');
      break;
      case "insertar":
        $group->post('[/]', UsuariosController::class . ':POST_InsertarUsuario');
      break;
    }
  }
  
  });
  $app->group('/productos', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', ProductosController::class . ':GET_TraerTodos');
        break;
        case "insertar":
          $group->post('[/]', ProductosController::class . ':POST_InsertarProducto');
        break;
      }
    }
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', MesasController::class . ':GET_TraerTodos');
        break;
        case "alta":
          $group->post('[/]', MesasController::class . ':POST_Alta_de_mesa');
        break;
        case "cambiarEstado":
          $group->post('[/]', MesasController::class . ':POST_cambiar_estado_de_mesa');
        break;
      }
    }
  });
  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', PedidosController::class . ':GET_TraerTodos');
        break;
        case "alta":
          $group->post('[/]', PedidosController::class . ':POST_AltaPedido');
        break;
        case "cambiarEstado":
          $group->post('[/]', PedidosController::class . ':POST_cambiar_estado_pedido');
        break;
      }
    }
  });

// Run application
$app->run();