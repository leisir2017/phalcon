<?php

use Phalcon\Mvc\Application;

    date_default_timezone_set('PRC');

try {


    # 项目路径
    define('APP_PATH', dirname(dirname(__FILE__)) . '/');

    $config = require APP_PATH. 'config/config.php';

    # 第三方路径
    define('LIBRARY',dirname(dirname(__FILE__)) . "/library/");

    # 日志路径
    define('LOGS', $config->application->logsDir );

    # 备份路径
    define('STORAGE', $config->application->storageDir );

    /**
     * Include services
     */
    require APP_PATH . 'config/services.php';

    /**
     * Handle the request
     */
    $application = new Application();

    /**
     * Assign the DI
     */
    $application->setDI($di);

    /**
     * 加入模块分组配置
     * Register application modules
     */
    require APP_PATH . 'config/modules.php';

    $response = $application->handle();

    echo $response->getContent();

} catch (Exception $e) {
    echo $e->getMessage();
}
