<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Router\Annotations as RouterAnnotations;

/**
 * The FactoryDefault Dependency Injector automatically registers the right
 * services to provide a full stack framework.
 */
$di = new FactoryDefault();


$di->set('eventsManager', 'Phalcon\Events\Manager', true);


/**
 * Registering a router
 */
$di->set('router', function () use ($config)
{

    $router = new RouterAnnotations(false);
    $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
    $router->removeExtraSlashes(false);
    
    require __DIR__ . "/routes.php";

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
    $url->setBaseUri($config->site_url);

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
