<?php
  
$router->add('/', array(
    "controller" => "index",
    "action"     => "index"
));

$router->add('/:params', [
    'controller' => 'index',
    'action'     => 'index',
    'params'     => 1
]);

$router->add('/:controller/:params', [
    'controller' => 1,
    'action'     => 'index',
    'params'     => 2
]);

$router->add('/:controller/:action/:params', [
    'controller' => 1,
    'action'     => 2,
    'params'     => 3
]);


$router->add("/pages/:params",array(
    "controller" => "pages",
    "action"     => "index",
    "params"     => 1,
));
