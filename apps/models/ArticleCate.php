<?php

class ArticleCate extends \Phalcon\Mvc\Model {
    # 状态码

    protected $code = 0;

    # 返回消息体
    protected $message = array(
        '成功', #0
        '服务器繁忙', #1
        '缺少必要条件', #2
        '不存在的信息', #3
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
        return 'article_cate';
    }

    # 表关联

    public function initialize() {

        $this->hasMany("id","Article","cateid",array("alias"=>"Article"));
    }

    # 列表

    public function getList($params = array()) {

        $where = '1=1';
        if (!isset($params['cid']))
            $where .= ' and cid = 0';
        else
            $where .= ' and cid = ' . $params['cid'];
        if (isset($params['keywords']))
            $where .= ' and name like "%' . $params['keywords'] . '%"';
        if (isset($params['postion']))
            $where .= ' and postion like "%' . $params['postion'] . '%"';

        $pagesize = $this->getConfigs('pagesize');
        $pagesize = isset($params['pagesize']) ? intval($params['pagesize']) : $pagesize;
        $nowpage = isset($params['page']) ? intval($params['page']) : 1;
        $order = isset($params['order']) ? $params['order'] : 'id desc';
        $lists = NewsCate::find(array($where, "order" => $order, "limit" => $pagesize, "offset" => ($nowpage - 1) * $pagesize));


//        if(isset($params['tree']) and $params['tree'] == 1 and !isset($params['cid']))
//            $lists = getTree($lists, $fid = 0, $id_p = 'id', $fid_p = "cid");
        # 总条数
        $this->getTotal($where);
        $this->response = $lists;
        return $lists;
    }

    # 列表 用于首页展示
    public function getHomeList($params)
    {
        $where = '1=1';
        if (isset($params['cid']))
            $where .= ' and cid = ' . $params['cid'];
        if (isset($params['keywords']))
            $where .= ' and name like "%' . $params['keywords'] . '%"';

        if (isset($params['position']))
            $where .= ' and position like "%' . $params['position'] . '%"';

        $pagesize = $this->getConfigs('pagesize');
        $pagesize = isset($params['pagesize']) ? intval($params['pagesize']) : $pagesize;
        $nowpage = isset($params['page']) ? intval($params['page']) : 1;
        $order = isset($params['order']) ? $params['order'] : 'id desc';
        $lists = NewsCate::find(array($where, "order" => $order, "limit" => $pagesize, "offset" => ($nowpage - 1) * $pagesize));
        # 总条数
        $this->getTotal($where);
        $this->response = $lists;
        return $lists;
    }

    # 详情 用于用户编辑是查询

    public function detail($number = null) {
        if (empty($number))
            return $this->code = 2;
        $info = Model::findFirst("number={$number}");
        if ($info) {
            $infos = $info->toArray();
            $infos['buytype'] = 0;

            if ($info->price != '')
                $infos['buytype'] = 1;
            if ($info->integral != '')
                $infos['buytype'] = 2;

            $id = $info->id;
            unset($infos['checked']);
            $user = $info->User;
            $infos['username'] = isset($user->username) ? $user->username : '';
            $infos['avatar'] = isset($user->avatar) ? $user->avatar : '';
            $infos['validate'] = isset($user->validate) ? intval($user->validate) : 0;
            $infos['usernumber'] = isset($user->idcode) ? $user->idcode : 0;

            if (isset($user->validate) and $user->validate == 1)
                $infos['usertype'] = @$user->type;
            $infofiles = ModelFile::find(array("modelid = {$id}", "order" => "id desc"));
            $infoimgs = ModelImg::find(array("modelid = {$id}", "order" => "id desc"));
            $tags = ModelTag::find(array("modelid = {$id}", "order" => "id desc"));
            $infos['tags'] = array();
            if ($tags) {
                foreach ($tags as $f)
                    $infostags[] = $f->tagname;

                $infostags = array_unique($infostags);
                $infostags = array_filter($infostags);
                $infos['tags'] = $infostags;
            }
            $infos['slug'] = "";
            $infos['catename'] = "";
            if (isset($info->modelcate)) {
                $infos['slug'] = $info->modelcate->slug;
                $infos['catename'] = $info->modelcate->name;
                if ($info->modelcate->cid > 0) {
                    $topcate = ModelCate::findFirst(array("id = {$info->modelcate->cid}", "columns" => "slug,name"));

                    $infos['childslug'] = $info->modelcate->slug;
                    $infos['childcatename'] = $info->modelcate->name;

                    $infos['slug'] = $topcate->slug;
                    $infos['catename'] = $topcate->name;
                }
            }

            $infos['files'] = $infofiles->toArray();
            $infos['total_volume'] = 0;
            $infos['total_surface'] = 0;
            if (count($infos['files']) > 0) {
                foreach ($infos['files'] as $f) {
                    $f['volume'] = $f['volume'] ? floatval($f['volume']) : 0;
                    $f['surface'] = $f['surface'] ? floatval($f['surface']) : 0;
                    $infos['total_surface'] += $f['surface'];
                    $infos['total_volume'] += $f['volume'];
                }
            }
            $infos['imgs'] = $infoimgs->toArray();

            return $this->response = $infos;
        } else
            return $this->code = 3;
    }

    # 详情

