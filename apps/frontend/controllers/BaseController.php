<?php

namespace Apps\Frontend\Controllers;

use Phalcon\Mvc\Controller;

class BaseController extends Controller
{
    protected $_params = array();
    

    public function initialize()
    {

    	$this->setParams();
    }

    # 参数初始化
    private function setParams()
    {
        $params = array();

        if( is_array($params) ){

            switch (true)
            {
                case $this->request->isPost(): $params = array_merge($params, $this->request->getPost());break;
                case $this->request->isDelete(): $params = $params;break;
                case $this->request->isPut(): $params = array_merge($params, $this->request->getPut());break;
                case $this->request->isGet(): $params = array_merge($params, $this->request->getQuery());break;
                default:$params = $params ; break;
            }
        }

        $getParams = $this->router->getParams();
        $params = array_merge($params,$getParams);

        $this->_params = $params;
    }

    protected function makeParams( $params = array() )
    {
        $length = count($params);

        if( $length % 2 != 0 )
        {
            $params[$length] = 0;
            $length++;
        }
        $array = array();
        for( $i = 0; $i < $length; $i += 2 ){
            if(isset($params[$i]))  {

                @$array[$params[$i]] = $this->special_filter(htmlspecialchars($params[$i + 1], ENT_QUOTES));
            }
        }
        unset($params);

        return $array;
    }

    /*
     * 返回当前地址 用于分页
     * */
    protected function makePageUrl ( $params = array() ,$unset = array() )
    {
        $params = array_merge($this->request->getQuery(),$params);
        $controllerName = strtolower($this->dispatcher->getControllerName());
        $actionName = strtolower($this->dispatcher->getActionName());
        $nowurl =$controllerName . '/' . $actionName ;
        $url = isset($params['_url']) ? $params['_url'] : $nowurl;

        if( isset($params['_url']) )
            unset($params['_url']);

        if(!empty($unset)){
            foreach($unset as $u)
                unset($params[$u]);
        }
        $queryUrl = "";
        foreach($params as $k=>$v){
            if(empty($queryUrl))
                $queryUrl .= "?".$k . '=' . $v . '&';
            else
                $queryUrl .= $k . '=' . $v . '&';
        }

        if( isset($queryUrl[strlen($queryUrl)-1]) and $queryUrl[strlen($queryUrl)-1] == '&')
            $queryUrl = substr($queryUrl,0,strlen($queryUrl)-1);
        if(count($params)<1){
            $queryUrl .= '?';
        }else{
            $queryUrl .= '&';
        }
        if(isset($params['page']) and $params['page']=="{page}")
            $queryUrl = substr($queryUrl,0,strlen($queryUrl)-1);

        $url .= $queryUrl;

        return $url;
    }

}
