<?php
session_start();   
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../core/Router.php';
require_once '../resources/v1/UserResource.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../resources/v1/ProductosResource.php';
require_once '../resources/v1/LoginResource.php';

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName;

$router = new Router('v1', $basePath);
$userResource = new UserResource();
$productosResource = new ProductosResource();
$LoginResource = new LoginResource();

// ruta para login
$router->addRoute('POST', '/login', [$LoginResource, 'login']);

// rutas para productos
$router->addRoute('GET', '/productos', [$productosResource, 'index']);
$router->addRoute('GET', '/productos/{id}', [$productosResource, 'show']);
$router->addRoute('POST', '/productos', [$productosResource, 'store']);
$router->addRoute('PUT', '/productos/{id}', [$productosResource, 'update']);
$router->addRoute('DELETE', '/productos/{id}', [$productosResource, 'destroy']);


// rutas para usuarios

$router->addRoute('GET', '/users', [$userResource, 'index']);
$router->addRoute('GET', '/users/{id}', [$userResource, 'show']);
$router->addRoute('POST', '/users', [$userResource, 'store']);
$router->addRoute('PUT', '/users/{id}', [$userResource, 'update']);
$router->addRoute('DELETE', '/users/{id}', [$userResource, 'destroy']);

$router->dispatch();
