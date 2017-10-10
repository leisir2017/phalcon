<?php

namespace Apps\Backend\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->dispatcher->forward(array(
            "controller" => "login",
            "action" => "index"
        ));
    }
}
