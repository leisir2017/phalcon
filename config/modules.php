<?php

$modules = [];

foreach ($config->modules as $key => $modul)
{
    $modules[$key] = [
        'className' => $modul->className,
        'path'      => $modul->dir . 'Module.php'
    ];
}

$application->registerModules($modules);
