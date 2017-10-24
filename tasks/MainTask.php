<?php

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;

    }

    /**
     * @param array $params
     */
    public function testAction(array $params)
    {
        if( isset($params[0]) ) {
            $info = \Article::detailFornumber($params[0]);
            echo $info->title . PHP_EOL;
            echo $info->number . PHP_EOL;
        }
        
    }
}