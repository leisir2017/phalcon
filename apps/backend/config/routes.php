<?php
$router->add(
    '/backend/:controller/:action',
    array(
        'namespace'  => 'Apps\Backend\Controllers',
        'module' => 'backend',
        'controller' => 1,
        'action'     => 2
        
    )
);

 $router->add(
    '/backend/:controller/:action/:params',
    array(
        'namespace'  => 'Apps\Backend\Controllers',
        'module' => 'backend',
        'controller' => 1,
        'action'     => 2,
        "params"     => 3,
    )
);