<?php

namespace Apps\Backend;

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
                'Apps\Backend\Controllers' => __DIR__.'/controllers/',
                'Apps\Backend\Models' => __DIR__.'/models/',
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
            $dispatcher->setDefaultNamespace('Apps\Backend\Controllers\\');
            return $dispatcher;
        });


        // Registering the view component
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir(APP_PATH.'apps/backend/views/');
            $view->registerEngines([
                '.volt'  => function ($view, $di) {

                    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

                    $volt->setOptions([
                        'compiledPath'      => APP_PATH . '/cache/volt/system/',
                        'compiledSeparator' => '_'
                    ]);
                    $volt->getCompiler()->addFilter('isset', 'isset');
                    $volt->getCompiler()->addFilter('empty', 'empty');
                    $volt->getCompiler()->addFilter('count', 'count');
                    $volt->getCompiler()->addFilter('floatval', 'floatval');
                    $volt->getCompiler()->addFilter('strstr', 'strstr');
                    $volt->getCompiler()->addFilter('setdate',function ($resolvedArgs, $exprArgs) {
                        return 'date("Y-m-d", '. $resolvedArgs  . ')';
                    });
                    $volt->getCompiler()->addFilter('setmonth',function ($resolvedArgs, $exprArgs) {
                        return 'date("m-d", '. $resolvedArgs  . ')';
                    });

                    $volt->getCompiler()->addFilter('setdatetime',function ($resolvedArgs, $exprArgs) {
                        return 'date("Y-m-d H:i:s", '. $resolvedArgs  . ')';
                    });
                    $volt->getCompiler()->addFilter('settime',function ($resolvedArgs, $exprArgs) {
                        return 'date("H:i:s", '. $resolvedArgs  . ')';
                    });

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
                    "charset"  => $config->database->charset
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
