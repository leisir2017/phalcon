<?php

# 注册自动加载目录 模型公共目录
$loader = new Phalcon\Loader();

$loader->registerDirs(
    [
        APP_PATH . 'apps/models/', #注意这里，必须填写，否则models/下的文件不能共用。
    ]
)->register();

# 注册各大分组模块
$modules = [];

foreach ($config->modules as $key => $modul)
{
    $modules[$key] = [
        'className' => $modul->className,
        'path'      => $modul->dir . 'Module.php'
    ];
}

$application->registerModules($modules);