    public function info($number = null, $id = null) {
        if (empty($id) and empty($number))
            return $this->code = 2;
        if ($id)
            $info = Model::findFirstByid($id);
        else
            $info = Model::findFirstBynumber($number);

        if ($info) {
            $info->views += 1;
            $info->save();
            $infos = $info->toArray();
            $infos['buytype'] = 0;

            if (floatval($info->price) > 0)
                $infos['buytype'] = 1;
            elseif (floatval($info->integral) > 0)
                $infos['buytype'] = 2;

            $id = $info->id;

            $user = $info->User;
            $infos['idcode'] = isset($user->idcode) ? $user->idcode : '';
            $infos['username'] = isset($user->username) ? $user->username : '';
            $infos['avatar'] = isset($user->avatar) ? $user->avatar : '';
            $infos['validate'] = isset($user->validate) ? intval($user->validate) : 0;
            $infos['usernumber'] = isset($user->idcode) ? $user->idcode : 0;

            if (isset($user->validate) and $user->validate == 1)
                $infos['usertype'] = @$user->type;
            $infofiles = ModelFile::find(array("modelid = {$id}", "order" => "id desc"));
            $infoimgs = ModelImg::find(array("modelid = {$id}", "order" => "id desc"));

            $infos['slug'] = "";
            $infos['catename'] = "";
            if (isset($info->modelcate)) {
                $infos['slug'] = $info->modelcate->slug;
                $infos['catename'] = $info->modelcate->name;
            }

            $infos['files'] = $infofiles->toArray();
            $infos['total_volume'] = 0;
            $infos['total_surface'] = 0;
            if (count($infos['files']) > 0) {
                foreach ($infos['files'] as $key => $f) {
                    $f['volume'] = $f['volume'] ? floatval($f['volume']) : 0;
                    $f['surface'] = $f['surface'] ? floatval($f['surface']) : 0;
                    $infos['total_surface'] += $f['surface'];
                    $infos['total_volume'] += $f['volume'];

                    if (strstr(strtolower($f['size']), 'b') !== false)
                        $infos['files'][$key]['size'] = $f['size'];
                    else {
                        if ($f['size'] / 1024 > 1000)
                            $infos['files'][$key]['size'] = sprintf("%.2f", ($f['size'] / 1024 / 1024)) . 'MB';
                        else
                            $infos['files'][$key]['size'] = sprintf("%.2f", ($f['size'] / 1024)) . 'KB';
                    }
                }
            }
            $infos['imgs'][0] = $infos['img'];

            $imglist = $infoimgs->toArray();
            foreach ($imglist as $i => $m) {
                if ($m['img'] == $infos['img']) {
                    $infos['imgs'][0] = $m;
                    unset($imglist[$i]);
                }
            }


//            $infos['imgs'][] = $infoimgs->toArray();

            $infos['imgs'] = array_merge($infos['imgs'], $imglist);

            $infos['candown'] = 0;


            if (is_login($this->getDI())) {
                $user = $this->getDI()->getSession()->get("user");

                if ($this->needpay($info->id)) {
                    $resttime = 0;
                    $activetime = intval(\Apps\Commons\Models\SysSetting::setting("activetime")) * 3600;
                    $builder = $this->getModelsManager()->createBuilder();
                    $modelinfo = $builder->addfrom("\\Apps\\Commons\\Models\\Orders", "Orders")
                            ->leftJoin("\\Apps\\Commons\\Models\\OrderInfo", "OrderInfo.orderid = Orders.id", "OrderInfo")
                            ->where("Orders.buid = :buid:", ["buid" => $user['id']])
                            ->andWhere("Orders.status = :status:", ["status" => 5])
                            ->andWhere("OrderInfo.modelid = :modelid:", ["modelid" => $info->id])
                            ->orderBy("Orders.id desc")
                            ->getQuery()
                            ->execute();

                    if ($modelinfo != false and count($modelinfo->toArray()) > 0) {
                        $modelinfo = $modelinfo->toArray();
                        $result = $modelinfo[0];
                        $closetime = $result["closetime"];
                        $endtime = $activetime + strtotime($closetime);
                        $nowtime = time();
                        if ($endtime > $nowtime) {
                            $infos['candown'] = 1;
                            $resttime = $endtime - $nowtime;
                        }
                    }
                    $t_time = comptime($resttime);
                    if ($t_time["d"] > 3)
                        $totaltime = $t_time["d"] . "天";
                    else
                        $totaltime = $t_time["h"] . "时 " . $t_time["m"] . "分 " . $t_time["s"] . "秒";
                    $infos['resttime'] = $totaltime;

//                    $candown = $this->candown($info->id, $user['id']);
//                    $infos['candown'] = $candown;
//                    if($candown>0){
//                        #可以下载
//                        $infos['candown'] = 1;
//                    }
//                    else{
//                        #您尚未购买此模型或已超过下载时间
//                        $infos['candown'] = 0;
//                    }
                }
                else {
                    $candown = $this->pointdown($info->number, $user['id']);
                    $infos['candown'] = $candown;
                    if ($candown == 4) {
                        $resttime = 0;
                        $syset = SysSetting::findFirst();

                        $activetime = intval($syset->activetime) * 3600;
                        $endtime = time() - $activetime;
                        $enddowntime = date("Y-m-d H:i:s", $endtime);
                        $result = Score::findFirst(array("uid=" . $user['id'] . " and reason=13 and note=" . $info->number . " and addtime >'" . $enddowntime . "'", "order" => "addtime desc"));
                        if ($result != false) {
                            $closetime = $result->addtime;
                            $endtime = $activetime + strtotime($closetime);
                            $nowtime = time();
                            if ($endtime > $nowtime) {
                                $infos['candown'] = 1;
                                $resttime = $endtime - $nowtime;
                            } else {
                                $infos['candown'] = 1;
                            }
                        }



                        $t_time = comptime($resttime);
                        if ($t_time["d"] > 3)
                            $totaltime = $t_time["d"] . "天";
                        else
                            $totaltime = $t_time["h"] . "时 " . $t_time["m"] . "分 " . $t_time["s"] . "秒";
                        $infos['resttime'] = $totaltime;
                    }
//                    if($candown>0){
//                        # 可以下载
//                        $infos['candown'] = 1;
//                    }
//                    else{
//                        #您的积分不够下载此模型
//                        $infos['candown'] = 0;
//                    }
                }
            }else {
                if (floatval($infos['price']) <= 0 and floatval($infos['integral']) <= 0) {
                    $infos['candown'] = 1;
                }
            }


            return $this->response = $infos;
        } else
            return $this->code = 3;
    }

    # 模型文件列表

    public function modelfiles($number = null) {
        if (empty($number))
            return $this->code = 2;
        $info = Model::findFirst("number={$number}");
        if ($info) {
            $infos['modeltitle'] = $info->title;
            $infos['modelnumber'] = $info->number;
            $infos['modelid'] = $info->id;
            $infos['modelcate'] = $info->cateid;
            $infos['price'] = $info->price;
            $infos['edittime'] = $info->edittime;
            $infos['integral'] = $info->integral;
            $id = $info->id;
            $infos['candown'] = 0;
            $infofiles = ModelFile::find(array("modelid = {$id}", "order" => "id desc"));

            $infos['files'] = $infofiles->toArray();
            foreach ($infos['files'] as $key => $value) {
                $infos['files'][$key]['timer'] = formatTime($value['addtime']);
                if (strstr(strtolower($value['size']), 'b') !== false)
                    $infos['files'][$key]['size'] = $value['size'];
                else {
                    if ($value['size'] / 1024 > 1000)
                        $infos['files'][$key]['size'] = sprintf("%.2f", ($value['size'] / 1024 / 1024)) . 'MB';
                    else
                        $infos['files'][$key]['size'] = sprintf("%.2f", ($value['size'] / 1024)) . 'KB';
                }

                unset($infos['files'][$key]['jsonpath']);
                unset($infos['files'][$key]['filestream']);
                unset($infos['files'][$key]['md5']);
            }

            if (is_login($this->getDI())) {
                $user = $this->getDI()->getSession()->get("user");
                if ($this->needpay($info->id)) {
                    $candown = $this->candown($info->id, $user['id']);
                    if ($candown > 0) {
                        $infos['candown'] = 1;
                    } else {
                        $infos['candown'] = 0;
                    }
                } else {
                    $candown = $this->pointdown($info->number, $user['id']);
                    if ($candown > 0) {
                        $infos['candown'] = 1;
                    } else {
                        $infos['candown'] = 0;
                    }
                }
            } else {
                if (floatval($info->price) <= 0 and floatval($info->integral) <= 0)
                    $infos['candown'] = 1;
            }

            return $this->response = $infos;
        } else
            return $this->code = 3;
    }

    # 设置字段值+1 浏览 点赞 收藏

