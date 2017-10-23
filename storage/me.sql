/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50538
Source Host           : localhost:3306
Source Database       : me

Target Server Type    : MYSQL
Target Server Version : 50538
File Encoding         : 65001

Date: 2017-10-11 11:50:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(50) DEFAULT NULL COMMENT '网站中模型编号隐藏真实数量',
  `uid` int(11) NOT NULL COMMENT '用户编号',
  `city` int(10) DEFAULT '0' COMMENT '某某城市',
  `cateid` int(11) DEFAULT NULL COMMENT '类别ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `description` text COMMENT '描述',
  `addtime` datetime DEFAULT NULL COMMENT '提交时间',
  `edittime` datetime DEFAULT NULL COMMENT '最近编辑时间',
  `views` int(11) DEFAULT '0' COMMENT '浏览次数',
  `checked` int(1) DEFAULT '0' COMMENT '审核状态，0默认，审核不通过是1，审核通过是2，删除是3，存为草稿:9',
  `img` varchar(255) DEFAULT NULL COMMENT '代表图',
  `zancount` int(11) DEFAULT '0' COMMENT '点赞次数',
  `commentcount` int(11) DEFAULT '0' COMMENT '评论次数',
  `opinion` varchar(255) DEFAULT '' COMMENT '审核不通过时给出的审核意见',
  `isrecommend` tinyint(1) DEFAULT '0' COMMENT '是否推荐到首页',
  `ismymodel` tinyint(1) unsigned DEFAULT '0' COMMENT '作者本人原创申明默认0非原创1原创',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `title_2` (`title`),
  FULLTEXT KEY `title_3` (`title`),
  FULLTEXT KEY `description_2` (`description`),
  FULLTEXT KEY `title_4` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='文章';

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('1', '58731799909', '1', '0', '51', '神僧', '神僧', '2017-09-13 15:05:18', null, '3', '2', 'imgs/20170913/model_1505286289.jpg', '1', '0', '', '0', '0');
INSERT INTO `article` VALUES ('2', '27503443092', '2', '0', '6', '机器人', '机器人', '2017-09-13 15:06:29', null, '0', '2', 'imgs/20170913/model_1505286359.jpg', '1', '0', '', '0', '0');

