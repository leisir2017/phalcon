<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;

/**
 * The FactoryDefault Dependency Injector automatically registers the right
 * services to provide a full stack framework.
 */
$di = new FactoryDefault();

/**
 * Registering a router
 */
// require __DIR__ . "/routes.php";

$di->set('eventsManager', 'Phalcon\Events\Manager', true);


$di->set('router', function () use ($config)
{
    $router = new Router();
    
    require __DIR__ . "/routes.php";

    $router->setDefaultNamespace("Apps\Frontend\Controllers");

    $router->setDefaultModule("frontend");

    $router->setDefaultController('index');

    $router->setDefaultAction('index');



    return $router;
});

/**
 * Flash service with custom CSS classes
 */
$di->set('flash', function ()
{
    return new Flash([
        'error'   => 'errorHandler alert alert-danger notification-error',
        'success' => 'errorHandler alert alert-success',
        'notice'  => 'errorHandler alert alert-info',
        'warning' => 'errorHandler alert alert-warning',
    ]);
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config)
{
    $url = new UrlResolver();
    $url->setBaseUri($config->base_uri);

    return $url;
});


/**
 * Register the global configuration as config
 */
$di->set('config', $config);


/**
 * Start the session the first time some component request the session service
 */
$di["session"] = function () {
    $session = new SessionAdapter();

    $session->start();

    return $session;
};

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di['modelsMetadata'] = function () use ($config)
{
    return new Phalcon\Mvc\Model\Metadata\Files([
        'metaDataDir' => $config->application->cacheDir . 'metaData/'
    ]);
};
