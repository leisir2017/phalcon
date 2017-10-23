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
        $this->response->setStatusCode(404, 'Not Found');

        print_r( $this->crypt->encryptBase64('123') );
        print_r('<br>');
        print_r( $this->security->hash('123') );
        print_r('<br>');
        print_r( $this->random->base64Safe(12) );


        $productId = $this->filter->sanitize("asf1232@qq.com12.", 'email');

        print_r('<br>');
        print_r( $productId );

        print_r('<br>');
        print_r( $this->filter->sanitize("12.245454", 'priceformat'));
        die;

    }

    public function signsAction()
    {
    	die('frontend sign');
    }
}