-- ----------------------------
-- Table structure for article_cate
-- ----------------------------
DROP TABLE IF EXISTS `article_cate`;
CREATE TABLE `article_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(50) DEFAULT NULL COMMENT '分类网站编号，隐藏实际编号',
  `cid` int(11) DEFAULT '0' COMMENT '类别ID',
  `name` varchar(255) DEFAULT NULL COMMENT '类别名称',
  `description` text COMMENT '分类说明',
  `edittime` datetime DEFAULT NULL COMMENT '分类最近更新时间',
  `slug` varchar(255) DEFAULT NULL COMMENT '分类SEO URL',
  PRIMARY KEY (`id`),
  KEY `name_2` (`name`),
  KEY `name_3` (`name`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `description_2` (`description`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COMMENT='模型分类';

-- ----------------------------
-- Records of article_cate
-- ----------------------------
INSERT INTO `article_cate` VALUES ('1', '606846970280101', '0', '3D打印', '3D打印相关的零配件、部件、测试原件。', '2014-07-21 14:14:25', 'dayin');
INSERT INTO `article_cate` VALUES ('2', '898299982058714', '0', '艺术', '艺术相关模型，包含数字艺术、徽章、钱币、雕刻等艺术模型。', '2014-07-21 10:56:40', 'yishu');
INSERT INTO `article_cate` VALUES ('3', '805259147532850', '0', '家居', '家庭生活客厅、厕所、浴室、厨房餐厅、液体容器、家具、食物所需要的3D模型展示、下载。\r\n', '2014-07-21 16:55:40', 'jiating');
INSERT INTO `article_cate` VALUES ('4', '674232898491128', '0', '时尚', '各种时尚服装、配件、装饰物品的模型。', '2014-07-21 10:58:32', 'shishang');
INSERT INTO `article_cate` VALUES ('5', '971520370112306', '0', '动物植物', '各种人物、生物造型模型。', '2014-08-27 11:43:18', 'dongzhiwu');
INSERT INTO `article_cate` VALUES ('6', '139896531072857', '0', '玩具', '各种游戏玩具、棋牌、积木等玩具模型。', '2014-07-21 11:31:11', 'wanju');
INSERT INTO `article_cate` VALUES ('7', '897338982294978', '0', '工具', '工具整件模型及配件模型下载。', '2014-07-21 11:39:09', 'gongju');
INSERT INTO `article_cate` VALUES ('8', '461007010723452', '0', '学习', '化学、生物学、工程学、地理、物理学、学习办公工具、文字语言、数学、美术等学习模型。', '2014-07-21 13:41:45', 'xuexi');
INSERT INTO `article_cate` VALUES ('9', '205359560182646', '8', '生物学', '生物学学习所需要的3D模型展示、下载。', '2014-09-17 14:41:47', 'shengwu');
INSERT INTO `article_cate` VALUES ('10', '795455829244479', '8', '工程学', '', '2014-07-21 15:13:46', 'gongcheng');
INSERT INTO `article_cate` VALUES ('11', '943131071897171', '8', '数学', '', '2014-07-21 15:14:31', 'shuxue');
INSERT INTO `article_cate` VALUES ('12', '291143322473367', '8', '地理学', '', '2014-07-21 15:15:41', 'dili');
INSERT INTO `article_cate` VALUES ('13', '245035324414697', '8', '物理学', '', '2014-07-21 15:16:43', 'wuli');
INSERT INTO `article_cate` VALUES ('14', '787338852613436', '3', '客厅物品', '', '2016-04-05 18:09:21', 'keting');
INSERT INTO `article_cate` VALUES ('15', '170821628066328', '3', '浴室物品', '', '2016-03-24 14:33:28', 'yushi');
INSERT INTO `article_cate` VALUES ('16', '155266183790726', '3', '厨房物品', '', '2014-07-21 16:57:00', 'chufang');
INSERT INTO `article_cate` VALUES ('17', '464158782700766', '3', '容器', '', '2016-03-30 14:58:03', 'rongqi');
INSERT INTO `article_cate` VALUES ('18', '262546947866057', '3', '家具', '', '2014-09-17 03:50:54', 'jiaju');
INSERT INTO `article_cate` VALUES ('19', '237673856098971', '3', '卧室物品', '卧室物品3D打印模型', '2014-09-17 14:41:41', 'woshi');
INSERT INTO `article_cate` VALUES ('20', '879724148463345', '6', '积木', '', '2015-01-16 11:05:09', 'jimu');
INSERT INTO `article_cate` VALUES ('21', '760221905000185', '6', '棋牌', '', '2014-09-24 10:28:45', 'qipai');
INSERT INTO `article_cate` VALUES ('22', '594727366087526', '6', '拼图', '', '2014-11-24 15:06:18', 'pintu');
INSERT INTO `article_cate` VALUES ('23', '405105950984663', '6', '益智', '', '2015-02-26 10:55:54', 'yizhi');
INSERT INTO `article_cate` VALUES ('24', '719304787079055', '7', '工具配件', '', '2015-02-05 14:15:13', 'peijian');
INSERT INTO `article_cate` VALUES ('25', '940614439338700', '7', '机械工具', '', '2014-09-26 13:13:29', 'jixieweixiu');
INSERT INTO `article_cate` VALUES ('26', '794464786857267', '7', '手工具', '', '2015-07-21 11:03:12', 'shougongju');
INSERT INTO `article_cate` VALUES ('27', '809965648219027', '6', '刀剑', '', '2014-11-24 11:56:14', 'daojian');
INSERT INTO `article_cate` VALUES ('28', '361120947937676', '6', '弓箭', '', '2014-09-16 09:47:36', 'gongjian');
INSERT INTO `article_cate` VALUES ('29', '676949496711916', '6', '枪炮', '', '2014-09-24 10:27:18', 'qiangpao');
INSERT INTO `article_cate` VALUES ('33', '697300267578002', '1', '3D打印机', '', '2015-11-12 11:21:35', 'dayinji');
INSERT INTO `article_cate` VALUES ('30', '614650464333888', '6', '车模型', '', '2014-09-17 12:05:48', 'chemoxing');
INSERT INTO `article_cate` VALUES ('31', '105614844492717', '6', '船模型', '', '2016-11-11 11:05:29', 'chuanmoxing');
INSERT INTO `article_cate` VALUES ('32', '279015829242124', '6', '飞行器', '', '2014-12-23 17:56:11', 'feixingqi');
INSERT INTO `article_cate` VALUES ('34', '946184225295472', '1', '打印测试', '', '2014-09-23 16:01:10', 'ceshi');
INSERT INTO `article_cate` VALUES ('35', '166009819859930', '1', '3D打印配件', '', '2016-04-05 13:50:33', '3dpeijian');
INSERT INTO `article_cate` VALUES ('36', '319153552617029', '2', '2D艺术', '', '2014-07-21 18:13:35', '2dyishu');
INSERT INTO `article_cate` VALUES ('37', '239414614564095', '2', '钱币徽章', '', '2014-07-21 18:15:15', 'qianbi');
INSERT INTO `article_cate` VALUES ('38', '499886246070897', '2', '互动艺术', '', '2014-07-21 18:15:56', 'hudong');
INSERT INTO `article_cate` VALUES ('39', '834650119821899', '2', '数学艺术', '', '2014-07-21 18:16:58', 'shuxueyishu');
INSERT INTO `article_cate` VALUES ('40', '622177972696652', '2', '雕塑', '', '2014-07-21 18:17:55', 'diaosu');
INSERT INTO `article_cate` VALUES ('41', '614929181232310', '2', '标志', '', '2014-07-21 18:18:23', 'logo');
INSERT INTO `article_cate` VALUES ('42', '353126317004610', '4', '吊坠', '', '2014-07-21 18:21:49', 'diaozhui');
INSERT INTO `article_cate` VALUES ('43', '102918414719819', '4', '手饰', '', '2014-07-21 18:23:18', 'shoushi');
INSERT INTO `article_cate` VALUES ('44', '508898475460806', '4', '头饰', '', '2016-03-25 17:48:33', 'toushi');
INSERT INTO `article_cate` VALUES ('45', '755846372748689', '4', '服饰', '', '2014-07-21 18:29:13', 'fushi');
INSERT INTO `article_cate` VALUES ('46', '180585379919135', '51', '机器人', '机器人模型', '2016-04-05 14:29:35', 'jiqiren');
INSERT INTO `article_cate` VALUES ('47', '202189507575952', '5', '海洋植物', '', '2014-07-21 18:47:10', 'haiyangzhiwu');
INSERT INTO `article_cate` VALUES ('48', '249937601421671', '5', '海洋动物', '', '2014-07-21 18:47:37', 'haidongwu');
INSERT INTO `article_cate` VALUES ('49', '198620726934715', '5', '陆地动物', '', '2016-11-11 11:09:12', 'ludongwu');
INSERT INTO `article_cate` VALUES ('50', '997940562337736', '5', '陆地植物', '', '2014-07-21 18:48:29', 'luzhiwu');
INSERT INTO `article_cate` VALUES ('51', '335261786527123', '0', '人物', '人物造型模型', '2016-03-30 15:13:08', 'renwu');
INSERT INTO `article_cate` VALUES ('52', '646912650790038', '51', '游戏造型', '', '2016-04-05 17:40:12', 'youxizaoxing');
INSERT INTO `article_cate` VALUES ('53', '497509973081940', '0', '业余爱好', '', '2014-07-21 18:53:14', 'aihao');
INSERT INTO `article_cate` VALUES ('54', '529319027914851', '53', 'DIY', '', '2015-07-21 14:01:41', 'diy');
INSERT INTO `article_cate` VALUES ('55', '554698704853539', '5', '飞禽', '', '2014-07-21 18:54:44', 'feiqin');
INSERT INTO `article_cate` VALUES ('56', '355757311032041', '53', '乐器', '', '2014-07-21 18:56:23', 'yueqi');
INSERT INTO `article_cate` VALUES ('57', '144927110177497', '53', '户外运动', '', '2014-09-16 18:57:42', 'yundong');
INSERT INTO `article_cate` VALUES ('58', '971129243893792', '0', '电子产品', '', '2014-08-27 11:35:26', 'dianzi');
INSERT INTO `article_cate` VALUES ('59', '070292437336363', '53', '自动化', '', '2014-07-21 18:59:33', 'zidonghua');
INSERT INTO `article_cate` VALUES ('60', '911525949062291', '58', '小玩意', '电子小玩意用品模型。', '2014-09-17 15:06:20', 'xiaowanyi');
INSERT INTO `article_cate` VALUES ('61', '884027241899101', '58', '耳机', '', '2014-07-21 19:01:25', 'erji');
INSERT INTO `article_cate` VALUES ('62', '838995834764147', '58', '计算机', '', '2016-11-11 11:10:13', 'jisuanji');
INSERT INTO `article_cate` VALUES ('63', '355546036849076', '58', '摄像机', '', '2014-07-21 19:02:02', 'shexiangji');
INSERT INTO `article_cate` VALUES ('64', '178025301610876', '58', '手机', '', '2014-11-03 13:49:43', 'shouji');
INSERT INTO `article_cate` VALUES ('65', '868786120767550', '58', '平板', '', '2014-07-21 19:04:51', 'pingban');
INSERT INTO `article_cate` VALUES ('66', '900240864324632', '3', '装饰品', '', '2016-03-24 14:02:27', 'zhuangshipin');
INSERT INTO `article_cate` VALUES ('67', '681695922305686', '3', '宠物', '', '2014-09-25 10:19:38', 'chongwu');
INSERT INTO `article_cate` VALUES ('68', '142533833539129', '0', '建筑结构', '建筑结构类模型下载。', '2014-07-21 20:31:45', 'jianzhu');
INSERT INTO `article_cate` VALUES ('69', '756664105305267', '68', '塔、碑、柱', '', '2016-03-30 16:27:26', 'tabeizhu');
INSERT INTO `article_cate` VALUES ('70', '235554709113324', '68', '桥梁', '', '2014-12-19 11:32:27', 'qiaoliang');
INSERT INTO `article_cate` VALUES ('71', '993322349976546', '68', '大厦房屋', '', '2014-07-21 20:30:10', 'dasha');
INSERT INTO `article_cate` VALUES ('72', '335098157896512', '68', '特殊建筑', '', '2016-03-30 15:58:34', 'teshu');
INSERT INTO `article_cate` VALUES ('73', '207005657035185', '5', '昆虫', '昆虫3D模型', '2016-11-11 11:18:41', 'kunchong');

-- ----------------------------
-- Table structure for article_comment
-- ----------------------------
DROP TABLE IF EXISTS `article_comment`;
CREATE TABLE `article_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '评论者ID',
  `articleid` int(11) DEFAULT NULL COMMENT '资源ID',
  `comment` text COMMENT '评论内容',
  `addtime` datetime DEFAULT NULL COMMENT '评论时间',
  `star` int(2) unsigned DEFAULT '0' COMMENT '星级',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '表示评论状态（未审核：0；审核未通过：1；审核通过：2；删除：3）',
  `zancount` int(11) DEFAULT '0' COMMENT '点赞数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户评论';