    public function setVal($id, $params = array()) {
        if (!$id) {
            return $this->code = 2;
        }
        if (!isset($params['key'])) {
            return $this->code = 4;
        }
        $user = $this->getDI()->getSession()->get("user");
        $uid = $user['id'];
        $val = 'zancount';
        # 点赞 +1 -1
        if (isset($params['key']) and strtolower($params['key']) == 'zan') {
            $val = 'zancount';
            $isZan = ModelZan::findFirst(array("uid={$uid} and modelid={$id}"));
            if ($isZan != false) {
                $info = Model::findFirst($id);
                if ($info) {
                    $info->$val -= 1;
                    $info->save();
                    $isZan->delete();
                    return true;
                }
            }
        }
        # 收藏
        if (isset($params['key']) and strtolower($params['key']) == 'collect') {
            $val = 'collectcount';
            if (!isset($params['albumid']))
                return $this->code = 25;
            else {
                $hasAlubm = ModelAlubm::count(array("uid={$uid} and id={$params['albumid']}"));
                if ($hasAlubm < 1)
                    return $this->code = 26;
            }
        }
        # 评论
        if (isset($params['key']) and strtolower($params['key']) == 'comment') {
            $val = 'commentcount';
        }
        # 被制作数
        if (isset($params['key']) and strtolower($params['key']) == 'make') {
            $val = 'makecount';
        }
        $info = Model::findFirst($id);
        if ($info) {
            $info->$val += 1;
            $rs = $info->save();
            if (!$rs)
                return $this->code = 1;
            else {

                if ($val == 'zancount') {
                    $isLog = ModelZan::count(array("uid={$uid} and modelid={$info->id}"));
                    if ($isLog < 1) {
                        $log = new ModelZan();
                        $log->uid = $uid;
                        $log->modelid = $info->id;
                        $log->addtime = date("Y-m-d H:i:s", time());
                        $log->save();
                    }
                }
                if ($val == 'collectcount') {
                    $isLog = ModelCollect::count(array("uid={$uid} and modelid={$info->id} and albumid={$params['albumid']}"));
                    if ($isLog < 1) {
                        $log = new ModelCollect();
                        $log->uid = $uid;
                        $log->modelid = $info->id;
                        $log->albumid = $params['albumid'];
                        $log->addtime = date("Y-m-d H:i:s", time());
                        $log->save();
                        # 该收藏夹的模型数量
                        $modelAlbumCount = ModelCollect::count(array("uid={$uid} and albumid={$params['albumid']}"));
                        $alubm = ModelAlubm::findFirst(array("id={$params['albumid']}"));
                        $alubm->modelcount = $modelAlbumCount;
                        $alubm->save();
                    }
                }
            }
        } else {
            return $this->code = 3;
        }
    }

    # 保存

    public function saveData($params = array()) {
        if (!isset($params['cateid']))
            return $this->code = 5;
        if (!isset($params['title']))
            return $this->code = 6;
        if (!isset($params['img'])) {
            return $this->code = 7;
        }

        if (floatval($params['price']) <= 0 and floatval($params['integral']) <= 0) {
            return $this->code = 28;
        }


        # 用于修改模型文件表和图片表的数据
        $fileids = $params['fileids'];
        $imgids = $params['imgids'];
        $tags = $params['tags'];
        unset($params['fileids']);
        unset($params['imgids']);
        unset($params['tags']);
        if ($fileids and $imgids) { //文件id集和图片id集
            if (!is_array($fileids)) {
                $fileids = explode(',', $fileids);
                $fileids = array_unique($fileids);
                $fileids = array_filter($fileids);
            }
            if (!is_array($imgids)) {
                $imgids = explode(',', $imgids);
                $imgids = array_unique($imgids);
                $imgids = array_filter($imgids);
            }
        }
        if ($tags) {
            if (!is_array($tags)) {
                $tags = explode(',', $tags);
                $tags = array_unique($tags);
                $tags = array_filter($tags);
            }
        }
        # edit
        if (isset($params['id']) and intval($params['id']) > 0) {
            $info = Model::findFirst("id=" . $params['id']);
            if (!$info) {
                return $this->code = 3;
            } else {
                unset($params['id']);
                foreach ($params as $key => $value) {
                    $info->$key = $value;
                }
                $rs = $info->save();
                if ($rs != false) {
                    #更新关联表数据
                    $this->updateModelFile($info->id, $fileids, $imgids, $tags);
                    return $this->response = $info->id;
                } else {
                    return $this->code = 1;
                }
            }
            # add
        } else {
            $mModel = new Model();
            foreach ($params as $key => $value) {
                $mModel->$key = $value;
            }
            $user = $this->getDI()->getSession()->get('user');
            $uid = $user['id'];
            $mModel->uid = $uid;
            $mModel->number = idCode();
            $mModel->addtime = date('Y-m-d H:i:s', time());
            $rs = $mModel->save();

            if ($rs != false) {
                #更新关联表数据
                $this->updateModelFile($mModel->id, $fileids, $imgids, $tags);
                return $this->response = $mModel->id;
            } else {
                return $this->code = 1;
            }
        }
    }

    # 更新表

    protected function updateModelFile($modelid = 0, $files = array(), $imgs = array(), $tags = array()) {
        $manager = $this->getModelsManager();

        if ($modelid and ! empty($files)) {
            foreach ($files as $key => $value) {
                $m = ModelFile::findFirst("id=" . $value);
                $m->modelid = $modelid;
                $m->save();
            }
        }
        if ($modelid and ! empty($imgs)) {
            foreach ($imgs as $key => $value) {
                $m = ModelImg::findFirst("id=" . $value);
                if ($m) {
                    $m->modelid = $modelid;
                    $m->save();
                }
            }
        }
        if ($modelid and ! empty($tags)) {
            $taglist = ModelTag::find(array("modelid=" . $modelid));
            if ($taglist) {
                foreach ($taglist as $value)
                    $value->delete();
            }
            foreach ($tags as $key => $value) {
                $ModelTag = new ModelTag();
                $ModelTag->modelid = $modelid;
                $ModelTag->tagname = $value;
                $ModelTag->addtime = date("Y-m-d H:i:s", time());
                $ModelTag->save();
            }
        }
    }

    # 个人中心列表

    public function getMyList($params = array()) {

        $where = '1=1';
        $order = 'id desc';
        $user = $this->getDI()->getSession()->get('user');
        $id = $user['id'];
        if (isset($params['idcode'])) {
            $u = User::findFirst(array("idcode={$params['idcode']}"));
            if ($u != false)
                $id = $u->id;
        }
        if (isset($params['cate']))
            $where .= ' and cateid =' . $params['cate'];
        if (isset($params['keywords']))
            $where .= ' and title like "%' . $params['keywords'] . '%"';
        if (isset($params['position']))
            $where .= ' and isrecommend =' . $params['position'];

        if (isset($params['checked'])) {
            #审核通过
            if (intval($params['checked']) == 2)
                $where .= ' and checked= 2 ';
            #审核中
            if (intval($params['checked']) == 0)
                $where .= ' and checked= 0 ';
            #审核不通过
            if (intval($params['checked']) == 1)
                $where .= ' and checked= 1 ';
        }
        $where .= 'and uid = ' . $id . '';
        $pagesize = $this->getConfigs('pagesize');
        $pagesize = isset($params['pagesize']) ? intval($params['pagesize']) : $pagesize;
        $nowpage = isset($params['page']) ? intval($params['page']) : 1;

        $lists = Model::find(array($where, "order" => $order, "limit" => $pagesize, "offset" => ($nowpage - 1) * $pagesize));
        if ($lists) {
            $lists = $lists->toArray();
            foreach ($lists as $key => $value) {
                unset($lists[$key]['description']);
                unset($lists[$key]['orgprice']);
                unset($lists[$key]['uid']);
                unset($lists[$key]['cateid']);
                unset($lists[$key]['makecount']);
                unset($lists[$key]['ismymodel']);
                unset($lists[$key]['share']);
                unset($lists[$key]['opinion']);

                $lists[$key]['timer'] = time_ago(strtotime($value['addtime']));
                $lists[$key]['img'] = $value['img'];
            }
        }
        # 总条数
        $this->getTotal($where);
        $this->response = $lists;
    }

    # 获取资源总数 和 近30天交易总额

    public function getTotalData($condition = array()) {
        if (empty($condition))
            $time = 30;
        if (isset($condition['time']))
            $time = $condition['time'];

        # 资源总数
        $total = Model::count(array("checked=2"));
        # 近30天交易总额
        $e = date("Y-m-d H:i:s", time());
        $s = date('Y-m-d H:i:s', strtotime('-1 month'));

        $totalprice = FinanceRecord::sum(
                        array(
                            "column" => "amount",
                            "conditions" => "addtime >= '{$s}' and addtime <= '{$e}'"
                        )
        );

        $this->response = array("totalnumber" => (int) $total, "totalprice" => (float) $totalprice);
    }

