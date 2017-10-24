<?php

 use Phalcon\DI\FactoryDefault\CLI as CliDI,
     Phalcon\CLI\Console as ConsoleApp;

 define('VERSION', '1.0.0');

 //使用CLI工厂类作为默认的服务容器
 $di = new CliDI();

 // 定义应用目录路径
 defined('APP_PATH')
 || define('APP_PATH', realpath(dirname(dirname(__FILE__))));


 /**
  *
  * 注册类自动加载器
  */
 $loader = new \Phalcon\Loader();
 $loader->registerDirs(
     array(
         APP_PATH . '/tasks',
         APP_PATH . '/apps/models',
     )
 );
 $loader->register();

 //加载配置文件（如果存在）
 if(is_readable(APP_PATH . '/config/config.php')) {
     $config = include APP_PATH . '/config/config.php';
     $di->set('config', $config);

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
          
            $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(
                [
                    "host"     => $config->database->host,
                    "username" => $config->database->username,
                    "password" => $config->database->password,
                    "dbname"   => $config->database->dbname,
                    "charset"  => $config->database->charset
                ]
            );
          
            //将事件管理器绑定到db实例中
            $connection->setEventsManager($eventsManager);
          
            return $connection;
        }); 
 }

 // 创建console应用
 $console = new ConsoleApp();
 $console->setDI($di);
 /**
 * 处理console应用参数
 */
 $arguments = array();
 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k >= 3) {
        $arguments['params'][] = $arg;
     }
 }

 // 定义全局的参数， 设定当前任务及action
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

 try {
     // 处理参数
     $console->handle($arguments);
 }
 catch (\Phalcon\Exception $e) {
     echo $e->getMessage();
     exit(255);
 }