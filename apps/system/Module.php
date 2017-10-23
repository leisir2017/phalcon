<?php

namespace Apps\System;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            [
                'Apps\System\Controllers' => __DIR__.'/controllers/',
                'Apps\System\Models' => __DIR__.'/models/',
            ]
        );

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        $config = require APP_PATH . "config/config.php";
        // Registering a dispatcher
        $di->set('dispatcher', function () {

            $dispatcher = new Dispatcher();

            $eventManager = new \Phalcon\Events\Manager();
            // $dispatcher->setEventsManager($di['eventsManager']);

            // Attach a event listener to the dispatcher (if any)
            // For example:
            // $eventManager->attach('dispatch', new \My\Awesome\Acl('frontend'));

            $dispatcher->setEventsManager($eventManager);
            $dispatcher->setDefaultNamespace('Apps\System\Controllers\\');
            return $dispatcher;
        });


        // Registering the view component
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir(APP_PATH.'apps/system/views/');
            $view->registerEngines([
                '.volt'  => function ($view, $di) {

                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

                    $volt->setOptions([
                        'compiledPath'      => APP_PATH . '/cache/volt/system/',
                        'compiledSeparator' => '_'
                    ]);
                    require APP_PATH. 'config/filter.php';

                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
                '.php'   => 'Phalcon\Mvc\View\Engine\Php'
            ]);
            return $view;
        });

        // Set a different connection in each module
        $di->set('db', function() use ( $config, $di) {
            //新建一个事件管理器
            $eventsManager = new \Phalcon\Events\Manager();
          
            //从di中获取共享的profiler实例
            $profiler = new \Phalcon\Db\Profiler();
          
            //监听所有的db事件
            $eventsManager->attach('db', function($event, $connection) use ($profiler) {
                //一条语句查询之前事件，profiler开始记录sql语句
                if ($event->getType() == 'beforeQuery') {
                    $profiler->startProfile($connection->getSQLStatement());
                }
                //一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
                if ($event->getType() == 'afterQuery') {
                    $profiler->stopProfile();
                }
            });
          
            $connection = new Mysql(
                [
                    "host"     => $config->database->host,
                    "username" => $config->database->username,
                    "password" => $config->database->password,
                    "dbname"   => $config->database->name,
                ]
            );
          
            //将事件管理器绑定到db实例中
            $connection->setEventsManager($eventsManager);
          
            return $connection;
        }); 
        
        $di["router"] = function () {
            $router = new Router();
            require __DIR__ . "config/routes.php";
            return $router;
        };
    }
}