    # 模型 下载单个模型

    public function download($params = array()) {
        if (!is_login($this->getDI()))
            return $this->code = 8;
        $uinfo = $this->getDI()->getSession()->get("user");
        # $uinfo['id'] = 4953; //测试
        if (!isset($params['modelnumber']))
            return $this->code = 2;

        if (!isset($params['filenumber'])) {
            return $this->code = 11;
        }
        $number = $params['modelnumber'];
        $model = Model::findFirst("number=" . $number . "");
        if (!$model) {
            return $this->code = 3;
        }

        if ($this->needpay($model->id)) {
            $candown = $this->candown($model->id, $uinfo['id']);
            if ($candown != false) {
                $restls = ModelFile::findFirst(array("number=" . $params['filenumber'] . ""));
                $data['modelid'] = $model->id;
                $data['fileid'] = $restls->id;
                $data['stl'] = $restls->stl;
                $data['filename'] = $restls->filename;
                $data['uid'] = $uinfo['id'];
                $this->response = $data;
            } else {
                return $this->code = 9;
            }
        } else {
            $pointdown = $this->pointdown($model->number, $uinfo['id']);
            if ($pointdown == -1) {
                return $this->code = 10;
            } elseif ($pointdown == 2) {
                $scoreObj = new Score();
                $scoreObj->addrecord($uinfo['id'], 13, $model->number);
                $scoreObj->addrecord($model->uid, 12);
            }

            $restls = ModelFile::findFirst(array("number=" . $params['filenumber'] . ""));
            $data['modelid'] = $model->id;
            $data['fileid'] = $restls->id;
            $data['filename'] = $restls->filename;
            $data['stl'] = $restls->stl;
            $data['uid'] = $uinfo['id'];

            /* 下载量 */
            $restls->downloads = intval($restls->downloads) + 1;
            $restls->save();
            $downloadNum = $model->downloads;
            $model->downloads = intval($downloadNum) + 1;
            $model->save();
            $this->response = $data;
        }
    }

    # 模型 下载模型打包zip

    public function downloadzip($params = array()) {
        if (!is_login($this->getDI()))
            return $this->code = 8;
        $uinfo = $this->getDI()->getSession()->get("user");
        # $uinfo['id'] = 4953; //测试
        if (!isset($params['modelnumber']))
            return $this->code = 2;

        $number = $params['modelnumber'];
        $model = Model::findFirst("number=" . $number . "");
        if (!$model)
            return $this->code = 3;
        $allowed = $this->candown($model->id, $uinfo['id']);

        if ($this->needpay($model->id) == false) {
            $pointdown = $this->pointdown($model->number, $uinfo['id']);
            if ($pointdown == -1) {
                return $this->code = 10;
            } elseif ($pointdown == 2) {
                $scoreObj = new Score();
                $scoreObj->addrecord($uinfo['id'], 13, $model->number);
                $scoreObj->addrecord($model->uid, 12);
            }
        }
        if ($allowed != false) {
            //插入下载记录
            $download_record = new DownloadRecord();
            $download_record->uid = $uinfo['id'];
            $download_record->modelid = $model->id;
            $download_record->IP = $_SERVER["REMOTE_ADDR"];
            $download_record->downtime = date("Y-m-d H:i:s", time());
            $download_record->save();
        }

        $zipcount = ModelZip::count(array("modelid=" . $model->id . ""));

        $config = $this->getDI()->getConfig();
        $basePath = $config['resourceSave'];
        $zippath = $basePath . 'zipstls/';

        if ($zipcount > 0) {
            $mxzip = ModelZip::findFirst("modelid=" . $model->id);
            $ziptime = strtotime($mxzip->addtime);
            $edittime = strtotime($model->edittime);
            if ($ziptime < $edittime) {
                @unlink($basePath . $mxzip->path);
                $mxzip->delete();
                $zipcount = 0;
            }
        }

        if ($zipcount < 1) {
            $restls = ModelFile::find(array("modelid=" . $model->id . ""));
            $stls = array();
            foreach ($restls as $sk => $sv) {
                $stls[] = $basePath . $sv->stl;
            }

            $allzippath = $zippath . date("Ymd") . "/";
            if (!is_dir($zippath . date("Ymd") . "/")) {
                $mk = mkdir($zippath . date("Ymd") . "/", 0755);
                if (!$mk)
                    return $this->code = 12;
            }

            $zipname = rand(100, 999) . $number . ".zip";
            $destination = $allzippath . $zipname;
            $stls[] = $basePath . 'dayinpai.txt';

            $zip = create_zip($stls, $destination, false);
            if ($zip == false) {
                return $this->code = 11;
            }
            //插入数据库
            $mxzip = new ModelZip();
            $mxzip->path = $zipfile = 'zipstls/' . date("Ymd") . "/" . $zipname;
            $mxzip->size = getFileSize($destination);
            $mxzip->modelid = $model->id;
            $mxzip->addtime = date("Y-m-d H:i:s", time());
            $mxzip->save();
            $this->response = $zipfile;
        } else {

            if (!file_exists($basePath . $mxzip->path)) {
                $restls = ModelFile::find(array("modelid=" . $model->id . ""));
                $stls = array();

                foreach ($restls as $sk => $sv) {
                    $stls[] = $basePath . $sv->stl;
                }
                $allzippath = $zippath . date("Ymd") . "/";
                if (!is_dir($zippath . date("Ymd") . "/")) {
                    $mk = mkdir($zippath . date("Ymd") . "/", 0755);
                    if (!$mk)
                        return $this->code = 12;
                }


                $zipname = rand(100, 999) . $number . ".zip";
                $destination = $allzippath . $zipname;
                $stls[] = $basePath . 'dayinpai.txt';
                $zip = create_zip($stls, $destination, false);
                if ($zip == false) {
                    return $this->code = 11;
                }
                $mxzip->path = 'zipstls/' . date("Ymd") . "/" . $zipname;
                $mxzip->addtime = date("Y-m-d H:i:s", time());
                $mxzip->save();
            }
            $zipfile = $basePath . $mxzip->path;
            $this->response = $zipfile;
        }


        /* 更新下载量 */
        $model->downloads = intval($model->downloads) + 1;
        $model->save();
    }

    # 判断是否是付费模型

    public function needpay($modelid) {
        $model = Model::findFirst("id=" . $modelid);
        if (!$model)
            return false;
        $price = floatval($model->price);
        if ($price == 0) {
            return false;
        }
        return true;
    }

    # 判断是否能够下载

