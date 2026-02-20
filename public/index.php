<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../core/Router.php';
require_once '../resources/v1/genPassResource.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptName;

$router = new Router('v1', $basePath);
$router->addRoute('GET', '/password', [new GenPassResource(), 'get']);
$router->addRoute('POST', '/passwords', [new GenPassResource(), 'getMultiple']);
$router->addRoute('POST', '/password/validate', [new GenPassResource(), 'passworValidate']);


$router->dispatch();
