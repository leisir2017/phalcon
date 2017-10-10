<?php

if(!isset($router)) 
    $router = new \Phalcon\Mvc\Router();

if(!isset($config)) 
    $config = require APP_PATH. 'config/config.php';


$router->notFound(array(
    'namespace'  => 'Apps\Frontend\Controllers',
    'module' => 'frontend',
    'controller' => 'index',
    'action' => 'index',
));

foreach ($config->modules as $key => $modul)
{
    $router->add('/'.$key, array(
        'namespace'  => $modul[$key]['className'],
        'module' => $key,
        'controller' => 'index',
        'action' => 'index',
    ));

    $router->add('/'.$key.'/:controller', array(
        'namespace'  => $modul[$key]['className'],
        'module' => $key,
        'controller' => 1,
        'action' => 'index',
    ));

    $router->add('/'.$key.'/:controller/:action', array(
        'namespace'  => $modul[$key]['className'],
        'module' => $key,
        'controller' => 1,
        'action' => 2,
    ));
    $router->add('/'.$key.'/:controller/:action/:params', array(
        'namespace'  => $modul[$key]['className'],
        'module' => $key,
        'controller' => 1,
        'action' => 2,
        'params' => 3,
    ));
}