    public function candown($modelid, $uid) {
        $model = Model::findFirst("id=" . $modelid);
        if ($model->uid == $uid) {
            return true;
        }

        $price = floatval($model->price);
        $syset = SysSetting::findFirst();
        $activetime = intval($syset->activetime) * 3600;

        $endtime = time() - $activetime;
        $enddowntime = date("Y-m-d H:i:s", $endtime);
        if ($price == 0) {
            return true;
        }

        $bind['buid'] = $uid;
        $bind['modelid'] = $modelid;
        $bind['closetime'] = $enddowntime;

        $builder = $this->getModelsManager()->createBuilder();

        $result = $builder->addfrom("\\Apps\\Commons\\Models\\Orders", "Orders")
                ->leftJoin("\\Apps\\Commons\\Models\\OrderInfo", "OrderInfo.orderid = Orders.id", "OrderInfo")
                ->where("Orders.buid = :buid:", ["buid" => $uid])
                ->andWhere("OrderInfo.modelid = :modelid:", ["modelid" => $modelid])
                ->andWhere("Orders.closetime > :endtime:", ["endtime" => $enddowntime])
                ->getQuery()
                ->execute();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    # 判断是否积分足够下载

    public function pointdown($modelnumber, $uid) {
        $model = Model::findFirst("number=" . $modelnumber . "");
        if ($model->uid == $uid) {
            return 1; //允许下载
        }
        $model_cate = ModelCate::findFirst(array("slug='3dshipin'"));
        if ($model_cate != false) {
            $cateids = array();
            $cateids[] = $model_cate->id;
            $model_cates = ModelCate::find(array("cid=" . $model_cate->id . ""));
            foreach ($model_cates as $modelcate) {
                $cateids[] = $modelcate->id;
            }
            if (in_array($model->cateid, $cateids)) {
                return 1;
            }
        }

        $syset = SysSetting::findFirst();

        $activetime = intval($syset->activetime) * 3600;
        $endtime = time() - $activetime;
        $enddowntime = date("Y-m-d H:i:s", $endtime);
        $score = Score::findFirst(array("uid=" . $uid . " and reason=13 and note=" . $modelnumber . " and addtime >'" . $enddowntime . "'", "order" => "addtime desc"));

        if ($score != false) {
            return 4; //允许下载的时间内，可下载
        }
        $scoreObj = new Score();
        $downscore = abs(intval($scoreObj->getscore(13)));
        $user = User::findFirst("id=" . $uid);
        $integral = intval($user->integral);
        if ($integral >= $downscore) {
            return 2; //积分够，可下载
        } else {
            return -1; //积分不够，不可下载
        }
    }

    # 购买模型主入口

    public function buyModel($params, $db) {
        if (!isset($params['payway']) or intval($params['payway'] < 1))
            return $this->code = 18;
        switch (intval($params['payway'])) {
            case 1: # 派币购买
                $this->buyModelForScore($params);
                break;
            case 2: # 余额购买
                $this->buyModelForYe($params);
                break;
            case 3: # 支付宝购买
                $this->buyModelForAlipay($params, $db);
                break;
            case 4: # 微信购买
                $this->buyModelForWx($params, $db);
                break;
            case 5: # 银联购买
                $this->buyModelForYl($params, $db);
                break;
            case 6: # 财付通
                $this->buyModelForCft($params, $db);
                break;
        }
    }

    # 派币购买模型

    public function buyModelForScore($params) {

        if (!isset($params['modelnumber']))
            return $this->code = 15;
        if (!is_login($this->getDI()))
            return $this->code = 8;

        $modelunmber = $params['modelnumber'];
        $uinfo = $this->getDI()->getSession()->get("user");

        $model = Model::findFirst("number=" . $modelunmber . "");
        if (!$model)
            return $this->code = 2;
        if (intval($model->integral) <= 0)
            return $this->code = 14;
        $scoreObj = new Score();
        # 用户总积分
        $userScore = $scoreObj->totalscores($uinfo['id']);
        if (intval($userScore) < intval($model->integral))
            return $this->code = 13;

        # 购买者减派币
//        $scoreObj->addrecord($uinfo['id'], 18, $modelunmber);
//        # 售卖者加派币
//        $scoreObj->addrecord($model->uid, 17,$modelunmber);

        $scoreObj->addrecord($uinfo['id'], 13, $modelunmber);
        $scoreObj->addrecord($model->uid, 12, $modelunmber);
    }

    # 余额购买模型

    public function buyModelForYe($params) {
        if (!is_login($this->getDI()))
            $this->noLogin();
        if (!isset($params['modelnumber']))
            return $this->code = 15;
        $model = Model::findFirst("number=" . $params['modelnumber']);
        if (!$model)
            return $this->code = 2;
        $user = $this->getDI()->getSession()->get("user");
        $userinfo = User::findFirst("id=" . $user['id']);
        if (empty($userinfo->paypassword) or ! isset($params['paypassword']))
            return $this->code = 19;
        if (md5($params['paypassword']) != $userinfo->paypassword)
            return $this->code = 20;
        $buid = $user['id'];
        $users = FinanceBalance::findFirst("uid=" . $buid);
        if (!$users) {
            $users = new FinanceBalance ();
            $users->uid = $buid;
            $users->availablebalance = 0;
            $users->balance = 0;
            $users->updatetime = date("Y-m-d H:i:s", time());
            $users->save();
        }
        $userBalance = floatval($users->availablebalance);
        if (!$users or $userBalance < floatval($model->price)) {
            $this->response = array();
            return $this->code = 17;
        }

        # 创建订单
        $params['buyForYe'] = 1;
        $this->creatOrder($params);
        # 创建成功 返回订单号 订单金额等
        if ($this->code == 0) {
            $response = $this->response;
            $ordersn = $response['ordersn'];
            $modelprice = $response['modelprice'];
            $buid = $response['buid']; # 购买者id
            $suid = $response['suid']; # 出售者id
            $totalprice = $response['totalprice'];

            # 买家扣除余额
            $balance = floatval($users->balance);
            $availablebalance = floatval($users->availablebalance);
            $nowbalance = $balance - $totalprice;
            $nowavailablebalance = $availablebalance - $totalprice;
            $users->balance = $nowbalance;
            $users->availablebalance = $nowavailablebalance;
            $users->updatetime = date("Y-m-d H:i:s", time());
            $users->save();

            $serialnumber = serialnumber(2);
            # 记账 流水号
            $finance_record = new FinanceRecord();
            $finance_record->uid = $buid;
            $finance_record->serialnumber = $serialnumber;
            $finance_record->amount = $totalprice;
            $finance_record->type = 0;
            $finance_record->obj = 'model';
            $finance_record->status = 1;
            $finance_record->balance = $nowbalance;
            $finance_record->note = '支付订单';
            $finance_record->addtime = date("Y-m-d H:i:s", time());
            $finance_record->save();

            # order status
            $order = Orders::findFirst("ordersn=" . $ordersn);
            $order->status = 5;
            $order->closetime = date("Y-m-d H:i:s", time());
            $r = $order->save();
            if ($r != false) {
                #order info closttime
                $oinfos = OrderInfo::find(array("orderid={$order->id}"));
                if ($oinfos) {
                    foreach ($oinfos as $v) {
                        $v->closetime = date("Y-m-d H:i:s", time());
                        $v->save();
                    }
                }
            }

            # 财务支出记录
            $finance_pay = new FinancePay();
            $finance_pay->recordid = $finance_record->id;
            $finance_pay->uid = $buid;
            $finance_pay->amount = $totalprice;
            $finance_pay->status = 1;
            $finance_pay->note = $ordersn;
            $finance_pay->addtime = date("Y-m-d H:i:s", time());
            $finance_pay->save();

            # 卖家收款操作
            $sUser = FinanceBalance::findFirst("uid=" . $suid);
            if (!$sUser) {
                $sUser = new FinanceBalance ();
                $sUser->uid = $suid;
                $sUser->availablebalance = 0;
                $sUser->balance = 0;
                $sUser->updatetime = date("Y-m-d H:i:s", time());
                $sUser->save();
            }
            $s_nowbalance = floatval($sUser->balance) + floatval($order->amount);
            $sUser->balance = round($s_nowbalance, 2);
            $sUser->updatetime = date("Y-m-d H:i:s", time());
            $sUser->save();

            $finance_frozen = new FinanceFrozen();
            $finance_frozen->orderid = $order->id;
            $finance_frozen->uid = $order->suid;
            $finance_frozen->amount = round(floatval($order->amount), 2);
            $finance_frozen->status = 0;
            $finance_frozen->addtime = date("Y-m-d H:i:s", time());
            $finance_frozen->save();

            $suserinfo = User::findFirst(array("id=" . $suid . ""));
            if (!empty($suserinfo->email)) {
                $email = $suserinfo->email;
                $time = date("Y-m-d H:i:s", time());
                $vars = json_encode(array("to" => array($email), "sub" => array("%time%" => array($time))));
                send_mail($this->getDI()->getConfig(), $vars, 'model_order_sure', '打印派提醒，您有一笔新的模型销售订单');
            }
        } else
            return false;
    }

    # 支付宝支付

    public function buyModelForAlipay($params, $db) {

        $this->creatOrder($params);
        if ($this->code == 0) {
            $response = $this->response;
            $ordersn = $response['ordersn'];
            $modelprice = $response['modelprice'];
            $buid = $response['buid']; # 购买者id
            $suid = $response['suid']; # 出售者id
            $totalprice = $response['totalprice'];
            $where = "buid=" . $buid . " and ordersn in (" . $ordersn . ") and (status=1 or status=0)";
            $ordercount = Orders::count(array(
                        $where
                    ));

            if ($ordercount < 1) {
                return $this->code = 21;
            }
            $orders = Orders::find(array(
                        $where
                    ));
            $totalfee = 0;
            foreach ($orders as $order) {
                $totalfee += (floatval($order->amount) + floatval($order->shippingfee));
            }
            if ($totalfee <= 0) {
                return $this->code = 22;
            }
            $out_trade_no = serialnumber(2);
            $fr = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($fr != false) {
                $out_trade_no = serialnumber(2);
            }
            $bUser = FinanceBalance::findFirst("uid=" . $buid);
            if (!$bUser) {
                $bUser = new FinanceBalance ();
                $bUser->uid = $suid;
                $bUser->availablebalance = 0;
                $bUser->balance = 0;
                $bUser->updatetime = date("Y-m-d H:i:s", time());
                $bUser->save();
            }
            $balance = $bUser->balance;
            $subject = "模型支付"; //订单名称
            $total_fee = round(floatval($totalfee), 2); //付款金额
            $body = "模型支付"; //订单描述
            if ($total_fee == "0.00" || $total_fee == "0") {
                $db->execute("rollback");
                return $this->code = 23;
            }
            $db->execute("set autocommit=0");
            $db->execute("insert into finance_record(uid,serialnumber,amount,status,balance,note,addtime,type,obj) values(" . $buid . ",'" . $out_trade_no . "'," . $total_fee . ",2," . $balance . ",'模型支付','" . date("Y-m-d H:i:s", time()) . "',0,'model')");
            $finance_record = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($finance_record == false) {
                $db->execute("rollback");
                $this->back("模型支付失败，请重新操作！");
                return false;
            }

            $db->execute("commit");


            $confing = $this->getDI()->getConfig();
            $alipay = $confing['alipay'];
            require_once PAY . 'alipay/lib/alipay_submit.class.php';
            $alipay_config['partner'] = $alipay['appid'];
            $alipay_config['key'] = $alipay['appkey'];
            $alipay_config['sign_type'] = strtoupper('MD5'); //签名方式 不需修改
            $alipay_config['input_charset'] = strtolower('utf-8'); //字符编码格式 目前支持 gbk 或 utf-8
            $alipay_config['cacert'] = PAY . "alipay/cacert.pem"; //ca证书路径地址，用于curl中ssl校验。请保证cacert.pem文件在当前文件夹目录中
            $alipay_config['transport'] = 'http'; //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            $payment_type = "1"; //支付类型
            $notify_url = $confing['webServer'] . "wallet/notify/o" . $ordersn . ""; //服务器异步通知页面路径
            $return_url = $confing['webServer'] . "wallet/return/o" . $ordersn . ""; //页面跳转同步通知页面路径

            $seller_email = $alipay['account']; //收款方支付宝帐户
            $anti_phishing_key = ""; //防钓鱼时间戳,若要使用请调用类文件submit中的query_timestamp函数
            $exter_invoke_ip = $_SERVER["REMOTE_ADDR"]; //客户端的IP地址
            $show_url = $alipay['webServer'] . 'print';
            $parameter = array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($alipay_config['partner']),
                "payment_type" => $payment_type,
                "notify_url" => $notify_url,
                "return_url" => $return_url,
                "seller_email" => $seller_email,
                "out_trade_no" => $out_trade_no,
                "subject" => $subject,
                "total_fee" => $total_fee,
                "body" => $body,
                "show_url" => $show_url,
                "anti_phishing_key" => $anti_phishing_key,
                "exter_invoke_ip" => $exter_invoke_ip,
                "_input_charset" => trim(strtolower($alipay_config['input_charset']))
            );
            $alipaySubmit = new \AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
            echo $html_text;
        }
    }

