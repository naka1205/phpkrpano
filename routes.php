<?php
use Naka507\Koa\Router;

$router = new Router();

$router->get('/index',['Controllers\Main', 'index']);

$router->get('/add', ['Controllers\Main', 'add']);

$router->mount('/api', function() use ($router) {

    $router->get('/table', ['Controllers\Api', 'table']);

    $router->post('/save',['Controllers\Api', 'save']);

    $router->post('/upload', ['Controllers\Api', 'upload']);

    $router->post('/add', ['Controllers\Api', 'add']);

});

return $router;