-- ----------------------------
-- Records of article_comment
-- ----------------------------
INSERT INTO `article_comment` VALUES ('1', '2', '1', '测试按时发斯蒂芬个', '2017-09-13 15:09:44', '0', '2', '0');
INSERT INTO `article_comment` VALUES ('2', '1', '1', '宋大哥宋大哥', '2017-09-13 15:10:08', '0', '2', '0');
INSERT INTO `article_comment` VALUES ('3', '1', '2', 'sf', '2017-09-18 09:43:16', '0', '0', '0');

-- ----------------------------
-- Table structure for article_img
-- ----------------------------
DROP TABLE IF EXISTS `article_img`;
CREATE TABLE `article_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `articleid` int(11) NOT NULL COMMENT '模型编号',
  `img` varchar(255) DEFAULT NULL COMMENT '图片',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `lwh` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='模型图片';

-- ----------------------------
-- Records of article_img
-- ----------------------------
INSERT INTO `article_img` VALUES ('1', '1', 'imgs/20170913/model_1505286289.jpg', '2017-09-13 15:04:49', '823,584', 'QQ图片20160617094500.jpg');
INSERT INTO `article_img` VALUES ('2', '1', 'imgs/20170913/model_1505286292.jpg', '2017-09-13 15:04:52', '1174,658', 'QQ图片20160613141737.jpg');
INSERT INTO `article_img` VALUES ('3', '2', 'imgs/20170913/model_1505286359.jpg', '2017-09-13 15:05:59', '799,600', 'product_222979_800x600.jpg');
INSERT INTO `article_img` VALUES ('4', '2', 'imgs/20170913/model_1505286363.jpg', '2017-09-13 15:06:03', '800,600', 'product_222978_800x600.jpg');
INSERT INTO `article_img` VALUES ('5', '0', 'imgs/20170922/model_1506044041.jpg', '2017-09-22 09:34:01', '379,219', '30_choose.jpg');
INSERT INTO `article_img` VALUES ('6', '0', 'imgs/20170922/model_1506044896.jpg', '2017-09-22 09:48:16', '379,219', '30_choose.jpg');

-- ----------------------------
-- Table structure for article_zan
-- ----------------------------
DROP TABLE IF EXISTS `article_zan`;
CREATE TABLE `article_zan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `articleid` int(11) DEFAULT NULL COMMENT '模型编号ID',
  `addtime` datetime DEFAULT NULL COMMENT '点赞时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='模型点赞';

-- ----------------------------
-- Records of article_zan
-- ----------------------------
INSERT INTO `article_zan` VALUES ('1', '1', '2', '2017-09-13 15:08:27');
INSERT INTO `article_zan` VALUES ('2', '2', '1', '2017-09-13 15:09:31');