    # 微信支付

    public function buyModelForWx($params, $db) {
        $this->creatOrder($params);
        if ($this->code == 0) {
            $response = $this->response;
            $ordersn = $response['ordersn'];
            $modelprice = $response['modelprice'];
            $buid = $response['buid']; # 购买者id
            $suid = $response['suid']; # 出售者id
            $totalprice = $response['totalprice'];
            $where = "buid=" . $buid . " and ordersn in (" . $ordersn . ") and (status=1 or status=0)";
            $ordercount = Orders::count(array(
                        $where
                    ));

            if ($ordercount < 1) {
                return $this->code = 21;
            }
            $orders = Orders::find(array(
                        $where
                    ));
            $totalfee = 0;
            foreach ($orders as $order) {
                $totalfee += (floatval($order->amount) + floatval($order->shippingfee));
            }
            if ($totalfee <= 0) {
                return $this->code = 22;
            }
            $out_trade_no = serialnumber(2);
            $fr = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($fr != false) {
                $out_trade_no = serialnumber(2);
            }
            $bUser = FinanceBalance::findFirst("uid=" . $buid);
            if (!$bUser) {
                $bUser = new FinanceBalance ();
                $bUser->uid = $suid;
                $bUser->availablebalance = 0;
                $bUser->balance = 0;
                $bUser->updatetime = date("Y-m-d H:i:s", time());
                $bUser->save();
            }
            $balance = $bUser->balance;
            $subject = "模型支付"; //订单名称
            $total_fee = round(floatval($totalfee), 2); //付款金额
            $body = "模型支付"; //订单描述
            if ($total_fee == "0.00" || $total_fee == "0") {
                $db->execute("rollback");
                return $this->code = 23;
            }
            $db->execute("set autocommit=0");
            $db->execute("insert into finance_record(uid,serialnumber,amount,status,balance,note,addtime,type,obj) values(" . $buid . ",'" . $out_trade_no . "'," . $total_fee . ",2," . $balance . ",'模型支付','" . date("Y-m-d H:i:s", time()) . "',0,'model')");
            $finance_record = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($finance_record == false) {
                $db->execute("rollback");
                $this->back("模型支付失败，请重新操作！");
                return false;
            }

            $db->execute("commit");


            $confing = $this->getDI()->getConfig();
            $appid = $confing['wxpay']['appid'];
            $appkey = $confing['wxpay']['appkey'];
            $mch_id = $confing['wxpay']['mch_id'];
            $secret = $confing['wxpay']['secret'];
            $notify_url = $confing['webServer'] . "wallet/wxnotify/o" . $ordersn . "";
            define("APPID", $appid);
            define("MCHID", $mch_id);
            define("KEY", $secret);
            define("APPSECRET", $appkey);
            define("NOTIFY_URL", $notify_url);
            define("CURL_TIMEOUT", 30);
            #define("SSLCERT_PATH", PAY."weixin_pay/cacert/apiclient_cert.pem");
            #define("SSLKEY_PATH", PAY."weixin_pay/cacert/apiclient_key.pem");
            include_once PAY . 'weixin_pay/WxPay.pub.config.php';
            require_once PAY . 'weixin_pay/WxPayPubHelper.php';
            $unifiedOrder = new \UnifiedOrder_pub();
            $unifiedOrder->setParameter("body", "打印钱包充值"); //商品描述
            $unifiedOrder->setParameter("out_trade_no", "$out_trade_no"); //商户订单号
            $unifiedOrder->setParameter("total_fee", $total_fee * 100); //总金额
            $unifiedOrder->setParameter("notify_url", NOTIFY_URL); //通知地址
            $unifiedOrder->setParameter("trade_type", "NATIVE"); //交易类型
            $unifiedOrderResult = $unifiedOrder->getResult();

            //商户根据实际情况设置相应的处理流程
            if ($unifiedOrderResult["return_code"] == "FAIL") {
                echo "通信出错：" . $unifiedOrderResult['return_msg'] . "<br>";
            } elseif ($unifiedOrderResult["result_code"] == "FAIL") {
                echo "错误代码：" . $unifiedOrderResult['err_code'] . "<br>";
                echo "错误代码描述：" . $unifiedOrderResult['err_code_des'] . "<br>";
            } elseif ($unifiedOrderResult["code_url"] != NULL) {
                //从统一支付接口获取到code_url
                $code_url = $unifiedOrderResult["code_url"];
                $code_uri = keyEncode($code_url, '_');
                $uri = keyEncode($code_url);
                $timestamp = time();
                $feestr = $total_fee * 100;
                $key = md5($uri . $feestr . $timestamp . $out_trade_no);
                //商户自行增加处理流程
                $this->getDI()->getResponse()->redirect("/weixin/paycode?&tradeno=" . $out_trade_no . "&uri=" . $code_uri . "&fee=" . $total_fee . "&timestamp=" . time() . "&key=" . $key);
            }
        }
    }

