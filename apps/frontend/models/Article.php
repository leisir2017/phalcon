<?php

namespace Apps\Frontend\Models;


# 模型表
class Article extends \Phalcon\Mvc\Model {
    # 状态码

    protected $code = 0;

    # 返回消息体
    protected $message = array(
        '成功', #0
        '服务器繁忙', #1
        '缺少必要条件', #2
        '不存在的信息', #3
        '缺少必要参数',#4
        '请填写标题',#5
        '请选择分类',#6
        '没有上传图片',#7
    );

    # 结果集
    protected $response = array();

    # 总条数
    protected $total = 0;

    # 获取配置文件

    public function getConfigs($obj = '') {
        $config = $this->getDI()->getConfig();
        if (!empty($obj))
            return $config[$obj];
        return $config;
    }

    # 表
    public function getSource() {
        return 'article';
    }

    # 表关联

    public function initialize() {

        // $this->belongsTo("cateid", "ArticleCate", "id", array("alias" => "ArticleCate"));
    }


    
    # 列表
    public function getList($params = array()) {

        $where = '1=1';
        $order = 'id desc';
        if (isset($params['cid']) and ! isset($params['childcid'])) {
            $cid = NewsCate::find(['cid = ' . $params['cid'], 'columns' => 'id,cid']);
            $cids[] = $params['cid'];
            if ($cid) {
                foreach ($cid as $c => $cv) {
                    $cids[] = $cv->id;
                }
            }
            $where .= ' and cateid in(' . implode(',', $cids) . ')';
        }

        if (isset($params['childcid']))
            $where .= ' and cateid in(' . $params['childcid'] . ')';

        if (isset($params['keywords']))
            $where .= ' and title like "%' . $params['keywords'] . '%"';

        if (isset($params['position']))
            $where .= ' and position like "%' . $params['position'] . '%"';


        if (isset($params['other']))
            $where .= ' and id <>' . $params['other'];

        if (isset($params['order'])) {
            # 排序 最新上传 默认
            if (intval($params['order']) == 1)
                $order = ' id desc ';
            # 排序 最多下载
            if (intval($params['order']) == 2)
                $order = ' downloads desc';
            # 排序 最多收藏
            if (intval($params['order']) == 3)
                $order = ' collectcount desc';
            # 排序 最多浏览
            if (intval($params['order']) == 4)
                $order = ' views desc';
            # 排序 最多点赞
            if (intval($params['order']) == 5)
                $order = ' zancount desc';
            # 排序 最多评论
            if (intval($params['order']) == 5)
                $order = ' commentcount desc';
            # 排序 推荐
            if (intval($params['order']) == 6)
                $order = ' ismymodel desc';
        }

        $pagesize = isset($params['pagesize']) ? intval($params['pagesize']) : $this->getConfigs('pagesize');
        
        $nowpage = isset($params['page']) ? intval($params['page']) : 1;

        $list = self::find(array($where, "order" => $order, "limit" => $pagesize, "offset" => ($nowpage - 1) * $pagesize));
        if ($list) {


            $lists = $list->toArray();

            foreach ($list as $key => $value) {




                $lists[$key]['time'] = date('Y-m-d H:i:s', $value->time);
                $lists[$key]['timemonth'] = date('m-d', $value->time);

                $lists[$key]['catename'] = "";

                if (isset($value->NewsCate)) {

                    $lists[$key]['catename'] = $value->NewsCate->name;
                }
                if (isset($value->User)) {

                    $lists[$key]['username'] = $value->User->username;
                }
            }
        }
        # 总条数
        $this->getTotal($where);
        $this->response = $lists;

        return $this->response;
    }

    public static function detail($id)
    {
        return self::findFirstByid($id);
    }


    # 获取条数
    public function getTotal($condition = array()) {
        if (empty($condition))
            return 0;

        $number = self::count($condition);

        $this->total = intval($number);

        return intval($number);
    }

    # 获取每页显示的条数

    public function getPageSize() {
        return $this->getConfigs('pagesize');
    }

    # 获取列表总条数

    public function getPageTotal() {
        return $this->total;
    }

    # 设置消息体

    public function getMessage() {
        return $this->message[$this->code];
    }

    # 设置消息状态码

    public function getCode() {
        return $this->code;
    }

    # 设置请求结果集

    public function getResponse() {
        return $this->response;
    }

}
