<?php

return new \Phalcon\Config([
    'debug' => true,
    'default_module' => 'frontend',
    'modules'        => [
        'frontend' => [
            'dir'           => APP_PATH . 'apps/frontend/',
            'className'     => 'Apps\Frontend\Module'
        ],
        'backend'  => [
            'dir'           => APP_PATH . 'apps/backend/',
            'className'     => 'Apps\Backend\Module'
        ],
        'system'  => [
            'dir'           => APP_PATH . 'apps/system/',
            'className'     => 'Apps\System\Module'
        ],
        'api'      => [
            'dir'           => APP_PATH . 'apps/api/',
            'className'     => 'Apps\Api\Module'
        ]
    ],

    'database' => [
        'adapter'  => 'Mysql',
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'dbname'   => 'me',
        'charset'  => 'utf8'
    ],

    'application' => [
        'cryptSalt' => 'WtxTUtBpgDSPLJIWdVcOQbdza1G1KLYx',
        'cacheDir'  => APP_PATH . 'cache/',
        'storageDir'  => APP_PATH . 'storage/',
        'logsDir'  => APP_PATH . 'logs/'
    ],
    'site_url'  => 'http://192.168.0.21:9000/',
    'site_name' => 'MY_LOVE',
    'site_source' => APP_PATH . 'public/',
    'site_api'  => 'http://192.168.0.21:9000/api/',
    'email' => '598627144@qq.com',
    'pagesize'=>10

]);