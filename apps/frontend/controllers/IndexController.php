<?php

namespace Apps\Frontend\Controllers;

/**
 * \Article::detailFornumber(58731799909); 实例化的公共模型 \apps\models
 * \Apps\Frontend\Models\Article::detailFornumber(58731799909); 实例化的该空间中的模型 \apps\frontend\models
 **/

class IndexController extends BaseController
{
    public function indexAction()
    {
        $getParams = $this->router->getParams();
        $info = \Article::detailFornumber(58731799909);
        $info1 = \Apps\Frontend\Models\Article::detail(2);
        $this->view->info = $info;
        $this->view->info1 = $info1;
    }

    public function signsAction()
    {
    	die('frontend sign');
    }
}
