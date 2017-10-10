<?php

namespace Apps\Api\Controllers;


class IndexController extends BaseController
{
    public function indexAction()
    {
    	$this->view->disable();
        $response = array(
            'code'    => 0,
            'message' => 'welcome to api!' ,
            'value'   => ''
        );

        $this->ajaxReturn($response);
    }
    
}
