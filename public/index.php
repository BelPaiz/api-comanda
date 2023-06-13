<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use App\Controllers\UsuariosController;
use App\Controllers\ProductosController;

require __DIR__ . '/../vendor/autoload.php';
require '../src/AccesoDatos.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', UsuariosController::class . ':TraerTodos');
     $group->post('[/]', UsuariosController::class . ':InsertarUsuario');
  });
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', ProductosController::class . ':TraerTodos');
     $group->post('[/]', ProductosController::class . ':InsertarProducto');
  });

// Run application
$app->run();