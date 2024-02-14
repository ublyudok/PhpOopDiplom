<?php
if( !session_id() ) {session_start();}

require "../vendor/autoload.php";

use Aura\SqlQuery\QueryFactory;
use DI\ContainerBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
use App\QueryBuilder;
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    Engine::class => function () {
        return new Engine('../app/Views');
    },
    PDO::class => function () {
        $driver = "mysql";
        $host = "localhost";
        $database_name = "OOPDiploma";
        $username = "root";
        $password = "";

        return new PDO("$driver:host=$host; dbname=$database_name", $username, $password);

    },

    Auth::class => function ($container) {
        return new Auth($container->get('PDO'));
    },
]);

$container = $containerBuilder->build();


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {


    $r->addRoute('GET', '/users', ['App\Controllers\Controller', 'users']);
    $r->addRoute('GET', '/register', ['App\Controllers\Controller', 'register']);
    $r->addRoute('GET', '/users/edit', ['App\Controllers\Controller', 'edit']);
    $r->addRoute('GET', '/createUser', ['App\Controllers\Controller', 'createUser']);
    $r->addRoute('GET', '/users/security', ['App\Controllers\Controller', 'security']);
    $r->addRoute('GET', '/login', ['App\Controllers\Controller', 'login']);
    $r->addRoute('GET', '/users/profile', ['App\Controllers\Controller', 'profile']);
    $r->addRoute('GET', '/users/media', ['App\Controllers\Controller', 'media']);
    $r->addRoute('GET', '/users/status', ['App\Controllers\Controller', 'status']);

    $r->addRoute('POST', '/addU', ['App\Controllers\UserController', 'register']);
    $r->addRoute('POST', '/loginUser', ['App\Controllers\UserController', 'login']);
    $r->addRoute('GET', '/logout', ['App\Controllers\UserController','logout']);
    $r->addRoute('POST', '/addUser', ['App\Controllers\UserController','addUser']);
    $r->addRoute('POST', '/addMedia', ['App\Controllers\UserController','addMedia']);
    $r->addRoute('GET', '/addStatus', ['App\Controllers\UserController','addStatus']);
    $r->addRoute('POST', '/addSecurity', ['App\Controllers\UserController','addSecurity']);
    $r->addRoute('GET', '/changeProfile', ['App\Controllers\UserController','changeProfile']);
    $r->addRoute('GET', '/delete', ['App\Controllers\UserController','delete']);

});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo "error 404?..";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo "Method not allow, try chainged link or check your code - 405";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($routeInfo[1], $routeInfo[2]);
        break;
}