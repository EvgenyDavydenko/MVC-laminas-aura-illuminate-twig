<?php

ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

// set up composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

$capsule = new Illuminate\Database\Capsule\Manager();

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'cursophp',
    'username'  => 'phpmyadmin',
    'password'  => 'qwas',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

// create a server request object
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();

// create the router container
$routerContainer = new Aura\Router\RouterContainer();
// get the routing map from the container ...
$map = $routerContainer->getMap();
// add a route to the map, and a handler for it
$map->get('index', '/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'indexAction'
]);
$map->get('addJobs', '/jobs', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction'
]);
$map->post('saveJobs', '/jobs', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction'
]);

// get the route matcher from the container ...
$matcher = $routerContainer->getMatcher();
// .. and try to match the request to a route.
$route = $matcher->match($request);

if (!$route) {
    $response = new Laminas\Diactoros\Response();
    $response->getBody()->write("No route found for the request.");
    $response = $response->withStatus(404);
} else {
    // add route attributes to the request
    foreach ($route->attributes as $key => $val) {
        $request = $request->withAttribute($key, $val);
    }
    // dispatch the request to the route handler.
    $handler = $route->handler;
    $controllerName = $handler['controller'];
    $actionName = $handler['action'];

    $controller = new $controllerName;
    $response = $controller->$actionName($request);
}

$response = $response->withHeader('X-Developer', 'Davydenko');

(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response); 