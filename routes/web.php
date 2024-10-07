<?php

use App\Router;

$router = new Router();

$router->get('/api/v1/login', 'AuthController@login');
$router->get('/api/v1/events', 'EventController@index');
$router->post('/api/v1/events/create', 'EventController@store');
$router->get('/api/v1/events/{id}', 'EventController@show');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($uri, $method);
