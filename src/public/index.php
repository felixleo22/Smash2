<?php
session_start();
require_once '../../vendor/autoload.php';
require_once('../config/boostrap.php');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//Installation de twig
$container = new \Slim\Container();
$container['view'] = function($container) {
    $view = new \Slim\Views\Twig('../views');

    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};



$container['db'] = function ($container) use ($capsule){
    return $capsule;
};

$app = new Slim\App($container);