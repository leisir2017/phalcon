<?php

use Phalcon\Mvc\Application;

    # 时区定位中国
    date_default_timezone_set('PRC');

try {


    # 项目根路径
    define('APP_PATH', dirname(dirname(__FILE__)) . '/');

    # 配置文件
    $config = require APP_PATH. 'config/config.php';

    # 第三方路径
    define('LIBRARY',dirname(dirname(__FILE__)) . "/library/");

    # 日志路径
    define('LOGS', $config->application->logsDir );

    # 备份路径
    define('STORAGE', $config->application->storageDir );

    /**
     * 包含注册服务文件
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
