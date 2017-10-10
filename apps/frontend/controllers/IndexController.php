<?php

namespace Apps\Frontend\Controllers;
use \Apps\Models\Article;

class IndexController extends BaseController
{
    public function indexAction()
    {
        // $info = Article::findFirst();
    	// $list = \Apps\Models\Article::find();
    	// print_r($list->toArray());
    	// die;
        $getParams = $this->router->getParams();
    }
}
