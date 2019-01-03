<?php
require __DIR__ . '/vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);
define('TEMPLATE_PATH', __DIR__ . DS . 'template');
define('PUBLIC_PATH', __DIR__ . DS . 'public');
define('VTOUR_PATH', __DIR__ . DS . 'vtour');

$tools = "tools_linux";
if ( IS_WIN ) {
    $tools = "tools_win";
}
define('TOOLS_PATH', __DIR__ . DS . $tools);

if ( !IS_CLI ) {
    die('Please use cli');
}

use Naka507\Koa\Application;
use Naka507\Koa\Context;
use Naka507\Koa\Error;
use Naka507\Koa\Timeout;
use Naka507\Koa\NotFound;

use Naka507\Koa\StaticFiles; 

$app = new Application();
$app->υse(new Error());
$app->υse(new Timeout(20));
$app->υse(new NotFound()); 

$public_path = __DIR__ . DS .  "public" ;
$app->υse(new StaticFiles( $public_path )); 


$routes = require __DIR__ . DS . "routes.php";
$app->υse($routes->routes());


$app->listen(3000);