    # 银联支付

    public function buyModelForYl($params, $db) {
        $this->creatOrder($params);
        if ($this->code == 0) {
            $response = $this->response;
            $ordersn = $response['ordersn'];
            $modelprice = $response['modelprice'];
            $buid = $response['buid']; # 购买者id
            $suid = $response['suid']; # 出售者id
            $totalprice = $response['totalprice'];
            $where = "buid=" . $buid . " and ordersn in (" . $ordersn . ") and (status=1 or status=0)";
            $ordercount = Orders::count(array(
                        $where
                    ));

            if ($ordercount < 1) {
                return $this->code = 21;
            }
            $orders = Orders::find(array(
                        $where
                    ));
            $totalfee = 0;
            foreach ($orders as $order) {
                $totalfee += (floatval($order->amount) + floatval($order->shippingfee));
            }
            if ($totalfee <= 0) {
                return $this->code = 22;
            }
            $out_trade_no = serialnumber(2);
            $fr = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($fr != false) {
                $out_trade_no = serialnumber(2);
            }
            $bUser = FinanceBalance::findFirst("uid=" . $buid);
            if (!$bUser) {
                $bUser = new FinanceBalance ();
                $bUser->uid = $suid;
                $bUser->availablebalance = 0;
                $bUser->balance = 0;
                $bUser->updatetime = date("Y-m-d H:i:s", time());
                $bUser->save();
            }
            $balance = $bUser->balance;
            $subject = "模型支付"; //订单名称
            $total_fee = round(floatval($totalfee), 2); //付款金额
            $body = "模型支付"; //订单描述
            if ($total_fee == "0.00" || $total_fee == "0") {
                $db->execute("rollback");
                return $this->code = 23;
            }
            $db->execute("set autocommit=0");
            $db->execute("insert into finance_record(uid,serialnumber,amount,status,balance,note,addtime,type,obj) values(" . $buid . ",'" . $out_trade_no . "'," . $total_fee . ",2," . $balance . ",'模型支付','" . date("Y-m-d H:i:s", time()) . "',0,'model')");
            $finance_record = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($finance_record == false) {
                $db->execute("rollback");
                $this->back("模型支付失败，请重新操作！");
                return false;
            }

            $db->execute("commit");



            $confing = $this->getDI()->getConfig();
            require_once PAY . 'yinlian/common.php';
            include_once PAY . 'yinlian/SDKConfig.php';
            include_once PAY . 'yinlian/secureUtil.php';
            include_once PAY . 'yinlian/log.class.php';
            define('SDK_FRONT_NOTIFY_URL', $confing['webServer'] . "wallet/ylreturn/o" . $ordersn . "");
            define('SDK_BACK_NOTIFY_URL', $confing['webServer'] . "wallet/ylnotify/o" . $ordersn . "");
            $merId = $confing['yinlian']['merId'];
            $params = array(
                'version' => '5.0.0', //版本号
                'encoding' => 'utf-8', //编码方式
                'certId' => getSignCertId(), //证书ID
                'txnType' => '01', //交易类型
                'txnSubType' => '01', //交易子类
                'bizType' => '000201', //业务类型
                'frontUrl' => SDK_FRONT_NOTIFY_URL, //前台通知地址
                'backUrl' => SDK_BACK_NOTIFY_URL, //后台通知地址
                'signMethod' => '01', //签名方法
                'channelType' => '08', //渠道类型，07-PC，08-手机
                'accessType' => '0', //接入类型
                'merId' => $merId, //商户代码，请改自己的测试商户号
                'orderId' => $out_trade_no, //商户订单号
                'txnTime' => date('YmdHis'), //订单发送时间
                'txnAmt' => $total_fee * 100, //交易金额，单位分
                'currencyCode' => '156', //交易币种
                'defaultPayType' => '0001', //默认支付方式
                'reqReserved' => $body, //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
            );
            sign($params);
            // 前台请求地址
            writelog(json_encode($params), "yinlian");
            $front_uri = SDK_FRONT_TRANS_URL;
            $html_form = create_html($params, $front_uri);
            echo $html_form;
        }
    }

    # 财付通支付

