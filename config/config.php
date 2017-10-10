<?php

return new \Phalcon\Config([
    'debug' => true,
    'prefix_session' => 'share_',
    'default_module' => 'frontend',
    'modules'        => [
        'frontend' => [
            'dir'           => APP_PATH . 'apps/frontend/',
            'className'     => 'Apps\Frontend\Module',
            'prefix_router' => false,
            'host_name'     => false
        ],
        'backend'  => [
            'dir'           => APP_PATH . 'apps/backend/',
            'className'     => 'Apps\Backend\Module',
            'prefix_router' => 'backend',
            'host_name'     => false
        ],
        'system'  => [
            'dir'           => APP_PATH . 'apps/system/',
            'className'     => 'Apps\System\Module',
            'prefix_router' => 'system',
            'host_name'     => false
        ],
        'api'      => [
            'dir'           => APP_PATH . 'apps/api/',
            'className'     => 'Apps\Api\Module',
            'prefix_router' => 'api',
            'host_name'     => false
        ]
    ],

    'name_lang_folder' => 'lang',
    'multilang'        => false,
    'default_lang'     => 'en',
    'languages'        => [
        'en' => [
            'name'                => 'English',
            'default_date_format' => 'F j, Y, g:i a'
        ],
        'ua' => [
            'name'                => 'Український',
            'default_date_format' => 'd-m-Y H:i'
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

    'base_uri' => 'http://me.local.com/',
    'site_url'  => 'http://me.local.com/',
    'site_name' => 'MY_LOVE',
    'webServer' => 'http://me.local.com/', //服务器 资源网址  http://www.dayinpai.com/
    //win资源保存物理路径 F:\phpStudy\WWW\paithree\public\\  linux /srv/web/dayinpaifiles/
    'resourceSave' => APP_PATH . 'public/',
    'email' => '598627144@qq.com',
    'pagesize'=>10

]);