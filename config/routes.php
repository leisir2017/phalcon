<?php

if(!isset($router)) 
    $router = new \Phalcon\Mvc\Router();

if(!isset($config)) 
    $config = require APP_PATH. 'config/config.php';

$router->setDefaultModule($config->default_module);

$router->notFound(array(
    'namespace'  => 'Apps\Frontend\Controllers',
    'module' => 'frontend',
    'controller' => 'index',
    'action' => 'index',
));

foreach ( $config->modules as $key => $modul )
{

    $router->add('/'.$key, array(
        'module' => $key,
        'controller' => 'index',
        'action' => 'index',
    ));

    $router->add('/'.$key.'/:controller', array(
        'module' => $key,
        'controller' => 1,
        'action' => 'index',
    ));

    $router->add('/'.$key.'/:controller/:action', array(
        'module' => $key,
        'controller' => 1,
        'action' => 2,
    ));

    $router->add('/'.$key.'/:controller/:action/:params', array(
        'module' => $key,
        'controller' => 1,
        'action' => 2,
        'params' => 3,
    ));    

    if ( file_exists( $modul->dir . 'config/routes.php') ) {
        # 包含各个模块中的独立路由配置
        require $modul->dir . 'config/routes.php';

    }

}