    public function buyModelForCft($params, $db) {
        $this->creatOrder($params);
        if ($this->code == 0) {
            $response = $this->response;
            $ordersn = $response['ordersn'];
            $modelprice = $response['modelprice'];
            $buid = $response['buid']; # 购买者id
            $suid = $response['suid']; # 出售者id
            $totalprice = $response['totalprice'];
            $where = "buid=" . $buid . " and ordersn in (" . $ordersn . ") and (status=1 or status=0)";
            $ordercount = Orders::count(array(
                        $where
                    ));

            if ($ordercount < 1) {
                return $this->code = 21;
            }
            $orders = Orders::find(array(
                        $where
                    ));
            $totalfee = 0;
            foreach ($orders as $order) {
                $totalfee += (floatval($order->amount) + floatval($order->shippingfee));
            }
            if ($totalfee <= 0) {
                return $this->code = 22;
            }
            $out_trade_no = serialnumber(2);
            $fr = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($fr != false) {
                $out_trade_no = serialnumber(2);
            }
            $bUser = FinanceBalance::findFirst("uid=" . $buid);
            if (!$bUser) {
                $bUser = new FinanceBalance ();
                $bUser->uid = $suid;
                $bUser->availablebalance = 0;
                $bUser->balance = 0;
                $bUser->updatetime = date("Y-m-d H:i:s", time());
                $bUser->save();
            }
            $balance = $bUser->balance;
            $subject = "模型支付"; //订单名称
            $total_fee = round(floatval($totalfee), 2); //付款金额
            $body = "模型支付"; //订单描述
            if ($total_fee == "0.00" || $total_fee == "0") {
                $db->execute("rollback");
                return $this->code = 23;
            }
            $db->execute("set autocommit=0");
            $db->execute("insert into finance_record(uid,serialnumber,amount,status,balance,note,addtime,type,obj) values(" . $buid . ",'" . $out_trade_no . "'," . $total_fee . ",2," . $balance . ",'模型支付','" . date("Y-m-d H:i:s", time()) . "',0,'model')");
            $finance_record = FinanceRecord::findFirst(array("serialnumber='" . $out_trade_no . "'"));
            if ($finance_record == false) {
                $db->execute("rollback");
                $this->back("模型支付失败，请重新操作！");
                return false;
            }

            $db->execute("commit");


            $confing = $this->getDI()->getConfig();
            require_once PAY . 'tenpay/RequestHandler.class.php';
            $partner = $confing['tenpay']['appid'];
            $key = $confing['tenpay']['appkey'];
            $notify_url = $confing['webServer'] . "wallet/tennotify/o" . $ordersn . ""; //服务器异步通知页面路径
            $return_url = $confing['webServer'] . "wallet/tenreturn/o" . $ordersn . ""; //页面跳转同步通知页面路径

            $reqHandler = new \RequestHandler();
            $reqHandler->init();
            $reqHandler->setKey($key);
            $reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");

            //设置支付参数
            $reqHandler->setParameter("partner", $partner);
            $reqHandler->setParameter("out_trade_no", $out_trade_no);
            $reqHandler->setParameter("total_fee", $total_fee * 100);  //总金额
            $reqHandler->setParameter("return_url", $return_url);
            $reqHandler->setParameter("notify_url", $notify_url);
            $reqHandler->setParameter("body", $body);
            $reqHandler->setParameter("bank_type", "DEFAULT");     //银行类型，默认为财付通
            //用户ip
            $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']); //客户端IP
            $reqHandler->setParameter("fee_type", "1");               //币种
            $reqHandler->setParameter("subject", $subject);          //商品名称，（中介交易时必填）
            //系统可选参数
            $reqHandler->setParameter("sign_type", "MD5");       //签名方式，默认为MD5，可选RSA
            $reqHandler->setParameter("service_version", "1.0");    //接口版本号
            $reqHandler->setParameter("input_charset", "utf-8");      //字符集
            $reqHandler->setParameter("sign_key_index", "1");       //密钥序号
            //业务可选参数
            $reqHandler->setParameter("attach", "");                //附件数据，原样返回就可以了
            $reqHandler->setParameter("product_fee", "");           //商品费用
            $reqHandler->setParameter("transport_fee", "0");         //物流费用
            $reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
            $reqHandler->setParameter("time_expire", "");             //订单失效时间
            $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
            $reqHandler->setParameter("goods_tag", "");               //商品标记
            $reqHandler->setParameter("trade_mode", "1");              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
            $reqHandler->setParameter("transport_desc", "");              //物流说明
            $reqHandler->setParameter("trans_type", "1");              //交易类型
            $reqHandler->setParameter("agentid", "");                  //平台ID
            $reqHandler->setParameter("agent_type", "");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
            $reqHandler->setParameter("seller_id", "");                //卖家的商户号
            //请求的URL
            $reqUrl = $reqHandler->getRequestURL();
            $submit_form = $reqHandler->buildRequestForm();
            echo $submit_form;
            $debugInfo = $reqHandler->getDebugInfo();
//            writelog($debugInfo,'tenpay');
        }
    }

    # 创建订单

    public function creatOrder($params) {
        if (!is_login($this->getDI()))
            $this->noLogin();
        if (!isset($params['modelnumber']))
            return $this->code = 15;
        $count = isset($params['count']) ? intval($params['count']) : 1;
        $attid = isset($params['attid']) ? intval($params['attid']) : 0;

        $modelnumber = $params['modelnumber'];
        if (intval($modelnumber) != 0) {
            $model = Model::findFirst(array(
                        "number=" . $modelnumber . ""
                    ));
            if (floatval($model->price) <= 0)
                return $this->code = 16;

            $totalprice = formatprice(floatval($model->price) * intval($count));
            if ($model != false) {
                $suid = $model->uid;
                $uinfo = $this->getDI()->getSession()->get("user");
                $config = $this->getDI()->getConfig();

                //判断该订单是否已经创建
                $builder = $this->getModelsManager()->createBuilder();
                $modelinfo = $builder->addfrom("\\Apps\\Commons\\Models\\Orders", "Orders")
                        ->leftJoin("\\Apps\\Commons\\Models\\OrderInfo", "OrderInfo.orderid = Orders.id", "OrderInfo")
                        ->where("Orders.buid = :buid:", ["buid" => $uinfo['id']])
                        ->andWhere("Orders.suid = :suid:", ["suid" => $suid])
                        ->andWhere("Orders.status = :status:", ["status" => 1])
                        ->andWhere("Orders.amount = :amount:", ["amount" => $totalprice])
                        ->andWhere("OrderInfo.modelid = :modelid:", ["modelid" => $model->id])
                        ->limit(1)
                        ->orderBy("Orders.id desc")
                        ->getQuery()
                        ->execute();
                // 改订单已经创建
                if ($modelinfo != false and count($modelinfo->toArray()) > 0) {
                    $modelinfo[0]->addtime = date('Y-m-d H:i:s', time());
                    $modelinfo[0]->save();
                    $ordersn2 = $modelinfo[0]->ordersn;
                } else {
                    $ordersn2 = serialnumber(2); // 生成订单号
                    $mOrder = new Orders();
                    $mOrder->suid = $suid;
                    $mOrder->buid = $uinfo['id'];
                    $mOrder->suid = $suid;
                    $mOrder->ordersn = $ordersn2;
                    $mOrder->amount = $totalprice;
                    $mOrder->status = 1;
                    $mOrder->addtime = date("Y-m-d H:i:s", time());
                    $mOrder->ordertype = 0;
                    $t2 = $mOrder->save();
                    if ($t2) {
                        $orderid2 = $mOrder->id;
                        // shippingfee要改
                        $mOrderInfo = new OrderInfo();
                        $mOrderInfo->orderid = $orderid2;
                        $mOrderInfo->madeid = 0;
                        $mOrderInfo->modelid = $model->id;
                        $mOrderInfo->price = $model->price;
                        $mOrderInfo->shippingfee = 0;
                        $mOrderInfo->amount = $count;
                        $mOrderInfo->attid = $attid;
                        $mOrderInfo->addtime = date("Y-m-d H:i:s", time());
                        $mOrderInfo->closetime = "0000-00-00 00:00:00";
                        $mOrderInfo->save();
                    }
                }

                $response['modelid'] = $model->id;
                $response['modelnumber'] = $model->number;
                $response['modelprice'] = floatval($model->price);
                $response['totalprice'] = floatval($totalprice);
                $response['ordersn'] = $ordersn2;
                $response['buid'] = $uinfo['id'];
                $response['suid'] = $model->uid;
                $response['ordersn'] = $ordersn2;
                $this->response = $response;
            }
        }
    }

    # 删除模型

    public function del($id = null) {
        if ($id) {
            $Info = Model::findFirst(array("id={$id}"));
            if ($Info) {
                $rs = $Info->delete();
                if (!$rs)
                    return $this->code = 27;
                else {
                    $files = ModelFile::find(array("modelid={$id}"));
                    if ($files) {
                        foreach ($files as $v) {
                            $v->delete();
                        }
                    }
                    $imgs = ModelImg::find(array("modelid={$id}"));
                    if ($imgs) {
                        foreach ($imgs as $v) {
                            $v->delete();
                        }
                    }
                    $tags = ModelTags::find(array("modelid={$id}"));
                    if ($tags) {
                        foreach ($tags as $v) {
                            $v->delete();
                        }
                    }
                }
            }
        } else {
            $this->code = 2;
        }
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
