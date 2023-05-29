<?php

//decode by http://www.yunlu99.com/
$installSql = "

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gc_access`
-- ----------------------------
DROP TABLE IF EXISTS `gc_access`;
CREATE TABLE `gc_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `purviewval` longtext COMMENT '分组对应权限值',
  `role_id` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3279 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_access
-- ----------------------------
INSERT INTO `gc_access` VALUES ('3278', 'admin/Reserve/view', '22');
INSERT INTO `gc_access` VALUES ('3277', 'admin/Reserve/updateExt', '22');
INSERT INTO `gc_access` VALUES ('3276', 'admin/Reserve/index', '22');
INSERT INTO `gc_access` VALUES ('3275', 'admin/Reserve', '22');
INSERT INTO `gc_access` VALUES ('3274', 'admin/Special/delete', '22');
INSERT INTO `gc_access` VALUES ('3273', 'admin/Special/update', '22');
INSERT INTO `gc_access` VALUES ('3272', 'admin/Special/add', '22');
INSERT INTO `gc_access` VALUES ('3271', 'admin/Special/updateExt', '22');
INSERT INTO `gc_access` VALUES ('3270', 'admin/Special/index', '22');
INSERT INTO `gc_access` VALUES ('3269', 'admin/Special', '22');
INSERT INTO `gc_access` VALUES ('3268', 'admin/Ticket/delete', '22');
INSERT INTO `gc_access` VALUES ('3267', 'admin/Ticket/update', '22');
INSERT INTO `gc_access` VALUES ('3266', 'admin/Ticket/add', '22');
INSERT INTO `gc_access` VALUES ('3265', 'admin/Ticket/updateExt', '22');
INSERT INTO `gc_access` VALUES ('3264', 'admin/Ticket/index', '22');
INSERT INTO `gc_access` VALUES ('3263', 'admin/Ticket', '22');
INSERT INTO `gc_access` VALUES ('3262', 'admin/Slide/view', '22');
INSERT INTO `gc_access` VALUES ('3261', 'admin/Slide/delete', '22');
INSERT INTO `gc_access` VALUES ('3260', 'admin/Slide/update', '22');
INSERT INTO `gc_access` VALUES ('3259', 'admin/Slide/add', '22');
INSERT INTO `gc_access` VALUES ('3258', 'admin/Slide/updateExt', '22');
INSERT INTO `gc_access` VALUES ('3257', 'admin/Slide/index', '22');
INSERT INTO `gc_access` VALUES ('3256', 'admin/Slide', '22');
INSERT INTO `gc_access` VALUES ('3255', 'admin/Notice/view', '22');
INSERT INTO `gc_access` VALUES ('3254', 'admin/Notice/delete', '22');
INSERT INTO `gc_access` VALUES ('3253', 'admin/Notice/update', '22');
INSERT INTO `gc_access` VALUES ('3252', 'admin/Notice/add', '22');
INSERT INTO `gc_access` VALUES ('3251', 'admin/Notice/updateExt', '22');
INSERT INTO `gc_access` VALUES ('3250', 'admin/Notice/index', '22');
INSERT INTO `gc_access` VALUES ('3249', 'admin/Notice', '22');
INSERT INTO `gc_access` VALUES ('3248', 'admin/TicketConfig/view', '22');
INSERT INTO `gc_access` VALUES ('3245', 'admin/TicketConfig', '22');
INSERT INTO `gc_access` VALUES ('3246', 'admin/TicketConfig/index', '22');
INSERT INTO `gc_access` VALUES ('3247', 'admin/TicketConfig/update', '22');
INSERT INTO `gc_access` VALUES ('3244', 'admin/Member/updateExt', '22');
INSERT INTO `gc_access` VALUES ('3243', 'admin/Member/index', '22');
INSERT INTO `gc_access` VALUES ('3242', 'admin/Member', '22');
INSERT INTO `gc_access` VALUES ('3241', 'admin/Base/password', '22');
INSERT INTO `gc_access` VALUES ('3240', 'admin/Index/main', '22');

-- ----------------------------
-- Table structure for `gc_account`
-- ----------------------------
DROP TABLE IF EXISTS `gc_account`;
CREATE TABLE `gc_account` (
  `wxapp_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `account` varchar(250) DEFAULT NULL COMMENT '登录账号',
  `pwd` varchar(250) DEFAULT NULL COMMENT '登录密码',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`wxapp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `gc_action`
-- ----------------------------
DROP TABLE IF EXISTS `gc_action`;
CREATE TABLE `gc_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL COMMENT '模块ID',
  `name` varchar(255) DEFAULT NULL COMMENT '动作名称',
  `action_name` varchar(128) NOT NULL COMMENT '动作名称',
  `type` tinyint(4) NOT NULL,
  `icon` varchar(32) DEFAULT NULL COMMENT 'icon图标',
  `pagesize` varchar(5) DEFAULT '20' COMMENT '每页显示数据条数',
  `is_view` tinyint(4) DEFAULT '0' COMMENT '是否按钮',
  `button_status` tinyint(4) DEFAULT NULL COMMENT '按钮是否显示列表',
  `sql_query` mediumtext COMMENT 'sql数据源',
  `block_name` varchar(255) DEFAULT NULL COMMENT '注释',
  `remark` varchar(255) DEFAULT NULL COMMENT '打开页面尺寸',
  `fields` text COMMENT '操作的字段',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `lable_color` varchar(12) DEFAULT NULL COMMENT '按钮背景色',
  `relate_table` varchar(32) DEFAULT NULL COMMENT '关联表',
  `relate_field` varchar(32) DEFAULT NULL COMMENT '关联字段',
  `list_field` varchar(255) DEFAULT NULL COMMENT '查询的字段',
  `bs_icon` varchar(32) DEFAULT NULL COMMENT '按钮图标',
  `sortid` mediumint(9) DEFAULT '0' COMMENT '排序',
  `orderby` varchar(250) DEFAULT NULL COMMENT '配置排序',
  `default_orderby` varchar(50) DEFAULT NULL COMMENT '默认排序',
  `tree_config` varchar(50) DEFAULT NULL,
  `jump` varchar(120) DEFAULT NULL COMMENT '按钮跳转地址',
  `is_controller_create` tinyint(4) DEFAULT '1' COMMENT '是否生成控制其方法',
  `is_service_create` tinyint(4) DEFAULT NULL COMMENT '是否生成服务层方法',
  `is_view_create` tinyint(4) DEFAULT NULL COMMENT '视图生成',
  `cache_time` mediumint(9) DEFAULT NULL COMMENT '缓存时间',
  `log_status` tinyint(4) DEFAULT NULL COMMENT '是否生成日志',
  `api_auth` tinyint(4) DEFAULT NULL COMMENT '接口是否鉴权',
  `sms_auth` tinyint(4) DEFAULT NULL COMMENT '短信验证',
  `captcha_auth` tinyint(4) DEFAULT NULL COMMENT '列表选中方式 1多选 2单选',
  `request_type` varchar(20) DEFAULT NULL COMMENT '请求类型',
  `do_condition` varchar(50) DEFAULT NULL COMMENT '操作条件',
  `select_type` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2935 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_action
-- ----------------------------
INSERT INTO `gc_action` VALUES ('78', '18', '首页数据列表', 'index', '1', '', '', '0', '0', 'select a.*,group_concat(b.name) as role_name from pre_user as a left join pre_role as b on find_in_set(b.role_id,a.role_id)  group by a.user_id', '', '', '', '', 'primary', '', '', '', '', '1', '', '', '', '', '1', '1', '1', null, null, null, null, '0', null, '', '2');
INSERT INTO `gc_action` VALUES ('80', '18', '添加', 'add', '3', '', '20', '1', '0', '', '添加账户', '800px|600px', 'name,user,pwd,role_id,note,status,create_time,avatar', '', 'primary', '', '', '', 'fa fa-plus', '3', '', '', '', '', '1', '1', '1', null, null, null, null, '0', null, '', null);
INSERT INTO `gc_action` VALUES ('81', '18', '修改', 'update', '4', '', '', '1', '1', '', '修改账户', '800px|550px', 'name,user,role_id,note,status,create_time,avatar', '', 'success', '', '', '', 'fa fa-edit', '4', '', '', '', '', '1', '1', '1', null, null, null, null, '0', null, '', null);
INSERT INTO `gc_action` VALUES ('82', '18', '修改密码', 'updatePassword', '9', '', '', '1', '0', '', '修改密码', '600px|300px', 'pwd', '', 'warning', '', '', '', '', '6', '', '', '', null, null, null, null, null, null, null, null, '0', null, null, null);
INSERT INTO `gc_action` VALUES ('85', '19', '首页数据列表', 'index', '1', '', '', '0', '0', '', '', '600px|250px', '', '', 'primary', '', '', '', '', '1', '', '', 'pid,name', '', '1', '1', '1', null, null, null, null, '0', null, '', '1');
INSERT INTO `gc_action` VALUES ('87', '19', '添加', 'add', '3', '', '', '1', '0', '', '添加分组', '800px|400px', 'pid,name,status,description', '', 'primary', '', '', '', 'fa fa-plus', '3', '', '', '', '', '1', '1', '1', null, null, null, null, '0', null, '', null);
INSERT INTO `gc_action` VALUES ('88', '19', '修改', 'update', '4', '', '', '1', '1', '', '修改分组', '800px|400px', 'pid,name,status,description', '', 'primary', '', '', '', 'fa fa-edit', '4', '', '', '', '', '1', '1', '1', null, null, null, null, '0', null, '', null);
INSERT INTO `gc_action` VALUES ('2131', '19', '修改状态', 'updateExt', '16', null, '', '0', '0', '', '修改排序开关按钮操作', '', '', null, 'primary', '', '', '', '', '324', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, '', null);
INSERT INTO `gc_action` VALUES ('2132', '19', '设置权限', 'auth', '11', null, '', '1', '1', '', '弹窗连接', '100%|100%', '', null, 'primary', '', '', '', 'fa fa-plus', '2131', null, '', '', '/Base/auth', '1', '1', '1', null, null, null, null, null, null, '', '2');
INSERT INTO `gc_action` VALUES ('106', '19', '查看用户', 'viewUser', '11', '', '', '1', '1', '', '弹窗连接', '100%|100%', 'status', '', 'success', '', '', '', 'fa fa-eye', '8', '', '', '', '/User/index', '1', '1', '1', null, null, null, null, '0', null, '', '2');
INSERT INTO `gc_action` VALUES ('324', '19', '删除', 'delete', '5', null, '', '1', '1', '', '删除数据', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2132', '', '', '', '', '0', '1', '1', null, null, null, null, '0', null, '', null);
INSERT INTO `gc_action` VALUES ('462', '18', '删除', 'delete', '5', null, '', '1', '1', '', '删除数据', '', '', null, 'danger', '', '', '', 'fa fa-trash', '462', null, '', '', '', '0', '1', '1', null, null, null, null, '0', null, '', null);
INSERT INTO `gc_action` VALUES ('2133', '18', '修改状态', 'updateExt', '16', null, '', '0', '0', '', '修改排序开关按钮操作', '', '', null, 'primary', '', '', '', '', '2133', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, '', null);
INSERT INTO `gc_action` VALUES ('2140', '670', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2160', '673', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2160', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2159', '673', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|500px', 'title,upload_replace,thumb_status,thumb_width,thumb_height,thumb_type', null, 'success', null, null, null, 'fa fa-pencil', '2159', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2144', '670', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2144', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2145', '670', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|600px', 'application_name,username,url,ip,useragent,content,errmsg,type,create_time', null, 'info', '', '', '', 'fa fa-eye', '2145', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2146', '670', '导出', 'dumpData', '12', null, '20', '1', '0', null, '导出', '', '', null, 'warning', null, null, null, 'fa fa-download', '2146', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2158', '673', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|500px', 'title,upload_replace,thumb_status,thumb_width,thumb_height,thumb_type', null, 'primary', null, null, null, 'fa fa-plus', '2158', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2157', '673', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2156', '673', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2275', '41', '首页方法', 'index', '1', null, '', '1', '0', '', '', '', '', null, 'primary', '', '', '', '', '2275', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2615', '785', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '', 'wxapp_id,u_id,name,sex,phone,s_id,addres', null, 'success', '', '', '', 'fa fa-pencil', '2616', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2568', '770', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 'wxapp_id,appid,appsecret,mch_id,mch_key,template_new,template_grab,template_cancel,template_store,template_comment,template_pay,user_month_fee,user_year_fee,store_week_fee,store_month_fee,xcx_logo,back_logo,vip_content,privacy_content,help_content,toast_content,user_vip_switch,company_pay_switch,take_all_switch,second_check_switch,article_check_switch,index_quanzi_switch,index_toast_switch,index_rank_switch,index_module_switch,index_history_switch,is_anonymous_switch,runner_auth_switch,refund_cert,refund_key,qu_tips,ji_tips,shi_tips,wan_tips,withdraw_tips,home_adv_type,home_adv_id,second_adv_type,second_adv_id,step_price,start_fee,withdraw_min,is_address_show,is_address_must,is_attach_show,is_attach_must,is_servicetime_show,is_servicenum_show,mp_appid,mp_secret,is_open_reward,posting_instructions,mp_template_new,mp_template_grab,mp_template_cancel,mp_template_store,mp_template_pay,mp_code', null, 'primary', '', '', '', 'fa fa-plus', '2568', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2553', '762', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2553', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2551', '762', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|600px', 'name,create_time,img,s_id,wxapp_id,show_type,jump_type,url', null, 'primary', null, null, null, 'fa fa-plus', '2551', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2552', '762', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|550px', 'name,wxapp_id,s_id,img,show_type,url_type,url', null, 'success', '', '', '', 'fa fa-pencil', '2552', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2549', '762', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2548', '758', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2548', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2547', '758', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|450px', 's_id,account,pwd,status,wxapp_id', null, 'success', '', '', '', 'fa fa-pencil', '2547', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2786', '823', '万能订单', 'universalOrder', '3', null, '', '0', null, '', '创建数据', '800px|550px', 's_id,wxapp_id,qu_phone,qu_name,qu_sex,pay_time,cancel_reason', null, null, '', '', '', null, '2786', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2566', '770', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', '', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2565', '769', '首页方法', 'index', '1', null, '', '1', '0', '', '', '', '', null, 'primary', '', '', '', '', '2275', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2571', '772', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2850', '764', '修改开关', 'updateExt', '16', null, '', '0', '0', '', '修改排序开关按钮操作', '600px|350px', 'status', null, 'primary', '', '', '', '', '2850', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2434', '729', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2435', '729', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2436', '729', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '900px|750px', 'title,img,notice', null, 'primary', '', '', '', 'fa fa-plus', '2436', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2437', '729', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '900px|750px', 'title,img,notice', null, 'success', '', '', '', 'fa fa-pencil', '2437', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2438', '729', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2438', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2449', '733', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 't_id', null, 'primary', 'tickets', 't_id', 'a.*,b.title', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2450', '733', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2451', '733', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '600px|450px', 'date,t_id,createtime', null, 'primary', null, null, null, 'fa fa-plus', '2451', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2569', '770', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|100%', 'wxapp_id,appid,appsecret,mch_id,mch_key,template_new,template_grab,template_cancel,template_store,template_comment,template_pay,user_month_fee,user_year_fee,store_week_fee,store_month_fee,xcx_logo,back_logo,vip_content,privacy_content,help_content,toast_content,user_vip_switch,company_pay_switch,take_all_switch,second_check_switch,article_check_switch,index_quanzi_switch,index_toast_switch,index_rank_switch,index_module_switch,index_history_switch,is_anonymous_switch,runner_auth_switch,refund_cert,refund_key,qu_tips,ji_tips,shi_tips,wan_tips,withdraw_tips,home_adv_type,home_adv_id,second_adv_type,second_adv_id,step_price,start_fee,withdraw_min,is_address_show,is_address_must,is_attach_show,is_attach_must,is_servicetime_show,is_servicenum_show,mp_appid,mp_secret,is_open_reward,posting_instructions,mp_template_new,mp_template_grab,mp_template_cancel,mp_template_store,mp_template_pay,mp_code', null, 'success', '', '', '', 'fa fa-pencil', '2569', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2535', '752', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '600px|400px', 'account,pwd', null, 'success', '', '', '', 'fa fa-pencil', '2535', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2534', '752', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '600px|450px', 'account,pwd,create_time', null, 'primary', '', '', '', 'fa fa-plus', '2534', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2532', '752', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2556', '764', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', '', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2546', '758', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|500px', 'status,pwd,s_id,account,create_time,wxapp_id', null, 'primary', null, null, null, 'fa fa-plus', '2546', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2526', '747', '删除', 'delete', '31', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2526', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2623', '787', '用户领取优惠券', 'add', '3', null, '', '0', null, '', '创建数据', '', 'o_id,u_id,s_id,wxapp_id,use_status,create_time,update_time', null, null, '', '', '', null, '2624', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2754', '822', '修改', 'update', '4', null, '20', '1', '1', '', '编辑数据', '', 't_sex,auth_sid,t_name,phone', null, 'success', '', '', '', 'fa fa-edit', '2586', null, '', '', '', '1', '1', '1', '0', '1', '1', '0', '0', 'post', null, '2');
INSERT INTO `gc_action` VALUES ('2618', '785', '我的地址列表', 'index', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2614', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2672', '801', '首页数据列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2609', '782', '学校列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2559', '764', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|400px', 'wxapp_id,c_name,price,status', null, 'success', '', '', '', 'fa fa-pencil', '2559', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2558', '764', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|450px', 'create_time,price,c_name,wxapp_id,status', null, 'primary', null, null, null, 'fa fa-plus', '2558', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2514', '747', '订单详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '', 'order_no,code,date,uid,time,status,createtime,num,t_id,list', null, 'info', '', '', '', 'fa fa-eye', '2504', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2617', '785', '地址详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '', 'wxapp_id,u_id,name,sex,phone,s_id,addres,create_time', null, 'info', '', '', '', 'fa fa-eye', '2618', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2512', '747', '我的订单列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2510', '747', '提交预约信息', 'add', '3', null, '20', '1', '0', '', '添加', '', 'order_no,code,date,uid,time,status,createtime,num,t_id,list', null, 'primary', '', '', '', 'fa fa-plus', '2501', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2544', '758', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 's_id', null, 'primary', 'school', 's_id', 'a.*,b.s_name', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2543', '757', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2543', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2542', '757', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|100%', 's_name,plat_rate,school_rate,second_rate,edit_status,robot_key,step,coupon_list,reward_rate', null, 'success', '', '', '', 'fa fa-pencil', '2542', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2541', '757', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 'wxapp_id,s_name,plat_rate,school_rate,second_rate,edit_status,robot_key,step,create_time,coupon_list,reward_rate', null, 'primary', '', '', '', 'fa fa-plus', '2541', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2539', '757', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2640', '789', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|450px', 's_id,wxapp_id,title,address,create_time', null, 'primary', '', '', '', 'fa fa-plus', '2640', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2644', '792', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2574', '772', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '600px|350px', 'status', null, 'success', '', '', '', 'fa fa-pencil', '2574', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2577', '775', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2579', '775', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|100%', 'wxapp_id,oss_status,oss_default_type,ali_oss_accesskeyid,ali_oss_accesskeysecret,ali_oss_endpoint,ali_oss_bucket,qny_oss_accesskey,qny_oss_secretkey,qny_oss_bucket,qny_oss_domain,create_time', null, 'primary', null, null, null, 'fa fa-plus', '2579', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2580', '775', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|100%', 'wxapp_id,oss_status,oss_default_type,ali_oss_accesskeyid,ali_oss_accesskeysecret,ali_oss_endpoint,ali_oss_bucket,qny_oss_accesskey,qny_oss_secretkey,qny_oss_bucket,qny_oss_domain,create_time', null, 'success', null, null, null, 'fa fa-pencil', '2580', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2583', '776', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 's_id', null, 'primary', 'school', 's_id', 'a.*,b.s_name', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2582', '775', '修改开关', 'updateExt', '16', null, '', '0', '0', '', '修改排序开关按钮操作', '600px|300px', '', null, 'primary', '', '', '', '', '2582', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2584', '776', '修改排序开关按钮操作', 'updateExt', '6', null, '20', '0', '0', '', '修改状态', '600px|400px', 'is_runner,run_status', null, 'primary', '', '', '', 'fa fa-pencil', '2', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2755', '822', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '', 'wxapp_id,openid,nickname,avatar,balance,s_id,run_status,t_sex,auth_sid,deadtime,t_name,phone,create_time', null, 'info', '', '', '', 'fa fa-eye', '2588', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2586', '776', '修改', 'update', '4', null, '20', '1', '1', '', '编辑数据', '800px|400px', 'run_status,t_sex,auth_sid,phone', null, 'success', '', '', '', 'fa fa-edit', '2586', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2867', '847', '点赞或取消点赞', 'giveUp', '3', null, '', '0', null, '', '创建数据', '', '', null, null, '', '', '', null, '2867', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2757', '823', '取快递下单', 'takeExpressOrder', '3', null, '', '0', null, '', '创建数据', '800px|100%', 's_id,wxapp_id,img,remarks,sex_limit,weight,express_num,sh_addres,qu_addres,co_id,out_time,qu_phone,qu_name,qu_sex,pay_time,cancel_reason', null, null, '', '', '', null, '2757', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2863', '828', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|100%', '', null, 'info', '', '', '', 'fa fa-eye', '2863', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2624', '787', '可使用优惠券列表', 'index', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2623', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2791', '832', '修改排序开关按钮操作', 'updateExt', '6', null, '20', '0', '0', '', '修改状态', '600px|400px', 'is_runner,run_status', null, 'primary', '', '', '', 'fa fa-pencil', '2', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2790', '832', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 's_id', null, 'primary', 'school', 's_id', 'a.*,b.s_name', '', '1', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2594', '779', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2595', '779', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2596', '779', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 'wxapp_id,sms_status,sms_type,ali_sms_accesskeyid,ali_sms_accesskeysecret,ali_sms_signname,ali_sms_tempcode,cl_account,cl_pwd,cl_sign,create_time', null, 'primary', '', '', '', 'fa fa-plus', '2596', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2597', '779', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|100%', 'wxapp_id,sms_status,sms_type,ali_sms_accesskeyid,ali_sms_accesskeysecret,ali_sms_signname,ali_sms_tempcode,cl_account,cl_pwd,cl_sign', null, 'success', '', '', '', 'fa fa-pencil', '2597', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2616', '785', '删除', 'delete', '5', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2617', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2614', '785', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'wxapp_id,u_id,name,sex,phone,s_id,addres,create_time', null, 'primary', '', '', '', 'fa fa-plus', '2615', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2626', '790', '首页数据列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2627', '790', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'wxapp_id,u_id,account,name,price,type,status,create_time,update_time', null, 'primary', '', '', '', 'fa fa-plus', '2573', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2649', '793', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2926', '857', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2648', '792', '删除', 'delete', '5', null, '20', '0', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2648', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2647', '792', '修改', 'update', '4', null, '20', '0', '1', '', '修改', '600px|400px', 'title,image', null, 'success', '', '', '', 'fa fa-pencil', '2647', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2646', '792', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|450px', 's_id,wxapp_id,title,image,create_time', null, 'primary', '', '', '', 'fa fa-plus', '2646', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2642', '789', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2642', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2636', '789', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2637', '789', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2645', '792', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2641', '789', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|400px', 's_id,wxapp_id,title,address', null, 'success', '', '', '', 'fa fa-pencil', '2641', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2650', '793', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2651', '793', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 's_id,wxapp_id,image,title,pay,price,details,create_time,cid,uid,state,rotation,degree,stock,paystate,commission,examine', null, 'primary', '', '', '', 'fa fa-plus', '2651', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2652', '793', '修改', 'update', '4', null, '20', '0', '1', '', '修改', '800px|500px', 'title,pay,details,state,degree,examine', null, 'success', '', '', '', 'fa fa-pencil', '2652', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2653', '793', '删除', 'delete', '5', null, '20', '0', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2653', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2921', '854', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2658', '796', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2659', '796', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2798', '804', '查看列表', 'index', '1', null, '', '0', null, '', '', '', 'u_id', null, null, 'wechat_user', 'u_id', 'a.*,b.nickname,b.avatar', null, '2798', null, '', 'pid', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2794', '823', '个人订单列表', 'orderLst', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2794', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2795', '833', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2782', '829', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 's_id', null, 'primary', 'school', 's_id', 'a.*,b.s_name', '', '1', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2664', '798', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2665', '798', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2666', '799', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2667', '799', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2692', '809', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|400px', 'type_name,createtime,type_image,sort', null, 'primary', '', '', '', 'fa fa-plus', '2692', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2693', '809', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|400px', 'type_name,createtime,type_image,sort', null, 'success', '', '', '', 'fa fa-pencil', '2693', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2680', '803', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, '', null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2799', '804', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|500px', 's_id,wxapp_id,m_id,u_id,details,pid,create_time', null, 'primary', '', '', '', 'fa fa-plus', '2799', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2682', '805', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, '', null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2683', '805', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '', 'name,phone,create_time,uid,state,wxapp_id,s_id', null, 'success', null, null, null, 'fa fa-pencil', '2662', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2684', '805', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2663', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2669', '800', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', '', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2671', '800', '查看详情', 'view', '15', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2671', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2696', '811', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'type_id', null, 'primary', 'zh_business_type', 'type_id', 'a.*,b.type_name', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2694', '809', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2694', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2695', '809', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|400px', 'type_name,createtime,type_image,sort', null, 'info', '', '', '', 'fa fa-eye', '2695', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2792', '832', '修改', 'update', '4', null, '20', '1', '1', '', '编辑数据', '600px|400px', 'run_status,auth_sid', null, 'success', '', '', '', 'fa fa-edit', '2586', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2690', '809', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2691', '809', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2685', '806', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, '', null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2741', '798', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 'title,image,types,start,ladder,create_time,s_id,wxapp_id,appid', null, 'primary', '', '', '', 'fa fa-plus', '2741', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2781', '823', '寄快递下单', 'sendExpressOrder', '3', null, '', '0', null, '', '创建数据', '800px|550px', 's_id,wxapp_id,qu_phone,qu_name,qu_sex,pay_time,cancel_reason', null, null, '', '', '', null, '2781', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2868', '847', '转发数累加', 'forwardAccumulation', '3', null, '', '0', null, '', '创建数据', '', '', null, null, '', '', '', null, '2868', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2864', '828', '首页查看全部', 'index', '1', null, '', '0', null, '', '', '', 'u_id', null, null, 'wechat_user', 'u_id', 'a.*,b.nickname,b.avatar', null, '2864', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2740', '803', '详情', 'details', '15', null, '', '0', null, '', '', '', 'state,phone,name,other', null, null, '', '', '', null, '2740', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2697', '811', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', '0', '', '', '600px|350px', 'is_recommend', null, 'primary', '', '', '', '', '2', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2698', '811', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 's_id,wxapp_id,wxadmin_name,type_id,start_time,end_time,business_name,business_address,phone,expire_time,business_image,status,type,createtime,is_recommend,starting_fee,longitude,latitude', null, 'primary', '', '', '', 'fa fa-plus', '2698', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2699', '811', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|100%', 'wxadmin_name,type_id,start_time,end_time,business_name,business_address,phone,expire_time,business_image,status,type,createtime,is_recommend,starting_fee,longitude,latitude', null, 'success', null, null, null, 'fa fa-pencil', '2699', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2700', '811', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2700', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2701', '811', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|100%', 'wxadmin_name,type_id,start_time,end_time,business_name,business_address,phone,expire_time,business_image,status,type,createtime,is_recommend,starting_fee,longitude,latitude', null, 'info', 'zh_business_type', 'type_id', 'a.*,b.type_name', 'fa fa-eye', '2701', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2702', '812', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'business_id', null, 'primary', 'zh_business', 'business_id', 'a.*,b.business_name', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2703', '812', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2704', '812', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|400px', 'business_id,goods_type_name,createtime,sort', null, 'primary', null, null, null, 'fa fa-plus', '2704', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2705', '812', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|400px', 'business_id,goods_type_name,createtime,sort', null, 'success', null, null, null, 'fa fa-pencil', '2705', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2706', '812', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2706', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2707', '812', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|400px', 'business_id,goods_type_name,createtime,sort', null, 'info', 'zh_business', 'business_id', 'a.*,b.business_name', 'fa fa-eye', '2707', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2708', '813', '首页数据列表', 'index', '1', null, '20', '0', '0', 'select c.business_name,b.goods_type_name,a.* from gc_zh_goods a join gc_zh_business c on a.business_id=c.business_id join gc_zh_goods_type b on b.goods_type_id = a.goods_type_id', '', '', '', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2709', '813', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2710', '813', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|550px', 'goods_type_id,goods_name,price,goods_img,status,createtime,sort', null, 'primary', '', '', '', 'fa fa-plus', '2710', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2711', '813', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|550px', 'goods_type_id,goods_name,price,goods_img,status,createtime,sort', null, 'success', '', '', '', 'fa fa-pencil', '2711', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2712', '813', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2712', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2714', '815', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2715', '815', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2716', '815', '查看详情', 'view', '15', null, '20', '1', '1', '', '查看详情', '800px|100%', 'title,address,type,media_type,createtime,phone,pick_date,claim_method,remarks,image,video', null, 'info', '', '', '', 'fa fa-eye', '2716', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2717', '816', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'info_id', null, 'primary', 'zh_info', 'info_id', 'a.*,b.title', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2718', '816', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2719', '816', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2719', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2720', '818', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2721', '818', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2722', '818', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|100%', 's_id,wxapp_id,sort,class_name,icon,img,createtime,is_recommend,url,is_cate', null, 'primary', '', '', '', 'fa fa-plus', '2722', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2723', '818', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|600px', 'class_name,createtime,sort,icon,img,is_recommend,url,is_cate', null, 'success', null, null, null, 'fa fa-pencil', '2723', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2724', '818', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2724', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2725', '818', '查看详情', 'view', '15', null, '20', '1', '0', null, '查看详情', '800px|600px', 'class_name,createtime,sort,icon,img,is_recommend,url,is_cate', null, 'info', null, null, null, 'fa fa-eye', '2725', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2726', '819', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'class_id', null, 'primary', 'zh_forum_class', 'class_id', 'a.*,b.class_name', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2727', '819', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2871', '848', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '', 'start_time,end_time,business_name,business_address,phone,business_image,status,type,createtime', null, 'success', '', '', '', 'fa fa-pencil', '2699', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2730', '819', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2730', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2731', '819', '查看详情', 'view', '15', null, '20', '1', '1', '', '查看详情', '800px|100%', 'class_id,content,media_type,status,createtime,image,video', null, 'info', 'zh_forum_class', 'class_id', 'a.*,b.class_name', 'fa fa-eye', '2731', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2732', '820', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'article_id', null, 'primary', 'zh_articles', 'article_id', 'a.*,b.content', '', '1', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2733', '820', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2848', '844', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '600px|450px', 'article_id,contents,createtime', null, 'primary', '', '', '', 'fa fa-plus', '2848', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2849', '845', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', '', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', '0', '1', '0', '0', '0', 'get', null, '2');
INSERT INTO `gc_action` VALUES ('2865', '840', '删除评论', 'delete', '5', null, '', '0', null, '', '删除数据', '', '', null, null, '', '', '', null, '2865', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'delete', null, null);
INSERT INTO `gc_action` VALUES ('2739', '807', '查询', 'index', '1', null, '', '0', null, '', '', '', 's_id,wxapp_id,title,address', null, null, '', '', '', null, '2739', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2742', '798', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|100%', 'title,image,types,start,ladder,create_time,s_id,wxapp_id,appid', null, 'success', '', '', '', 'fa fa-pencil', '2742', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2743', '798', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2743', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2744', '798', '查看详情', 'view', '15', null, '20', '1', '0', null, '查看详情', '800px|100%', 'tltle,image,types,start,ladder,create_time,s_id,wxapp_id,appid', null, 'info', null, null, null, 'fa fa-eye', '2744', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2745', '782', '详情', 'view', '15', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2745', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2747', '803', '我的订单', 'lit', '1', null, '', '0', null, '', '', '', 'state,phone,name,other', null, null, '', '', '', null, '2747', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2861', '828', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|100%', 'u_id,cid,create_time,details,price,pay,s_id,wxapp_id,image,title,m_id,state', null, 'success', null, null, null, 'fa fa-pencil', '2861', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2751', '821', '首页数据列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2785', '823', '帮买订单', 'helpBuyOrder', '3', null, '', '0', null, '', '创建数据', '800px|550px', 's_id,wxapp_id,qu_phone,qu_name,qu_sex,pay_time,cancel_reason', null, null, '', '', '', null, '2785', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2787', '831', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'auth_sid', null, 'primary', 'school', 's_id', 'a.*,b.s_name', '', '1', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2788', '831', '修改排序开关按钮操作', 'updateExt', '6', null, '20', '0', '0', '', '修改状态', '600px|400px', 'is_runner,run_status', null, 'primary', '', '', '', 'fa fa-pencil', '2', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2789', '831', '修改', 'update', '4', null, '20', '1', '1', '', '编辑数据', '600px|400px', 'run_status', null, 'success', '', '', '', 'fa fa-edit', '2586', null, '', '', '', '1', '1', '1', null, '1', null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2796', '833', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2797', '833', '查看详情', 'view', '15', null, '20', '1', '1', '', '查看详情', '800px|100%', 'img,remarks,attach_file,sex_limit,weight,express_num,sh_name,sh_phone,sh_addres,service_num,y_money,total,t_money,s_money,fxs_money,food_money,createtime,good_details,refuse_reason,proof', null, 'info', '', '', '', 'fa fa-eye', '2797', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2800', '804', '删除', 'delete', '5', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2800', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2801', '834', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '800px|400px', 'u_id,m_id,s_id,wxapp_id', null, 'primary', '', '', '', 'fa fa-plus', '2801', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2802', '834', '删除', 'delete', '5', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2802', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2803', '803', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|450px', 'state,create_time,phone,name,other', null, 'primary', null, null, null, 'fa fa-plus', '2803', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2804', '835', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2805', '835', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2816', '836', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|400px', 'u_id,s_id,m_id,wxapp_id', null, 'primary', null, null, null, 'fa fa-plus', '2816', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2815', '834', '查看', 'index', '1', null, '', '0', null, '', '', '', 'm_id', null, null, 'dmh_market_goods', 'm_id', 'a.m_id,b.title,b.image,b.create_time,b.pay', null, '2815', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', '', null, null);
INSERT INTO `gc_action` VALUES ('2814', '836', '删除', 'delete', '5', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2814', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2810', '836', '列表', 'index', '1', null, '', '0', null, '', '', '', 'm_id', null, null, 'dmh_market_goods', 'm_id', 'a.m_id,b.title,b.image,b.create_time,b.pay', null, '2810', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2860', '828', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|100%', 'u_id,cid,create_time,details,price,pay,s_id,wxapp_id,image,title,m_id,state', null, 'primary', null, null, null, 'fa fa-plus', '2860', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2823', '838', '信息列表', 'getInformationList', '1', null, '', '0', null, '', '', '', 'title,address,type,media_type,createtime,phone,pick_date,claim_method,remarks', null, null, '', '', '', null, '2823', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2818', '838', '查看详情', 'detail', '15', null, '20', '1', '0', '', '查看详情', '', 'title,address,type,media_type,createtime,phone,pick_date,claim_method,remarks', null, 'info', '', '', '', 'fa fa-eye', '2716', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2819', '838', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'title,address,type,media_type,createtime,phone,pick_date,claim_method,remarks', null, 'primary', '', '', '', 'fa fa-plus', '2737', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2827', '840', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'info_id,content,createtime', null, 'primary', '', '', '', 'fa fa-plus', '2738', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2824', '838', '查询我发布的信息', 'getMyInformationList', '1', null, '', '0', null, '', '', '', 'title,address,type,phone,pick_date,claim_method,remarks', null, null, '', '', '', null, '2824', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2862', '828', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2862', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2828', '841', '首页数据列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2839', '843', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'class_id', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', '0', '1', '0', '0', '0', 'get', null, '2');
INSERT INTO `gc_action` VALUES ('2896', '819', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|100%', 'class_id,content,media_type,status,createtime,image,video', null, 'success', '', '', '', 'fa fa-pencil', '2896', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2897', '799', '查看详情', 'view', '15', null, '20', '1', '1', '', '查看详情', '800px|100%', 'img,remarks,attach_file,sex_limit,weight,express_num,start_time,door_time,sh_name,sh_sex,sh_phone,sh_addres,service_num,qu_addres,store_id,total,t_money,y_money,s_money,fxs_money,store_money,food_money,createtime,good_details,refuse_reason,proof,title', null, 'info', '', '', '', 'fa fa-eye', '2897', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2851', '764', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2851', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2845', '820', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2845', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2840', '843', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'class_id,openid,content,media_type,status,createtime,image', null, 'primary', '', '', '', 'fa fa-plus', '2728', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2844', '843', '查询我的文章', 'getMyArticle', '1', null, '', '0', null, '', '', '', 'class_id,openid,content,status,createtime,image', null, null, '', '', '', null, '2844', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2843', '843', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '', 'class_id,openid,content,media_type,status,createtime,image', null, 'info', '', '', '', 'fa fa-eye', '2731', null, '', '', '', '1', '1', '1', '0', '1', '0', '0', '0', 'get', null, '2');
INSERT INTO `gc_action` VALUES ('2856', '846', '最新订单列表', 'getNewestOrderList', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2794', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2859', '846', '历史订单列表', 'getHistoryOrderList', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2859', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2886', '848', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|100%', 'wxadmin_name,type_id,start_time,end_time,business_name,business_address,phone,expire_time,business_image,status,type,createtime', null, 'info', '', '', '', 'fa fa-eye', '2886', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2887', '834', '查看详情', 'view', '15', null, '20', '1', '0', null, '查看详情', '800px|450px', 'm_id,u_id,create_time,s_id,wxapp_id', null, 'info', null, null, null, 'fa fa-eye', '2887', null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2874', '849', '首页数据列表', 'index', '1', null, '20', '0', '0', '', '', '', 'business_id', null, 'primary', 'zh_business', 'business_id', 'a.*,b.business_name', '', '1', null, '', '', '', '1', '1', '1', '0', '1', '1', '0', '0', 'get', null, '2');
INSERT INTO `gc_action` VALUES ('2875', '849', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'business_id,goods_type_name,createtime', null, 'primary', '', '', '', 'fa fa-plus', '2704', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2876', '849', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '', 'business_id,goods_type_name,createtime', null, 'success', '', '', '', 'fa fa-pencil', '2705', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2877', '849', '删除', 'delete', '5', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2706', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'delete', null, null);
INSERT INTO `gc_action` VALUES ('2888', '849', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '600px|450px', 'business_id,goods_type_name,createtime', null, 'info', '', '', '', 'fa fa-eye', '2888', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2879', '850', '首页数据列表', 'index', '1', null, '20', '0', '0', 'select c.business_name,b.goods_type_name,a.* from gc_zh_goods a join gc_zh_business c on a.business_id=c.business_id join gc_zh_goods_type b on b.goods_type_id = a.goods_type_id', '', '', '', null, 'primary', '', '', '', '', '1', null, '', '', '', '1', '1', '1', '0', '1', '1', '0', '0', 'get', null, '2');
INSERT INTO `gc_action` VALUES ('2880', '850', '添加', 'add', '3', null, '20', '1', '0', '', '添加', '', 'goods_type_id,business_id,goods_name,price,goods_img,status,createtime', null, 'primary', '', '', '', 'fa fa-plus', '2710', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2881', '850', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '', 'goods_type_id,business_id,goods_name,price,goods_img,status,createtime', null, 'success', '', '', '', 'fa fa-pencil', '2711', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2889', '850', '查看详情', 'view', '15', null, '20', '1', '0', '', '查看详情', '800px|550px', 'goods_type_id,business_id,goods_name,price,goods_img,status,createtime', null, 'info', '', '', '', 'fa fa-eye', '2889', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2884', '850', '商品上架', 'goodShelves', '3', null, '', '0', null, '', '创建数据', '', 'status', null, null, '', '', '', null, '2884', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2890', '848', '商家订单列表', 'businessOrderList', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2890', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2895', '851', '首页数据列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'get', null, null);
INSERT INTO `gc_action` VALUES ('2898', '833', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|100%', 'attach_file,id,s_id,wxapp_id,status,img,ordersn,goods_id,type,remarks,sex_limit,weight,express_num,start_time,door_time,start_openid,end_openid,sh_name,sh_sex,sh_phone,sh_school,sh_addres,service_num,qu_addres,co_id,co_name,guess_price,store_id,y_money,total,t_money,s_money,fxs_money,store_money,food_money,createtime,updatetime,out_time,cancel_time,out_time_num,good_details,cancel_from,refuse_reason,proof,is_refuse,title,is_start_show', null, 'success', null, null, null, 'fa fa-pencil', '2898', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2900', '796', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2900', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2899', '799', '修改', 'update', '4', null, '20', '1', '1', null, '修改', '800px|100%', 'attach_file,s_id,wxapp_id,status,img,ordersn,type,remarks,sex_limit,weight,express_num,start_time,door_time,start_openid,end_openid,sh_name,sh_sex,sh_phone,sh_school,sh_addres,service_num,qu_addres,co_id,co_name,store_id,y_money,total,t_money,s_money,fxs_money,store_money,food_money,createtime,updatetime,out_time,cancel_time,out_time_num,good_details,cancel_from,refuse_reason,proof,is_refuse,title,is_start_show', null, 'success', null, null, null, 'fa fa-pencil', '2899', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2912', '795', '首页查看全部', 'index', '1', null, '', '0', '0', '', '', '', '', null, 'primary', '', '', '', '', '2912', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2924', '855', '列表', 'index', '1', null, '', '0', null, '', '', '', '', null, null, '', '', '', null, '2924', null, '', '', null, '1', '1', null, '0', '1', '1', '0', '0', 'post', null, null);
INSERT INTO `gc_action` VALUES ('2925', '857', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2920', '854', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, '1', null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2913', '795', '修改', 'update', '4', null, '20', '0', '1', '', '修改', '600px|400px', 'phone,name', null, 'success', '', '', '', 'fa fa-pencil', '2913', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2914', '795', '同意退款', 'delete', '5', null, '20', '1', '1', '', '删除', '', '', null, 'danger', '', '', '', 'fa fa-trash', '2914', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2915', '853', '首页数据列表', 'index', '1', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2916', '853', '修改排序开关按钮操作', 'updateExt', '16', null, '20', '0', null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2918', '853', '审批', 'update', '4', null, '20', '1', '1', '', '修改', '800px|400px', 'state,pay,alipay_name,alipay_account', null, 'success', '', '', '', 'fa fa-pencil', '2918', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2927', '857', '添加', 'add', '3', null, '20', '1', '0', null, '添加', '800px|450px', 'price,day,wxapp_id,s_id,addtime', null, 'primary', null, null, null, 'fa fa-plus', '2927', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2928', '857', '修改', 'update', '4', null, '20', '1', '1', '', '修改', '800px|400px', 'price,day,wxapp_id,s_id', null, 'success', '', '', '', 'fa fa-pencil', '2928', null, '', '', '', '1', '1', '1', null, null, null, null, null, null, null, '2');
INSERT INTO `gc_action` VALUES ('2929', '857', '删除', 'delete', '5', null, '20', '1', '1', null, '删除', '', '', null, 'danger', null, null, null, 'fa fa-trash', '2929', null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null);
INSERT INTO `gc_action` VALUES ('2931', '858', '首页数据列表', 'index', '1', null, '20', '0', null, '', '', '', '', null, null, '', '', '', null, '1', null, '', '', null, '1', '1', null, '0', '1', '0', '0', '0', 'post', null, null);

-- ----------------------------
-- Table structure for `gc_address`
-- ----------------------------
DROP TABLE IF EXISTS `gc_address`;
CREATE TABLE `gc_address` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `name` varchar(250) DEFAULT NULL COMMENT '联系人姓名',
  `sex` smallint(6) DEFAULT NULL COMMENT '性别',
  `phone` varchar(250) DEFAULT NULL COMMENT '手机号',
  `s_id` smallint(6) DEFAULT NULL COMMENT '学校id',
  `addres` varchar(250) DEFAULT NULL COMMENT '详情地址',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`a_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_address
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_application`
-- ----------------------------
DROP TABLE IF EXISTS `gc_application`;
CREATE TABLE `gc_application` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `app_dir` varchar(250) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `app_type` tinyint(4) DEFAULT NULL,
  `login_table` varchar(250) DEFAULT NULL,
  `login_fields` varchar(250) DEFAULT NULL,
  `domain` varchar(250) DEFAULT NULL,
  `pk` varchar(50) DEFAULT NULL COMMENT '登录表主键',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM AUTO_INCREMENT=212 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_application
-- ----------------------------
INSERT INTO `gc_application` VALUES ('1', '后台管理', 'gcadmin', '1', '1', '', '', '', null);
INSERT INTO `gc_application` VALUES ('209', 'api', 'api', '1', '2', '', '', 'http://test.fkynet.net/api', '');
INSERT INTO `gc_application` VALUES ('210', '子后台管理', 'accounts', '1', '1', 'account', 'account|pwd', '/accounts', 'id');
INSERT INTO `gc_application` VALUES ('211', '学校分后台', 'subschool', '1', '1', 'school_account', 'account|pwd', '/subschool', 'id');

-- ----------------------------
-- Table structure for `gc_article_pay`
-- ----------------------------
DROP TABLE IF EXISTS `gc_article_pay`;
CREATE TABLE `gc_article_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` int(11) NOT NULL,
  `s_id` int(11) NOT NULL,
  `ordersn` varchar(255) NOT NULL COMMENT '订单号',
  `a_id` int(11) NOT NULL COMMENT '文章id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `price` decimal(10,2) NOT NULL,
  `day` int(11) NOT NULL DEFAULT '0' COMMENT '置顶天数，type为1时有值',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0为未支付，1为已支付',
  `type` int(11) DEFAULT '1' COMMENT '1为置顶，2为打赏',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '抽成',
  `pay_time` varchar(255) DEFAULT NULL COMMENT '支付时间',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COMMENT='文章支付表';

-- ----------------------------
-- Records of gc_article_pay
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_config`
-- ----------------------------
DROP TABLE IF EXISTS `gc_config`;
CREATE TABLE `gc_config` (
  `name` varchar(50) NOT NULL,
  `data` varchar(255) NOT NULL,
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_config
-- ----------------------------
INSERT INTO `gc_config` VALUES ('water_status', '0');
INSERT INTO `gc_config` VALUES ('site_title', '格创跑腿SAAS管理系统');
INSERT INTO `gc_config` VALUES ('keyword', '小程序开发,APP开发');
INSERT INTO `gc_config` VALUES ('description', '格创校园跑腿SAAS管理系统');
INSERT INTO `gc_config` VALUES ('site_logo', 'http://test.fkynet.net/uploads/admin/202203/6242ba8f59b40.png');
INSERT INTO `gc_config` VALUES ('file_size', '2');
INSERT INTO `gc_config` VALUES ('copyright', '格创网络');
INSERT INTO `gc_config` VALUES ('file_type', 'gif,png,jpg,jpeg,doc,docx,xls,xlsx,csv,pdf,rar,zip,txt,mp4,flv,pem,pem');
INSERT INTO `gc_config` VALUES ('water_position', '5');
INSERT INTO `gc_config` VALUES ('water_logo', '');
INSERT INTO `gc_config` VALUES ('school_site_title', '分校管理');
INSERT INTO `gc_config` VALUES ('sub_site_title', '小程序管理');
INSERT INTO `gc_config` VALUES ('cert', '');

-- ----------------------------
-- Table structure for `gc_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `gc_coupon`;
CREATE TABLE `gc_coupon` (
  `o_id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `c_name` varchar(250) DEFAULT NULL COMMENT '优惠券名称',
  `price` varchar(250) DEFAULT NULL COMMENT '金额',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`o_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dates`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dates`;
CREATE TABLE `gc_dates` (
  `d_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(250) DEFAULT NULL COMMENT '日期',
  `t_id` smallint(6) DEFAULT NULL COMMENT '所属票务',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`d_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dates
-- ----------------------------
INSERT INTO `gc_dates` VALUES ('7', '1635827474', '1', '1635827478');
INSERT INTO `gc_dates` VALUES ('8', '1635913896', '1', '1635827500');
INSERT INTO `gc_dates` VALUES ('9', '1636000303', '1', '1635827507');

-- ----------------------------
-- Table structure for `gc_dmh_express`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_express`;
CREATE TABLE `gc_dmh_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `title` varchar(250) DEFAULT NULL COMMENT '名称',
  `address` varchar(250) DEFAULT NULL COMMENT '地址',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='快递站点';

-- ----------------------------
-- Records of gc_dmh_express
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_express_copy1`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_express_copy1`;
CREATE TABLE `gc_dmh_express_copy1` (
  `id` int(11) NOT NULL,
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `title` varchar(255) DEFAULT NULL COMMENT '名称',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快递点';

-- ----------------------------
-- Records of gc_dmh_express_copy1
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_footprint`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_footprint`;
CREATE TABLE `gc_dmh_footprint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `m_id` varchar(250) DEFAULT NULL COMMENT '商品id',
  PRIMARY KEY (`id`),
  KEY `m_id` (`m_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_footprint
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_goods_stay`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_goods_stay`;
CREATE TABLE `gc_dmh_goods_stay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `m_id` int(11) DEFAULT NULL COMMENT '商品id',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_goods_stay
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_category`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_category`;
CREATE TABLE `gc_dmh_market_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `title` varchar(250) DEFAULT NULL COMMENT '名称',
  `image` varchar(250) DEFAULT NULL COMMENT '图标',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='二手市场分类';


-- ----------------------------
-- Table structure for `gc_dmh_market_category_copy1`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_category_copy1`;
CREATE TABLE `gc_dmh_market_category_copy1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `title` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `image` varchar(255) DEFAULT NULL COMMENT '分类图标',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='二手市场分类';

-- ----------------------------
-- Records of gc_dmh_market_category_copy1
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_category_stay`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_category_stay`;
CREATE TABLE `gc_dmh_market_category_stay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '小程序id',
  `m_id` varchar(250) DEFAULT NULL COMMENT '商品',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户昵称',
  `details` varchar(250) DEFAULT NULL COMMENT '评价内容',
  `pid` varchar(250) DEFAULT '0' COMMENT '上级',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_market_category_stay
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_category_stay_copy1`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_category_stay_copy1`;
CREATE TABLE `gc_dmh_market_category_stay_copy1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `m_id` varchar(255) DEFAULT NULL COMMENT '商品id',
  `uid` varchar(255) DEFAULT NULL COMMENT '用户id',
  `details` longtext COMMENT '评价内容',
  `pid` int(11) DEFAULT '0' COMMENT '上级id默认为0',
  `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品评论表';

-- ----------------------------
-- Records of gc_dmh_market_category_stay_copy1
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_category_stay_copy2`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_category_stay_copy2`;
CREATE TABLE `gc_dmh_market_category_stay_copy2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `m_id` varchar(255) DEFAULT NULL COMMENT '商品id',
  `uid` varchar(255) DEFAULT NULL COMMENT '用户id',
  `details` longtext COMMENT '评价内容',
  `pid` int(11) DEFAULT '0' COMMENT '上级id默认为0',
  `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品评论表';

-- ----------------------------
-- Records of gc_dmh_market_category_stay_copy2
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_goods`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_goods`;
CREATE TABLE `gc_dmh_market_goods` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `image` varchar(250) DEFAULT NULL COMMENT '图标',
  `title` varchar(250) DEFAULT NULL COMMENT '名称',
  `pay` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `price` decimal(10,2) DEFAULT NULL COMMENT '原价',
  `details` varchar(250) DEFAULT NULL COMMENT '详情描述',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `cid` smallint(6) DEFAULT NULL COMMENT '分类',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `fabulous` int(11) DEFAULT NULL COMMENT '点赞',
  `state` smallint(6) DEFAULT '0' COMMENT '状态',
  `rotation` text COMMENT '轮播图',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `degree` varchar(250) DEFAULT NULL COMMENT '新旧程度',
  `stock` varchar(250) DEFAULT '0' COMMENT '库存',
  `examine` smallint(6) DEFAULT NULL COMMENT '审核状态',
  `name` varchar(255) DEFAULT NULL COMMENT '联系人',
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_market_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_goods_copy1`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_goods_copy1`;
CREATE TABLE `gc_dmh_market_goods_copy1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `image` longtext COMMENT '图标',
  `title` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `pay` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `price` decimal(10,2) DEFAULT NULL COMMENT '原价',
  `details` longtext COMMENT '详情描述',
  `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品列表';

-- ----------------------------
-- Records of gc_dmh_market_goods_copy1
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_order`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_order`;
CREATE TABLE `gc_dmh_market_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` varchar(250) DEFAULT NULL COMMENT '订单号',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `m_id` smallint(6) DEFAULT NULL COMMENT '商品',
  `uid` varchar(250) DEFAULT NULL COMMENT '用户id',
  `pay` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `state` tinyint(4) DEFAULT NULL COMMENT '状态',
  `phone` varchar(250) DEFAULT NULL COMMENT '下单人手机号',
  `name` varchar(250) DEFAULT NULL COMMENT '下单人姓名',
  `other` varchar(250) DEFAULT NULL COMMENT '其他信息',
  `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
  `purchase` varchar(250) DEFAULT '0' COMMENT '购买用户id',
  `paystate` int(11) DEFAULT '0' COMMENT '支付状态',
  `reason` varchar(250) DEFAULT NULL COMMENT '退款理由',
  `commission` decimal(10,2) DEFAULT NULL COMMENT '平台扣点',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_market_order
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_market_order_copy1`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_market_order_copy1`;
CREATE TABLE `gc_dmh_market_order_copy1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oid` varchar(255) DEFAULT NULL COMMENT '订单号',
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `m_id` varchar(255) DEFAULT NULL COMMENT '商品id',
  `uid` varchar(255) DEFAULT NULL COMMENT '用户id',
  `pay` varchar(255) DEFAULT NULL COMMENT '支付金额',
  `create_time` varchar(255) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品订单';

-- ----------------------------
-- Records of gc_dmh_market_order_copy1
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_modular`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_modular`;
CREATE TABLE `gc_dmh_modular` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL COMMENT '名称',
  `image` varchar(250) DEFAULT NULL COMMENT '缩略图',
  `types` smallint(6) DEFAULT NULL COMMENT '类型',
  `start` decimal(10,2) DEFAULT NULL COMMENT '起步价',
  `ladder` decimal(10,2) DEFAULT NULL COMMENT '阶梯价格',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `appid` varchar(250) DEFAULT NULL COMMENT ' 跳转的路径或者小程序APPID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `gc_dmh_rotation`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_rotation`;
CREATE TABLE `gc_dmh_rotation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `type` varchar(1) DEFAULT NULL COMMENT '类型 3外部小程序 2内部页面 1外链（WEB页面）',
  `url` varchar(255) NOT NULL COMMENT '链接',
  `wxapp_id` varchar(255) DEFAULT NULL COMMENT '平台id',
  `s_id` varchar(255) DEFAULT NULL COMMENT '学校id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小程序轮播图';

-- ----------------------------
-- Records of gc_dmh_rotation
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_school_carry`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_school_carry`;
CREATE TABLE `gc_dmh_school_carry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `state` smallint(6) DEFAULT NULL COMMENT '状态',
  `pay` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `alipay_name` varchar(250) DEFAULT NULL COMMENT '支付宝姓名',
  `alipay_account` varchar(250) DEFAULT NULL COMMENT '支付宝账户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_school_carry
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_school_order`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_school_order`;
CREATE TABLE `gc_dmh_school_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `u_id` int(11) DEFAULT NULL,
  `status` smallint(6) DEFAULT '1' COMMENT '未支付|1,待接单|2,待取货|3,到送达|7,已完成|4,已过期|5,未完成|6,已取消|8,等待取消抢单|9',
  `img` varchar(250) DEFAULT NULL COMMENT '图片',
  `ordersn` varchar(250) DEFAULT NULL COMMENT '订单号',
  `type` smallint(6) DEFAULT NULL COMMENT '订单类型   取件|1,寄件|2,超市食堂|3,无所不能/饮品|4',
  `remarks` varchar(250) DEFAULT NULL COMMENT '订单备注',
  `attach_file` varchar(250) DEFAULT NULL COMMENT '附件地址',
  `sex_limit` smallint(6) DEFAULT NULL COMMENT '接单员性别限制',
  `weight` varchar(250) DEFAULT NULL COMMENT '物品重量',
  `express_num` int(11) DEFAULT NULL COMMENT '件数',
  `start_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `door_time` int(11) DEFAULT NULL COMMENT '预计上门时间',
  `start_openid` varchar(250) DEFAULT NULL COMMENT '发起用户openid',
  `end_openid` varchar(250) DEFAULT NULL COMMENT '抢单用户openid',
  `sh_name` varchar(250) DEFAULT NULL COMMENT '收货姓名',
  `sh_sex` smallint(6) DEFAULT NULL COMMENT '收货性别',
  `sh_phone` varchar(250) DEFAULT NULL COMMENT '收货联系手机号',
  `sh_school` varchar(250) DEFAULT NULL COMMENT '收货学校',
  `sh_addres` varchar(250) DEFAULT NULL COMMENT '具体收货地址',
  `service_num` int(11) DEFAULT NULL COMMENT '服务人数',
  `qu_addres` varchar(250) DEFAULT NULL COMMENT '取货具体地址',
  `co_id` varchar(250) DEFAULT NULL COMMENT '优惠券id',
  `co_name` varchar(250) DEFAULT NULL COMMENT '名称',
  `store_id` varchar(250) DEFAULT NULL COMMENT '商家id',
  `y_money` decimal(10,2) DEFAULT NULL COMMENT '优惠券金额',
  `total` decimal(10,2) DEFAULT NULL COMMENT '总价（跑腿费）',
  `t_money` decimal(10,2) DEFAULT NULL COMMENT '实付金额',
  `s_money` decimal(10,2) DEFAULT '0.00' COMMENT '跑腿被抽取的手续费',
  `fxs_money` decimal(10,2) DEFAULT '0.00' COMMENT '学校收取手续费',
  `store_money` decimal(10,2) DEFAULT NULL COMMENT '商家抽成费用',
  `food_money` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `out_time` int(11) DEFAULT NULL COMMENT '订单过期时间',
  `cancel_time` int(11) DEFAULT NULL COMMENT '取消时间',
  `out_time_num` int(11) DEFAULT NULL COMMENT '超时',
  `good_details` tinytext COMMENT '商品详情',
  `cancel_from` varchar(250) DEFAULT NULL COMMENT '取消抢单来自哪一方:user:下单者,rider:骑手',
  `refuse_reason` varchar(250) DEFAULT NULL COMMENT '拒绝原因',
  `proof` varchar(250) DEFAULT NULL COMMENT '凭证',
  `is_refuse` int(11) DEFAULT NULL COMMENT '是否拒绝',
  `title` varchar(250) DEFAULT NULL COMMENT '标题',
  `is_start_show` int(11) DEFAULT NULL COMMENT '开始时间是否显示',
  `qu_phone` varchar(20) DEFAULT NULL COMMENT '寄件取货联系电话',
  `qu_name` varchar(250) DEFAULT NULL COMMENT '寄件取货姓名',
  `qu_sex` tinyint(4) DEFAULT NULL COMMENT '寄件 取货  性别 ',
  `pay_time` int(11) DEFAULT NULL COMMENT '用户支付时间',
  `cancel_reason` varchar(250) DEFAULT NULL COMMENT '取消原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_school_order
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_dmh_school_orders`
-- ----------------------------
DROP TABLE IF EXISTS `gc_dmh_school_orders`;
CREATE TABLE `gc_dmh_school_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `status` tinyint(4) DEFAULT '1' COMMENT '订单的完成状态    未支付|1,待接单|2,待取货|3,到送达|7,已完成|4,已过期|5,未完成|6,已取消|8,等待取消抢单|9',
  `img` varchar(250) DEFAULT NULL COMMENT '图片',
  `ordersn` varchar(250) DEFAULT NULL COMMENT '订单号',
  `goods_id` varchar(250) DEFAULT NULL COMMENT '商品id',
  `type` smallint(6) DEFAULT NULL COMMENT '订单类型   取件|1,寄件|2,超市食堂|3,无所不能/饮品|4',
  `remarks` varchar(250) DEFAULT NULL COMMENT '订单备注',
  `attach_file` varchar(250) DEFAULT NULL COMMENT '附件地址',
  `sex_limit` smallint(6) DEFAULT NULL COMMENT '接单员性别限制',
  `weight` varchar(250) DEFAULT NULL COMMENT '物品重量',
  `express_num` int(11) DEFAULT NULL COMMENT '件数',
  `start_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `door_time` int(11) DEFAULT NULL COMMENT '预计上门时间',
  `start_openid` varchar(250) DEFAULT NULL COMMENT '发起用户openid',
  `end_openid` varchar(250) DEFAULT NULL COMMENT '抢单用户openid',
  `sh_name` varchar(250) DEFAULT NULL COMMENT '收获姓名',
  `sh_sex` smallint(6) DEFAULT NULL COMMENT '收货性别',
  `sh_phone` varchar(250) DEFAULT NULL COMMENT '收获联系手机号',
  `sh_school` varchar(250) DEFAULT NULL COMMENT '收获学校',
  `sh_addres` varchar(250) DEFAULT NULL COMMENT '具体收货地址',
  `service_num` int(11) DEFAULT NULL COMMENT '服务人数',
  `sh_longitude` varchar(250) DEFAULT NULL COMMENT '收货经度参数',
  `sh_latitude` varchar(250) DEFAULT NULL COMMENT '收货纬度参数',
  `qu_addres` varchar(250) DEFAULT NULL COMMENT '取货具体地址',
  `qu_longitude` varchar(250) DEFAULT NULL COMMENT '取货经度参数',
  `qu_latitude` varchar(250) DEFAULT NULL COMMENT '取货纬度参数',
  `co_id` varchar(250) DEFAULT NULL COMMENT '优惠券id',
  `co_name` varchar(250) DEFAULT NULL COMMENT '名称',
  `guess_price` varchar(250) DEFAULT NULL COMMENT '商品预估价',
  `store_id` varchar(250) DEFAULT NULL COMMENT '商家id',
  `y_money` decimal(10,2) DEFAULT NULL COMMENT '优惠券金额',
  `total` decimal(10,2) DEFAULT NULL COMMENT '总价（跑腿费）',
  `t_money` decimal(10,2) DEFAULT NULL COMMENT '实付金额',
  `s_money` decimal(10,2) DEFAULT NULL COMMENT '跑腿被抽取的手续费',
  `fxs_money` decimal(10,2) DEFAULT NULL COMMENT '学校收取手续费',
  `store_money` decimal(10,2) DEFAULT NULL COMMENT '商家抽成费用',
  `food_money` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `out_time` int(11) DEFAULT NULL COMMENT '订单过期时间',
  `cancel_time` int(11) DEFAULT NULL COMMENT '取消时间',
  `out_time_num` int(11) DEFAULT NULL COMMENT '超时',
  `good_details` tinytext COMMENT '商品详情',
  `cancel_from` varchar(250) DEFAULT NULL COMMENT '取消抢单来自哪一方:user:下单者,rider:骑手',
  `refuse_reason` varchar(250) DEFAULT NULL COMMENT '拒绝原因',
  `proof` varchar(250) DEFAULT NULL COMMENT '凭证',
  `is_refuse` int(11) DEFAULT NULL COMMENT '是否拒绝',
  `title` varchar(250) DEFAULT NULL COMMENT '标题',
  `is_start_show` int(11) DEFAULT NULL COMMENT '开始时间是否显示',
  `qu_phone` varchar(20) DEFAULT NULL COMMENT '寄件取货联系电话',
  `qu_name` varchar(250) DEFAULT NULL COMMENT '寄件取货姓名',
  `qu_sex` tinyint(4) DEFAULT NULL COMMENT '寄件 取货  性别 ',
  `pay_time` int(11) DEFAULT NULL COMMENT '用户支付时间',
  `cancel_reason` varchar(250) DEFAULT NULL COMMENT '取消原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_dmh_school_orders
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_field`
-- ----------------------------
DROP TABLE IF EXISTS `gc_field`;
CREATE TABLE `gc_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL COMMENT '模块ID',
  `name` varchar(64) NOT NULL COMMENT '字段名称',
  `field` varchar(32) NOT NULL,
  `type` smallint(6) NOT NULL COMMENT '表单类型1输入框 2下拉框 3单选框 4多选框 5上传图片 6编辑器 7时间',
  `list_show` tinyint(4) DEFAULT NULL COMMENT '列表显示',
  `search_show` tinyint(4) DEFAULT NULL COMMENT '搜索显示',
  `search_type` tinyint(4) DEFAULT NULL COMMENT '1精确匹配 2模糊搜索',
  `config` varchar(255) DEFAULT NULL COMMENT '下拉框或者单选框配置',
  `is_post` tinyint(4) DEFAULT NULL COMMENT '是否前台录入',
  `is_field` tinyint(4) DEFAULT NULL,
  `align` varchar(24) DEFAULT NULL COMMENT '表格显示位置',
  `note` varchar(255) DEFAULT NULL COMMENT '提示信息',
  `message` varchar(255) DEFAULT NULL COMMENT '错误提示',
  `validate` varchar(32) DEFAULT NULL COMMENT '验证方式',
  `rule` mediumtext COMMENT '验证规则',
  `sortid` mediumint(9) DEFAULT '0' COMMENT '排序号',
  `sql` varchar(255) DEFAULT NULL COMMENT '字段配置数据源sql',
  `tab_menu_name` varchar(30) DEFAULT NULL COMMENT '所属选项卡名称',
  `default_value` varchar(255) DEFAULT NULL,
  `datatype` varchar(32) DEFAULT NULL COMMENT '字段数据类型',
  `length` varchar(5) DEFAULT NULL COMMENT '字段长度',
  `indexdata` varchar(20) DEFAULT NULL COMMENT '索引',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4000 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_field
-- ----------------------------
INSERT INTO `gc_field` VALUES ('134', '18', '编号', 'user_id', '1', '1', '0', '0', '', '0', '1', 'center', '', '', '', '', '1', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('135', '18', '真实姓名', 'name', '1', '1', '0', '0', '', '1', '1', 'center', '', '用户名不能为空', 'notEmpty', '', '2', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('136', '18', '用户名', 'user', '1', '1', '1', '0', '', '1', '1', 'center', '', '用户名不能为空', 'notEmpty', '', '3', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('137', '18', '密码', 'pwd', '5', '0', '0', '0', '', '1', '1', 'center', '', '6-21位数字字母组合', 'notEmpty', '/^(?![0-9]+\$)(?![a-zA-Z]+\$)[0-9A-Za-z]{6,20}\$/', '4', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('138', '18', '所属分组', 'role_id', '27', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '5', 'select role_id,name,pid from pre_role', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('140', '18', '备注', 'note', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '7', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('141', '18', '状态', 'status', '23', '1', '1', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '7', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('142', '18', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '8', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('143', '18', '所属分组', 'role_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '5', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('144', '19', '编号', 'role_id', '1', '1', '0', null, '', '0', '0', 'center', '', '', '', '', '1', '', '', null, null, null, null);
INSERT INTO `gc_field` VALUES ('145', '19', '角色', 'name', '1', '1', '0', '0', '', '1', '0', 'left', '', '名称不能为空', 'notEmpty', '', '3', '', '', '', '', '', '');
INSERT INTO `gc_field` VALUES ('146', '19', '状态', 'status', '23', '1', '0', '0', '开启|1,关闭|0', '1', '0', 'center', '', '', '', '', '2387', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('2388', '19', '描述', 'description', '6', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2388', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2411', '673', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2412', '673', '配置名称', 'title', '1', '1', '1', '0', null, '1', '0', 'center', null, null, null, null, '2412', null, null, null, 'varchar', '250', null);
INSERT INTO `gc_field` VALUES ('2413', '673', '覆盖同名文件', 'upload_replace', '23', '1', '0', '0', '开启|1,关闭|0', '1', '0', 'center', '', '', '', '', '2414', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('2414', '673', '缩图开关', 'thumb_status', '23', '1', '0', '0', '开启|1,关闭|0', '1', '0', 'center', '', '', '', '', '2413', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('2415', '673', '缩放宽度', 'thumb_width', '1', '1', '0', '0', '', '1', '0', 'center', '单位px', '', '', '', '2415', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('194', '41', '站点名称', 'site_title', '1', '0', '0', null, '', '1', '0', 'center', '', '', 'notEmpty', '', '194', '', '基本设置', '', '', '', '');
INSERT INTO `gc_field` VALUES ('195', '41', '关键词站点', 'keyword', '28', '0', '0', null, '', '0', '0', 'center', '', '', '', '', '197', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('196', '41', '站点描述', 'description', '6', '0', '0', null, '', '0', '0', 'center', '', '', '', '', '198', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('198', '41', '站点LOGO', 'site_logo', '8', '0', '0', null, '', '1', '0', 'center', '', '', '', '', '196', '', '基本设置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('200', '41', '上传文件大小', 'file_size', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '200', '', '上传配置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('1462', '41', '站点版权', 'copyright', '1', null, '0', null, '', '1', null, 'center', '', '', '', '', '700', null, '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('488', '41', '文件类型', 'file_type', '6', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '488', '', '上传配置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('3463', '821', '名称', 'name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2893', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3464', '821', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2143', '41', '水印位置', 'water_position', '29', null, null, null, '左上角水印|1,上居中水印|2,右上角水印|3,左居中水印|4,居中水印|5,右居中水印|6,左下角水印|7,下居中水印|8,右下角水印|9', '0', null, 'center', '', '', '', '', '2142', '', '上传配置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2142', '41', '水印状态', 'water_status', '3', null, null, null, '正常|1|success,禁用|0|danger', '0', null, 'center', '', '', '', '', '1462', '', '上传配置', '0', null, null, null);
INSERT INTO `gc_field` VALUES ('2144', '41', '水印图片', 'water_logo', '8', null, null, null, '', '0', null, 'center', '', '', '', '', '2143', '', '上传配置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2416', '673', '缩放高度', 'thumb_height', '1', '1', '0', '0', '', '1', '0', 'center', '单位px', '', '', '', '2416', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2417', '673', '缩图方式', 'thumb_type', '2', '1', '1', '0', '等比例缩放|1,缩放后填充|2,居中裁剪|3,左上角裁剪|4,右下角裁剪|5,固定尺寸缩放|6', '1', '0', 'center', '', '', '', '', '2417', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2401', '670', '创建时间', 'create_time', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2402', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2400', '670', '异常信息', 'errmsg', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2400', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2402', '670', '类型', 'type', '3', '1', '1', '0', '登录日志|1|primary,操作日志|2|success,异常日志|3|danger', '1', '1', 'center', '', '', '', '', '2401', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2399', '670', '请求内容', 'content', '6', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2399', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2397', '670', 'ip', 'ip', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2397', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2398', '670', 'useragent', 'useragent', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2398', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2396', '670', '请求url', 'url', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2396', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2394', '670', '应用名称', 'application_name', '2', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2394', 'select app_dir,app_dir from cd_application', '', '', 'varchar', '50', '');
INSERT INTO `gc_field` VALUES ('2395', '670', '操作用户', 'username', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2395', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2387', '19', '所属父类', 'pid', '2', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2', 'select role_id,name,pid from cd_role', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2393', '670', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2893', '762', '名称', 'name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2893', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2892', '762', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2906', '757', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '2878', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2717', '18', '头像', 'avatar', '8', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2717', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3472', '776', '性别', 't_sex', '3', '1', '0', '0', '女|2|success,男|1|danger', '1', '1', 'center', '', '', '', '', '3068', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2726', '729', '编号', 't_id', '1', '1', '0', '0', '', '0', '1', 'center', '', '', '', '', '1', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2727', '729', '名称', 'title', '1', '1', '1', '1', '', '1', '1', 'center', '', '', '', '', '2727', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2728', '729', '图片', 'img', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2728', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2729', '729', '购票须知', 'notice', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2729', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2743', '733', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2744', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2744', '733', '票务名称', 'title', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '2743', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2741', '733', '日期', 'date', '31', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2741', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2742', '733', '所属票务', 't_id', '2', '0', '1', '1', '', '1', '1', 'center', '', '', '', '', '2742', 'select t_id,title from cd_tickets', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2740', '733', '编号', 'd_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3018', '772', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3018', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2907', '757', '优惠券列表', 'coupon_list', '27', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3970', 'select o_id,c_name from gc_coupon where status=1', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2898', '41', '分学校站点名称', 'school_site_title', '1', null, null, null, '', '1', null, 'center', '', '', '', '', '195', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2897', '41', '子站点名称', 'sub_site_title', '1', null, null, null, '', '1', null, 'center', '', '', 'notEmpty', '', '194', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2896', '762', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3207', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2895', '762', '图片', 'img', '8', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2896', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2875', '752', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2875', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2874', '752', '登录密码', 'pwd', '1', '0', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2874', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2873', '752', '登录账号', 'account', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty,unique', '', '2873', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2872', '752', '编号', 'wxapp_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3102', '782', '优惠券列表', 'coupon_list', '27', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2907', 'select o_id,c_name from gc_coupon where status=1', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3101', '782', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2878', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2904', '764', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2904', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2903', '764', '金额', 'price', '1', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '2903', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2902', '764', '优惠券名称', 'c_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2902', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3484', '822', '骑手审核状态', 'run_status', '3', '1', '1', '0', '未申请审核|0,审核中|1,审核通过|2,审核拒绝|-1', '1', '0', 'center', '', '', '', '', '3067', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2901', '764', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '2901', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3482', '822', '余额', 'balance', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3063', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('2900', '764', '编号', 'o_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3483', '822', '所属学校', 's_id', '2', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3064', 'select s_id,s_name from gc_school', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3016', '770', '提现最低金额', 'withdraw_min', '1', '0', '0', '0', '', '1', '1', 'center', '', '请输入正确的金额', '', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '3016', '', '财务设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3015', '770', '起步费', 'start_fee', '1', '0', '0', '0', '', '1', '1', 'center', '', '请输入正确的金额', '', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '3015', '', '财务设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3107', '782', '二手市场抽成百分比', 'second_rate', '20', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '/^[0-9]*\$/', '2882', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3105', '782', '机器人key', 'robot_key', '1', '0', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2884', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3106', '782', '是否允许分校修改跑腿抽佣', 'edit_status', '2', '1', '0', '0', '允许|1|success,不允许|0|danger', '1', '0', 'center', '', '', 'notEmpty', '', '2883', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3104', '782', '阶梯选择配置', 'step', '6', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2885', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3103', '782', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2906', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2966', '769', '认证说明', 'explain', '16', null, null, null, '', '1', '0', 'center', '', '', '', '', '2949', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2963', '769', '小程序appid', 'appid', '1', null, null, null, '', '1', '0', 'center', '', '', '', '', '2861', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2964', '769', '小程序密钥', 'appsecret', '1', null, null, null, '', '1', '0', 'center', '', '', '', '', '2897', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2965', '769', '隐私协议', 'agreement', '16', null, null, null, '', '1', '0', 'center', '', '', '', '', '2931', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('3014', '770', '阶梯价格', 'step_price', '1', '0', '0', '0', '', '1', '1', 'center', '', '请输入正确的金额', '', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '3014', '', '财务设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3608', '829', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3474', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2889', '758', '密码', 'pwd', '1', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2890', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2888', '758', '管理学校', 's_id', '2', '0', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2888', 'select s_id,s_name from gc_school', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2887', '758', '账号', 'account', '1', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty,unique', '', '2889', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2886', '758', '编号', 'a_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2885', '757', '创建时间', 'create_time', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '2906', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2884', '757', '阶梯选择配置', 'step', '6', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2885', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2834', '747', '门票id', 't_id', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2812', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2833', '747', '人数', 'num', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2811', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2832', '747', '用户id', 'uid', '24', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '2807', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2831', '747', '添加时间', 'createtime', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2810', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2830', '747', '状态', 'status', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '2809', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2829', '747', '时间段', 'time', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2808', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2828', '747', '日期', 'date', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2806', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2827', '747', '核销码', 'code', '30', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2805', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2826', '747', '订单号', 'order_no', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2804', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2825', '747', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2905', '764', '状态', 'status', '23', '1', '0', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '2905', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('2835', '747', '预约人员', 'list', '1', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2824', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3124', '785', '详情地址', 'addres', '1', null, '0', '0', '', '1', '1', null, null, '', 'notEmpty', '', '3124', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3123', '785', '学校id', 's_id', '3', null, '0', '0', '', '1', '1', null, null, '', 'notEmpty', '', '3123', '', null, '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3121', '785', '性别', 'sex', '3', null, '0', '0', '（0男1女）', '1', '1', null, null, '', 'notEmpty', '', '3121', '', null, '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3017', '772', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3122', '785', '手机号', 'phone', '1', null, '0', '0', '', '1', '1', null, null, '手机号有误', 'notEmpty', '/^1[3456789]\\\\d{9}\$/', '3122', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3120', '785', '联系人姓名', 'name', '1', null, '0', '0', '', '1', '1', null, null, '', 'notEmpty', '', '3120', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2891', '758', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3203', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3465', '821', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3207', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3462', '798', ' 跳转的路径或者小程序APPID', 'appid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3462', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2894', '762', '所属学校', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '2895', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2883', '757', '机器人key', 'robot_key', '1', '0', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2884', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2882', '757', '是否允许分校修改跑腿抽佣', 'edit_status', '2', '1', '0', '0', '允许|1|success,不允许|0|danger', '1', '1', 'center', '', '', 'notEmpty', '', '2883', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2881', '757', '二手市场抽成百分比', 'second_rate', '20', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '/^[0-9]*\$/', '2882', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2880', '757', '学校抽成', 'school_rate', '20', '1', '0', '0', '', '1', '1', 'center', '', '请输入1-100的整数', 'notEmpty', '/^[0-9]*\$/', '2881', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3995', '758', '学校名称', 's_name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3995', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2878', '757', '平台抽成', 'plat_rate', '20', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '/^[0-9]*\$/', '2880', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('2877', '757', '编号', 's_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2962', '769', '子站点名称', 'sub_site_title', '1', null, null, null, '', '1', '0', 'center', '', '', 'notEmpty', '', '194', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2960', '769', '水印图片', 'water_logo', '8', null, null, null, '', '1', '0', 'center', '', '', '', '', '2143', '', '上传配置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2961', '769', '分学校站点名称', 'school_site_title', '1', null, null, null, '', '1', '0', 'center', '', '', '', '', '195', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2958', '769', '水印位置', 'water_position', '29', null, null, null, '左上角水印|1,上居中水印|2,右上角水印|3,左居中水印|4,居中水印|5,右居中水印|6,左下角水印|7,下居中水印|8,右下角水印|9', '1', '0', 'center', '', '', '', '', '2142', '', '上传配置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2959', '769', '水印状态', 'water_status', '3', null, null, null, '正常|1|success,禁用|0|danger', '1', '0', 'center', '', '', '', '', '1462', '', '上传配置', '0', null, null, null);
INSERT INTO `gc_field` VALUES ('2957', '769', '绑定域名', 'domain', '1', '0', '0', '0', '', '1', '0', 'center', '上传目录绑定域名访问，请解析域名到 /public/upload目录  前面带上http://  非必填项', '', '', '', '2144', '', '上传配置', '', '', '', '');
INSERT INTO `gc_field` VALUES ('2956', '769', '文件类型', 'file_type', '6', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '488', '', '上传配置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('2967', '769', '大城市', 'ciy', '16', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '2967', '', '上传配置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2953', '769', '站点LOGO', 'site_logo', '8', '0', '0', null, '', '1', '0', 'center', '', '', '', '', '196', '', '基本设置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('2954', '769', '上传文件大小', 'file_size', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '200', '', '上传配置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('3059', '776', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3059', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2952', '769', '站点描述', 'description', '6', '0', '0', null, '', '1', '0', 'center', '', '', '', '', '198', '', '基本设置', null, null, null, null);
INSERT INTO `gc_field` VALUES ('2951', '769', '关键词站点', 'keyword', '28', '0', '0', null, '', '1', '0', 'center', '', '', '', '', '197', '', '基本设置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2950', '769', '站点名称', 'site_title', '1', '0', '0', null, '', '1', '0', 'center', '', '', 'notEmpty', '', '194', '', '基本设置', '', '', '', '');
INSERT INTO `gc_field` VALUES ('3058', '776', '编号', 'u_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2968', '41', '证书上传', 'cert', '10', null, null, null, '', '0', null, 'center', '', '', '', '', '2968', '', '上传配置', '', null, null, null);
INSERT INTO `gc_field` VALUES ('2969', '770', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('2970', '770', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '2970', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2971', '770', '小程序appid', 'appid', '1', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '2971', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2972', '770', '小程序密钥', 'appsecret', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2972', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2973', '770', '商户号', 'mch_id', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2973', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2974', '770', '商户号密钥', 'mch_key', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2974', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2975', '770', '骑手订阅消息模板ID', 'template_new', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2975', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2976', '770', '用户抢单提醒', 'template_grab', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2976', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2978', '770', '商家订阅消息', 'template_store', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2978', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2977', '770', '取消订单消息提醒', 'template_cancel', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2977', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2979', '770', '圈子留言订阅消息', 'template_comment', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2979', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2980', '770', '二手市场支付订阅消息', 'template_pay', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2980', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2981', '770', '用户会员包月价格', 'user_month_fee', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2981', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2982', '770', '用户会员包年价格', 'user_year_fee', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2982', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2983', '770', '商家周卡价格', 'store_week_fee', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2983', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2985', '770', '小程序个人中心logo', 'xcx_logo', '8', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2985', '', '图文设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2984', '770', '商家月卡价格', 'store_month_fee', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2984', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2986', '770', '分后台登录背景图', 'back_logo', '8', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2986', '', '图文设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('2987', '770', '会员协议说明', 'vip_content', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2987', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2992', '770', '企业付款到零钱', 'company_pay_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2992', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2988', '770', '跑腿认证隐私条款', 'privacy_content', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2988', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2989', '770', '帮助中心', 'help_content', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2989', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2990', '770', '弹框内容', 'toast_content', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2990', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('2991', '770', '会员充值功能', 'user_vip_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2991', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2993', '770', '骑手是否可跨校接单', 'take_all_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2993', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2994', '770', '二手市场开启审核', 'second_check_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2994', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2995', '770', '论坛文章开启审核', 'article_check_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2995', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2996', '770', '首页论坛是否显示', 'index_quanzi_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2996', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2997', '770', '首页弹窗是否显示', 'index_toast_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2997', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2998', '770', '首页排行榜是否显示', 'index_rank_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2998', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('2999', '770', '首页模块是否显示', 'index_module_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '2999', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3000', '770', '首页历史订单是否显示', 'index_history_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '3000', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3001', '770', '论坛匿名是否开启', 'is_anonymous_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '3001', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3002', '770', '个人中心跑腿认证是否显示', 'runner_auth_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '3002', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3003', '770', '退款证书（apiclient_cert.pem）', 'refund_cert', '10', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3003', '', '小程序设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3004', '770', '退款证书（apiclient_key.pem）', 'refund_key', '10', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3004', '', '小程序设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3005', '770', '取快递文本框提示语句', 'qu_tips', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3005', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3006', '770', '寄快递文本框提示语句', 'ji_tips', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3006', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3007', '770', '食堂超市文本框提示语句', 'shi_tips', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3007', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3008', '770', '万能任务文本框提示语句', 'wan_tips', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3008', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3009', '770', '提现提示语句配置', 'withdraw_tips', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3009', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3010', '770', '首页广告位类型', 'home_adv_type', '3', '0', '0', '0', 'banner|1|success,视频|2|primary', '1', '1', 'center', '', '', '', '', '3010', '', '其他设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3011', '770', '首页广告位id', 'home_adv_id', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3011', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3012', '770', '二手市场广告位类型', 'second_adv_type', '3', '0', '0', '0', 'banner|1|success,视频|2|primary', '1', '1', 'center', '', '', '', '', '3012', '', '其他设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3013', '770', '二手市场广告位id', 'second_adv_id', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3013', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3019', '772', '用户id', 'u_id', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3019', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3020', '772', '提现账号', 'account', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3020', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3021', '772', '提现金额', 'price', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3022', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3022', '772', '提现类型', 'type', '3', '1', '1', '0', '支付宝|1,企业付款|2', '1', '1', 'center', '', '', '', '', '3023', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3023', '772', '状态', 'status', '3', '1', '1', '0', '待审核|1|primary,审核通过|2|success,审核拒绝|3|danger', '1', '1', 'center', '', '', '', '', '3024', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3024', '772', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3025', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3025', '772', '修改时间', 'update_time', '25', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3026', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3026', '772', '提现姓名', 'name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3021', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3045', '775', '编号', 'u_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3046', '775', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3046', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3047', '775', '是否开启远程附件', 'oss_status', '23', '1', '0', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '3047', '', '类型选择', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3048', '775', '类型', 'oss_default_type', '3', '1', '0', '0', '阿里|ali,七牛|qiniuyun', '1', '1', 'center', '', '', '', '', '3048', '', '类型选择', '', 'varchar', '200', '');
INSERT INTO `gc_field` VALUES ('3049', '775', 'Access Key ID', 'ali_oss_accesskeyid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3049', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3050', '775', 'Access Key Secret', 'ali_oss_accesskeysecret', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3050', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3051', '775', 'url', 'ali_oss_endpoint', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3051', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3052', '775', 'bucket', 'ali_oss_bucket', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3052', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3053', '775', 'Accesskey', 'qny_oss_accesskey', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3053', '', '七牛云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3054', '775', 'Secretkey', 'qny_oss_secretkey', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3054', '', '七牛云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3055', '775', 'bucket', 'qny_oss_bucket', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3055', '', '七牛云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3056', '775', 'url', 'qny_oss_domain', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3056', '', '七牛云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3057', '775', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3057', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3060', '776', '昵称', 'nickname', '1', '1', '1', '1', '', '1', '0', 'center', '', '', '', '', '3061', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3061', '776', '头像', 'avatar', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3062', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3062', '776', 'openid', 'openid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3060', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3063', '776', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3474', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3064', '776', '余额', 'balance', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3063', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3637', '832', 'openid', 'openid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3060', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3066', '776', '所属学校', 's_id', '2', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3064', 'select s_id,s_name from gc_school', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3067', '776', '骑手审核状态', 'run_status', '3', '1', '1', '0', '未申请审核|0,审核中|1,审核通过|2,审核拒绝|-1', '1', '1', 'center', '', '', '', '', '3067', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3068', '776', '认证学校id', 'auth_sid', '2', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3069', 'select s_id,s_name from gc_school', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3069', '776', '所属学校名称', 's_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3065', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3636', '832', '头像', 'avatar', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3062', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3634', '832', '编号', 'u_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3635', '832', '昵称', 'nickname', '1', '1', '1', '1', '', '1', '1', 'center', '', '', '', '', '3061', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3633', '832', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3059', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3076', '779', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3077', '779', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3077', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3078', '779', '平台类型', 'sms_type', '3', '1', '0', '0', '阿里云|ali,创蓝|cl', '1', '1', 'center', '', '', '', '', '3079', '', '基本配置', '', 'varchar', '200', '');
INSERT INTO `gc_field` VALUES ('3089', '779', '是否使用短信通知', 'sms_status', '23', '1', '0', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '3078', '', '基本配置', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3080', '779', '阿里云AccessKeyId', 'ali_sms_accesskeyid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3080', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3081', '779', '阿里云AccessKeySecret', 'ali_sms_accesskeysecret', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3081', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3082', '779', '阿里云签名名称', 'ali_sms_signname', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3082', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3083', '779', '阿里云模板CODE', 'ali_sms_tempcode', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3083', '', '阿里云配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3084', '779', '创蓝账号', 'cl_account', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3084', '', '创蓝配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3085', '779', '创蓝密码', 'cl_pwd', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3085', '', '创蓝配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3086', '779', '创蓝签名名称', 'cl_sign', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3086', '', '创蓝配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3632', '832', '性别', 't_sex', '3', '1', '0', '0', '女|1|success,男|0|danger', '1', '1', 'center', '', '', '', '', '3068', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3088', '779', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3088', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3481', '822', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3474', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3480', '822', 'openid', 'openid', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3060', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3479', '822', '头像', 'avatar', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3062', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3478', '822', '昵称', 'nickname', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3061', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3476', '822', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3059', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3475', '822', '性别', 't_sex', '3', '1', '1', '0', '女|1|success,男|0|danger', '1', '0', 'center', '', '', 'notEmpty', '', '3068', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3108', '782', '学校抽成', 'school_rate', '20', '1', '0', '0', '', '1', '0', 'center', '', '请输入1-100的整数', 'notEmpty', '/^[0-9]*\$/', '2881', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3109', '782', '学校名称', 's_name', '1', '1', '0', '1', '', '1', '0', 'center', '', '', 'notEmpty,unique', '', '2879', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3110', '782', '平台抽成', 'plat_rate', '20', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '/^[0-9]*\$/', '2880', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3111', '782', '编号', 's_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3119', '785', '用户id', 'u_id', '24', null, '1', '0', '', '1', '1', null, null, '', '', '', '3119', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3118', '785', '平台id', 'wxapp_id', '1', null, '1', '0', '', '1', '1', null, null, '', '', '', '3118', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3125', '785', '创建时间', 'create_time', '12', null, '0', '0', '', '1', '1', null, null, '', '', '', '3125', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3282', '799', '附件地址', 'attach_file', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3282', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3137', '787', '创建时间', 'create_time', '12', null, '0', '0', '', '1', '1', null, null, '', '', '', '3137', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3136', '787', '使用状态（0为未使用，1为已使用）', 'use_status', '3', null, '1', '0', '', '1', '1', null, null, '', '', '', '3136', '', null, '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3135', '787', '平台id', 'wxapp_id', '1', null, '0', '0', '', '1', '1', null, null, '', '', '', '3135', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3134', '787', '学校id', 's_id', '1', null, '1', '0', '', '1', '1', null, null, '', '', '', '3134', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3133', '787', '用户id', 'u_id', '24', null, '1', '0', '', '1', '1', null, null, '', '', '', '3133', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3132', '787', '优惠券id', 'o_id', '1', null, '0', '0', '', '1', '1', null, null, '', 'notEmpty', '', '3132', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3138', '787', '更新时间', 'update_time', '1', null, '0', '0', '', '1', '1', null, null, '', '', '', '3138', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3139', '787', '优惠券名称', 'c_name', '1', null, '0', '0', '', '1', '1', null, null, '', '', '', '3139', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3140', '787', '价格', 'price', '13', null, '0', '0', '', '1', '1', null, null, '', '', '', '3140', '', null, '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3141', '776', '到期时间', 'deadtime', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3141', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3142', '790', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3018', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3143', '790', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3144', '790', '用户id', 'u_id', '24', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3019', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3145', '790', '提现账号', 'account', '1', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3020', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3146', '790', '提现金额', 'price', '1', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '/(^[1-9]\\\\d*(\\\\.\\\\d{1,2})?\$)|(^0(\\\\.\\\\d{1,2})?\$)/', '3022', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3147', '790', '提现类型', 'type', '3', '1', '0', '0', '支付宝|1,企业付款|2', '1', '0', 'center', '', '', '', '', '3023', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3148', '790', '状态', 'status', '1', '1', '1', '0', '待审核|1|primary,审核通过|2|success,审核拒绝|3|danger', '1', '0', 'center', '', '', '', '', '3024', '', '', '1', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3149', '790', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3025', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3150', '790', '修改时间', 'update_time', '25', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3026', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3151', '790', '提现姓名', 'name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3021', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3170', '792', '创建时间', 'create_time', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3170', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3169', '792', '图标', 'image', '8', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3169', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3168', '792', '名称', 'title', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3168', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3167', '792', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3167', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3166', '792', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3166', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3158', '789', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3159', '789', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3159', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3160', '789', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3160', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3171', '793', '编号', 'm_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3161', '789', '名称', 'title', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3161', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3162', '789', '地址', 'address', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3162', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3165', '792', '编号', 'id', '1', '1', '0', '0', '', '0', '1', 'center', '', '', '', '', '1', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3164', '789', '添加时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3164', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3172', '793', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3172', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3173', '793', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3173', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3174', '793', '图标', 'image', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3174', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3175', '793', '名称', 'title', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3175', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3176', '793', '金额', 'pay', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3176', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3177', '793', '原价', 'price', '13', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3177', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3178', '793', '详情描述', 'details', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3178', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3179', '793', '创建时间', 'create_time', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3179', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3181', '793', '分类', 'cid', '2', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3181', 'select id,title from gc_dmh_market_category', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3183', '795', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3184', '795', '订单号', 'oid', '1', '1', '1', '0', '', '0', '1', 'center', '', '', '', '', '3184', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3185', '795', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3185', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3186', '795', '平台id', 'wxapp_id', '15', '2', '0', '0', '', '0', '1', 'center', '', '', '', '', '3186', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3187', '795', '商品', 'm_id', '2', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3187', 'select id,title from gc_dmh_market_goods', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3188', '795', '用户id', 'uid', '1', '2', '0', '0', '', '0', '1', 'center', '', '', '', '', '3188', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3189', '795', '价格', 'pay', '13', '1', '0', '0', '', '0', '1', 'center', '', '', '', '', '3189', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3191', '795', '状态', 'state', '23', '1', '1', '0', '已完成|2,已支付|1,未支付|0', '1', '1', 'center', '', '', '', '', '3191', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3190', '795', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3195', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3192', '795', '下单人手机号', 'phone', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3192', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3193', '795', '下单人姓名', 'name', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3193', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3194', '795', '其他信息', 'other', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3194', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3195', '796', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3196', '796', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3196', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3197', '796', '小程序id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3197', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3198', '796', '商品', 'm_id', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3198', 'select id,title from gc_dmh_market_goods', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3199', '796', '用户昵称', 'u_id', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3199', 'select id,nickname from gc_wechat_user', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3200', '796', '评价内容', 'details', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3200', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3937', '853', '编号', 'id', '1', '1', '0', null, null, '1', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3201', '796', '上级', 'pid', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3201', '', '', '0', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3202', '796', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3202', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3203', '758', '平台id', 'wxapp_id', '15', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2887', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3204', '762', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '2894', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3205', '762', '显示位置', 'show_type', '3', '1', '1', '0', '首页|1,二手市场|2,论坛|3,失物招领|4,商家列表|5', '1', '1', 'center', '', '', '', '', '3204', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3206', '762', '跳转类型', 'url_type', '3', '1', '1', '0', '内部页面|1,外部小程序|2,外部链接|3', '1', '1', 'center', '', '', '', '', '3205', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3207', '762', 'url地址', 'url', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3206', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3938', '853', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3938', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3607', '829', 'openid', 'openid', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3060', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3606', '829', '头像', 'avatar', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3062', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3605', '829', '昵称', 'nickname', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3061', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3603', '829', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3059', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3602', '829', '性别', 't_sex', '3', '1', '0', '0', '女|2|success,男|1|danger', '1', '0', 'center', '', '', '', '', '3068', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3216', '798', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3217', '798', '名称', 'title', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3217', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3218', '798', '缩略图', 'image', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3218', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3219', '798', '类型', 'types', '3', '1', '0', '0', '内部页面|0,外部链接|1,外部小程序|2', '1', '1', 'center', '', '', '', '', '3219', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3220', '798', '起步价', 'start', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3220', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3221', '798', '阶梯价格', 'ladder', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3221', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3222', '798', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3222', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3223', '798', '学校', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3223', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3224', '798', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3224', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3604', '829', '编号', 'u_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3225', '799', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3477', '822', '编号', 'u_id', '24', '1', '1', '0', '', '0', '0', 'center', null, '', '', '', '1', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3226', '799', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3226', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3227', '799', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3227', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3228', '800', '提现最低金额', 'withdraw_min', '1', '0', '0', '0', '', '1', '0', 'center', '', '请输入正确的金额', '', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '3016', '', '财务设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3229', '800', '起步费', 'start_fee', '1', '0', '0', '0', '', '1', '0', 'center', '', '请输入正确的金额', '', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '3015', '', '财务设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3230', '800', '阶梯价格', 'step_price', '1', '0', '0', '0', '', '1', '0', 'center', '', '请输入正确的金额', '', '/^(([1-9][0-9]*)|(([0]\\\\.\\\\d{1,2}|[1-9][0-9]*\\\\.\\\\d{1,2})))\$/', '3014', '', '财务设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3231', '800', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3232', '800', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2970', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3233', '800', '小程序appid', 'appid', '1', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2971', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3234', '800', '小程序密钥', 'appsecret', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2972', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3235', '800', '商户号', 'mch_id', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2973', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3236', '800', '商户号密钥', 'mch_key', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '2974', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3237', '800', '骑手订阅消息模板ID', 'template_new', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2975', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3238', '800', '用户抢单提醒', 'template_grab', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2976', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3239', '800', '商家订阅消息', 'template_store', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2978', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3240', '800', '取消订单消息提醒', 'template_cancel', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2977', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3241', '800', '圈子留言订阅消息', 'template_comment', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2979', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3242', '800', '二手市场支付订阅消息', 'template_pay', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2980', '', '订阅消息配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3243', '800', '用户会员包月价格', 'user_month_fee', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2981', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3244', '800', '用户会员包年价格', 'user_year_fee', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2982', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3245', '800', '商家周卡价格', 'store_week_fee', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2983', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3246', '800', '小程序个人中心logo', 'xcx_logo', '8', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2985', '', '图文设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3247', '800', '商家月卡价格', 'store_month_fee', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2984', '', '小程序设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3248', '800', '分后台登录背景图', 'back_logo', '8', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2986', '', '图文设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3249', '800', '会员协议说明', 'vip_content', '16', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2987', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3250', '800', '企业付款到零钱', 'company_pay_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2992', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3251', '800', '跑腿认证隐私条款', 'privacy_content', '16', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2988', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3252', '800', '帮助中心', 'help_content', '16', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2989', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3253', '800', '弹框内容', 'toast_content', '16', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '2990', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3254', '800', '会员充值功能', 'user_vip_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2991', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3255', '800', '骑手是否可跨校接单', 'take_all_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2993', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3256', '800', '二手市场开启审核', 'second_check_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2994', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3257', '800', '论坛文章开启审核', 'article_check_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2995', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3258', '800', '首页论坛是否显示', 'index_quanzi_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2996', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3259', '800', '首页弹窗是否显示', 'index_toast_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2997', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3260', '800', '首页排行榜是否显示', 'index_rank_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2998', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3261', '800', '首页模块是否显示', 'index_module_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '2999', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3262', '800', '首页历史订单是否显示', 'index_history_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '3000', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3263', '800', '论坛匿名是否开启', 'is_anonymous_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '3001', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3264', '800', '个人中心跑腿认证是否显示', 'runner_auth_switch', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '0', 'center', '', '', '', '', '3002', '', '开关设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3265', '800', '退款证书（apiclient_cert.pem）', 'refund_cert', '10', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3003', '', '小程序设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3266', '800', '退款证书（apiclient_key.pem）', 'refund_key', '10', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3004', '', '小程序设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3267', '800', '取快递文本框提示语句', 'qu_tips', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3005', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3268', '800', '寄快递文本框提示语句', 'ji_tips', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3006', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3269', '800', '食堂超市文本框提示语句', 'shi_tips', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3007', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3270', '800', '万能任务文本框提示语句', 'wan_tips', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3008', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3271', '800', '提现提示语句配置', 'withdraw_tips', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3009', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3272', '800', '首页广告位类型', 'home_adv_type', '3', '0', '0', '0', 'banner|1|success,视频|2|primary', '1', '0', 'center', '', '', '', '', '3010', '', '其他设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3273', '800', '首页广告位id', 'home_adv_id', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3011', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3274', '800', '二手市场广告位类型', 'second_adv_type', '3', '0', '0', '0', 'banner|1|success,视频|2|primary', '1', '0', 'center', '', '', '', '', '3012', '', '其他设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3275', '800', '二手市场广告位id', 'second_adv_id', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3013', '', '其他设置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3276', '799', '订单的完成状态', 'status', '2', '1', '1', '0', '未支付|1,待接单|2,待取货|3,到送达|7,已完成|4,已过期|5,未完成|6,已取消|8,等待取消抢单|9', '1', '1', 'center', '', '', '', '', '3278', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3277', '799', '图片', 'img', '8', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3276', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3278', '799', '订单号', 'ordersn', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3277', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3280', '799', '订单类型', 'type', '2', '1', '1', '0', '取件|1,寄件|2,超市食堂|3,无所不能/饮品|4', '1', '1', 'center', '', '', '', '', '3280', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3281', '799', '订单备注', 'remarks', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3281', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3706', '836', '用户id', 'u_id', '24', null, '1', '0', '', '1', '1', null, null, '', '', '', '3706', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3283', '799', '接单员性别限制', 'sex_limit', '2', '0', '1', '0', '无限制|0|primary,男|1|success,女|2|warning', '1', '1', 'center', '', '', '', '', '3283', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3284', '799', '物品重量', 'weight', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3284', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3285', '799', '件数', 'express_num', '20', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3285', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3286', '799', '开始时间', 'start_time', '12', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3286', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3287', '799', '预计上门时间', 'door_time', '12', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3287', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3288', '799', '发起用户openid', 'start_openid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3288', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3289', '799', '抢单用户openid', 'end_openid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3289', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3290', '799', '收货姓名', 'sh_name', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3290', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3291', '799', '收货性别', 'sh_sex', '2', '0', '0', '0', '男|1|success,女|2|warning', '1', '1', 'center', '', '', '', '', '3291', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3705', '836', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3292', '799', '收货联系手机号', 'sh_phone', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3292', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3293', '799', '收货学校', 'sh_school', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3293', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3294', '799', '具体收货地址', 'sh_addres', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3294', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3295', '799', '服务人数', 'service_num', '20', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3295', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3935', '795', '平台扣点', 'commission', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3935', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3936', '793', '审核状态', 'examine', '3', '1', '1', '0', '审核中|0,审核通过|1,审核失败|2', '1', '1', 'center', '', '', '', '', '3936', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3298', '799', '取货具体地址', 'qu_addres', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3298', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3939', '853', '学校id', 's_id', '20', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3939', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3301', '799', '优惠券id', 'co_id', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3301', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3302', '799', '名称', 'co_name', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3302', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3933', '795', '退款理由', 'reason', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3933', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3304', '799', '商家id', 'store_id', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3304', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3305', '799', '优惠券金额', 'y_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3307', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3306', '799', '总价（跑腿费）', 'total', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3305', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3307', '799', '实付金额', 't_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3306', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3308', '799', '跑腿被抽取的手续费', 's_money', '13', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3308', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3309', '799', '学校收取手续费', 'fxs_money', '13', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3309', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3310', '799', '商家抽成费用', 'store_money', '13', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3310', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3311', '799', '商品价格', 'food_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3311', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3312', '799', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3312', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3313', '799', '更新时间', 'updatetime', '12', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3313', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3314', '799', '订单过期时间', 'out_time', '12', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3314', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3315', '799', '取消时间', 'cancel_time', '12', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3315', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3316', '799', '超时', 'out_time_num', '25', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3316', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3317', '799', '商品详情', 'good_details', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3317', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3707', '836', '学校id', 's_id', '1', null, '1', '0', '', '1', '1', null, null, '', '', '', '3707', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3318', '799', '取消抢单来自哪一方:user:下单者,rider:骑手', 'cancel_from', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3318', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3319', '799', '拒绝原因', 'refuse_reason', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3319', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3320', '799', '凭证', 'proof', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3320', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3321', '799', '是否拒绝', 'is_refuse', '20', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3321', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3322', '799', '标题', 'title', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3322', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3323', '799', '开始时间是否显示', 'is_start_show', '20', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3323', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3704', '835', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3324', '801', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3170', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3325', '801', '图标', 'image', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3169', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3326', '801', '名称', 'title', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3168', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3327', '801', '平台id', 'wxapp_id', '6', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3167', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3328', '801', '学校id', 's_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3166', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3329', '801', '编号', 'id', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '1', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3617', '831', '性别', 't_sex', '3', '1', '0', '0', '女|2|success,男|1|danger', '1', '0', 'center', '', '', '', '', '3068', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3616', '829', '手机号', 'phone', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '/^1[3456789]\\\\d{9}\$/', '3473', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3615', '829', '姓名', 't_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3472', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3614', '829', '到期时间', 'deadtime', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3141', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3613', '829', '所属学校名称', 's_name', '1', '0', '0', '0', '', '0', '0', 'center', '', '', '', '', '3065', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3609', '829', '余额', 'balance', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3063', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3340', '803', '编号', 'id', '1', '0', '0', null, null, '1', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3341', '803', '订单号', 'oid', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3184', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3342', '803', '学校id', 's_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3185', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3343', '803', '平台id', 'wxapp_id', '6', '2', '1', '0', '', '1', '0', 'center', '', '', '', '', '3186', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3344', '803', '商品', 'm_id', '2', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3187', 'select id,title from gc_dmh_market_goods', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3345', '803', '用户id', 'uid', '24', '2', '1', '0', '', '1', '0', 'center', '', '', '', '', '3188', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3346', '803', '价格', 'pay', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3189', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3347', '803', '状态', 'state', '23', '1', '0', '0', '已完成|2,已支付|1,未支付|0', '1', '0', 'center', '', '', '', '', '3191', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3348', '803', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3195', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3349', '803', '下单人手机号', 'phone', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3192', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3350', '803', '下单人姓名', 'name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3193', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3351', '803', '其他信息', 'other', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3194', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3352', '804', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3353', '804', '学校id', 's_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3196', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3354', '804', '小程序id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3197', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3355', '804', '商品', 'm_id', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3198', 'select id,title from gc_dmh_market_goods', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3356', '804', '用户昵称', 'u_id', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3199', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3357', '804', '评价内容', 'details', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3200', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3358', '804', '上级', 'pid', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3201', '', '', '0', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3359', '804', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3202', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3360', '805', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3361', '805', '姓名', 'name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3209', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3362', '805', '手机号', 'phone', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3210', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3363', '805', '添加时间', 'create_time', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3211', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3364', '805', '用户', 'uid', '24', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3212', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3365', '805', '状态', 'state', '23', '1', '1', '0', '拒绝|2,通过|1,审核中|0', '1', '0', 'center', '', '', '', '', '3213', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3366', '805', '平台id', 'wxapp_id', '6', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3214', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3367', '805', '学校id', 's_id', '6', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3215', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3368', '806', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3369', '806', '名称', 'tltle', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3217', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3370', '806', '缩略图', 'image', '8', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3218', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3371', '806', '类型', 'types', '3', '1', '1', '0', '内部页面|0,外部链接|1,外部小程序|2', '1', '0', 'center', '', '', '', '', '3219', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3372', '806', '起步价', 'start', '13', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3220', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3373', '806', '阶梯价格', 'ladder', '13', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3221', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3374', '806', '创建时间', 'create_time', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3222', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3375', '806', '学校', 's_id', '15', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3223', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3376', '806', '平台id', 'wxapp_id', '15', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3224', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3377', '807', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3378', '807', '学校id', 's_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3159', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3379', '807', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3160', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3380', '807', '名称', 'title', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3161', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3381', '807', '地址', 'address', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3162', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3382', '807', '添加时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3164', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3383', '793', '用户id', 'u_id', '24', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3383', 'select u_id,nickname from gc_wechat_user', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3384', '809', '编号', 'type_id', '1', '1', '0', '0', '', '0', '1', 'center', '', '', '', '', '1', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3385', '809', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3385', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3386', '809', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3386', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3387', '809', '分类名称', 'type_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3387', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3388', '809', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3388', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3389', '811', '编号', 'business_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3390', '811', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3390', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3391', '811', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3391', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3392', '811', '微信管理员昵称', 'wxadmin_name', '29', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3392', 'select u_id,nickname from gc_wechat_user ', '', '', 'int', '6', '');
INSERT INTO `gc_field` VALUES ('3393', '811', '商家分类', 'type_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', '', '', '3393', 'select type_id,type_name from gc_zh_business_type', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3394', '811', '分类名称', 'type_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3394', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3395', '811', '商家营业开始时间', 'start_time', '1', '0', '0', '0', '', '1', '1', 'center', '请输入营业开始时间。例: 08:30（冒号为英文冒号）', '', '', '', '3395', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3396', '811', '商家打烊时间', 'end_time', '1', '0', '0', '0', '', '1', '1', 'center', '请输入打烊时间。例：20:30（冒号为英文冒号）', '', '', '', '3396', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3397', '811', '商家名称', 'business_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3397', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3398', '811', '商家地址', 'business_address', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3398', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3399', '811', '商家电话', 'phone', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '/^1[3456789]\\\\d{9}\$/', '3399', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3400', '811', '到期时间', 'expire_time', '7', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3400', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3401', '811', '商家图片', 'business_image', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3401', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3402', '811', '营业状态', 'status', '3', '1', '1', '0', '营业|1|success,打烊|2|warning', '1', '1', 'center', '', '', 'notEmpty', '', '3402', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3403', '811', '商家类型', 'type', '3', '1', '1', '0', '校内|1|success,校外|2|warning', '1', '1', 'center', '', '', 'notEmpty', '', '3403', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3404', '811', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3404', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3405', '812', '编号', 'goods_type_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3406', '812', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3406', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3407', '812', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3407', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3408', '812', '所属商家', 'business_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', '', '', '3408', 'select business_id,business_name from gc_zh_business', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3409', '812', '商家名称', 'business_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3409', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3410', '812', '分类名称', 'goods_type_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3410', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3411', '812', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3411', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3412', '813', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3413', '813', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3413', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3414', '813', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3414', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3415', '813', '所属分类', 'goods_type_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3415', 'select goods_type_id,goods_type_name from gc_zh_goods_type', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3416', '813', '分类名称', 'goods_type_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3416', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3417', '813', '所属商家', 'business_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3417', 'select business_id,business_name from gc_zh_business', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3418', '813', '商家名称', 'business_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3418', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3419', '813', '商品名称', 'goods_name', '1', '1', '1', '1', '', '1', '1', 'center', '', '', 'notEmpty', '', '3419', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3420', '813', '商品价格', 'price', '13', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3420', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3421', '813', '商品图片', 'goods_img', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3421', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3422', '813', '状态', 'status', '3', '1', '1', '0', '上架|1|success,下架|0|danger,草稿|2|info', '1', '1', 'center', '', '', '', '', '3422', '', '', '2', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3423', '813', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3423', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3424', '815', '编号', 'info_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3425', '815', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3425', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3426', '815', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3426', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3427', '815', '物品名称', 'title', '1', '1', '1', '1', '', '1', '1', 'center', '', '', 'notEmpty', '', '3427', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3428', '815', '地点', 'address', '1', '1', '1', '1', '', '1', '1', 'center', '', '', '', '', '3428', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3429', '815', '用户id', 'u_id', '1', '2', '0', '0', '', '1', '1', 'center', '', '', '', '', '3429', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3857', '815', '图片', 'image', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3857', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3430', '815', '所属分类', 'type', '2', '1', '1', '0', '失物招领|1|primary,寻物启事|2|success', '1', '1', 'center', '', '', 'notEmpty', '', '3430', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3431', '815', '媒体类型', 'media_type', '2', '1', '1', '0', '视频|1| primary,图片|2|succes', '1', '1', 'center', '', '', '', '', '3431', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3432', '815', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3432', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3433', '816', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3434', '816', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3434', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3435', '816', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3435', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3436', '816', '所属信息', 'info_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', '', '', '3436', 'select info_id,title from gc_zh_info', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3437', '816', '信息名称	', 'title', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3437', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3438', '816', '内容', 'content', '6', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3438', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3439', '816', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3439', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3440', '818', '编号', 'class_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3441', '818', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3441', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3442', '818', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3442', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3443', '818', '分类名称', 'class_name', '1', '1', '0', '1', '', '1', '1', 'center', '', '', 'notEmpty', '', '3444', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3444', '818', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3974', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3445', '819', '编号', 'article_id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3446', '819', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3446', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3447', '819', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3447', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3448', '819', '所属分类', 'class_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', '', '', '3448', 'select class_id,class_name from gc_zh_forum_class', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3449', '819', '分类名称', 'class_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3449', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3450', '819', '用户id', 'u_id', '1', '2', '0', '0', '', '1', '1', 'center', '', '', '', '', '3450', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3451', '819', '内容', 'content', '6', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3451', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3452', '819', '媒体类型', 'media_type', '2', '1', '1', '0', '视频|1|success,图片|2|warning', '1', '1', 'center', '', '', '', '', '3452', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3453', '819', '状态', 'status', '3', '1', '1', '0', '待审核|0|warning,通过|1|success,拒绝|2|warning', '1', '1', 'center', '', '', '', '', '3453', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3454', '819', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3454', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3455', '820', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3456', '820', '学校id', 's_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3456', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3457', '820', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '1', 'center', '', '', '', '', '3457', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3458', '820', '所属文章', 'article_id', '2', '2', '1', '0', '', '1', '1', 'center', '', '', 'notEmpty', '', '3458', 'select article_id,content from gc_zh_articles', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3459', '820', '文章内容', 'content', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3459', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3460', '820', '内容', 'contents', '6', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3460', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3461', '820', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3461', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3466', '821', '图片', 'img', '8', '0', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2896', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3467', '821', '所属学校', 's_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '2895', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3468', '821', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '2894', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3469', '821', '显示位置', 'show_type', '3', '1', '1', '0', '首页|1,二手市场|2,论坛|3,失物招领|4,商家列表|5', '1', '0', 'center', '', '', '', '', '3204', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3470', '821', '跳转类型', 'url_type', '3', '1', '0', '0', '内部页面|1,外部小程序|2,外部链接|3', '1', '0', 'center', '', '', '', '', '3205', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3471', '821', 'url地址', 'url', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3206', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3473', '776', '姓名', 't_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3472', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3474', '776', '手机号', 'phone', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '/^1[3456789]\\\\d{9}\$/', '3473', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3485', '822', '认证学校id', 'auth_sid', '2', '0', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3069', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3486', '822', '所属学校名称', 's_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3065', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3487', '822', '到期时间', 'deadtime', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3141', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3488', '822', '姓名', 't_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3472', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3489', '822', '手机号', 'phone', '1', '1', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '/^1[3456789]\\\\d{9}\$/', '3473', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3490', '823', '附件地址', 'attach_file', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3282', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3491', '823', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3492', '823', '学校id', 's_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3226', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3493', '823', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3227', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3494', '823', '订单的完成状态', 'status', '1', '1', '1', '0', '未支付|1,待接单|2,待取货|3,到送达|7,已完成|4,已过期|5,未完成|6,已取消|8,等待取消抢单|9,', '1', '0', 'center', '', '', '', '', '3276', '', '', '', 'tinyint', '2', '');
INSERT INTO `gc_field` VALUES ('3495', '823', '图片', 'img', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3277', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3496', '823', '订单号', 'ordersn', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3278', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3497', '823', '商品id', 'goods_id', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3279', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3498', '823', '订单类型', 'type', '2', '1', '1', '0', '取件|1,寄件|2,超市食堂|3,无所不能/饮品|4', '1', '0', 'center', '', '', '', '', '3280', '', '', '', 'tinyint', '2', '');
INSERT INTO `gc_field` VALUES ('3499', '823', '订单备注', 'remarks', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3281', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3500', '823', '接单员性别限制', 'sex_limit', '2', '1', '0', '0', '男|1|success,女|2|warning', '1', '0', 'center', '', '', '', '', '3283', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3501', '823', '物品重量', 'weight', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3284', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3502', '823', '件数', 'express_num', '20', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3285', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3503', '823', '开始时间', 'start_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3286', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3504', '823', '预计上门时间', 'door_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3287', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3505', '823', '发起用户openid', 'start_openid', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3288', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3506', '823', '抢单用户openid', 'end_openid', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3289', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3507', '823', '收获姓名', 'sh_name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3290', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3508', '823', '收货性别', 'sh_sex', '2', '1', '0', '0', '男|1|success,女|2|warning', '1', '0', 'center', '', '', '', '', '3291', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3509', '823', '收获联系手机号', 'sh_phone', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3292', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3510', '823', '收获学校', 'sh_school', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3293', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3511', '823', '具体收货地址', 'sh_addres', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3294', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3512', '823', '服务人数', 'service_num', '20', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3295', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3513', '823', '收货经度参数', 'sh_longitude', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3296', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3514', '823', '收货纬度参数', 'sh_latitude', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3297', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3515', '823', '取货具体地址', 'qu_addres', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3298', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3516', '823', '取货经度参数', 'qu_longitude', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3299', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3517', '823', '取货纬度参数', 'qu_latitude', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3300', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3518', '823', '优惠券id', 'co_id', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3301', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3519', '823', '名称', 'co_name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3302', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3520', '823', '商品预估价', 'guess_price', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3303', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3521', '823', '商家id', 'store_id', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3304', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3522', '823', '优惠券金额', 'y_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3305', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3523', '823', '总价（跑腿费）', 'total', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3306', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3524', '823', '实付金额', 't_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3307', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3525', '823', '跑腿被抽取的手续费', 's_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3308', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3526', '823', '学校收取手续费', 'fxs_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3309', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3527', '823', '商家抽成费用', 'store_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3310', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3528', '823', '商品价格', 'food_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3311', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3529', '823', '创建时间', 'createtime', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3312', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3530', '823', '更新时间', 'updatetime', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3313', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3531', '823', '订单过期时间', 'out_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3314', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3532', '823', '取消时间', 'cancel_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3315', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3533', '823', '超时', 'out_time_num', '25', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3316', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3534', '823', '商品详情', 'good_details', '16', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3317', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3535', '823', '取消抢单来自哪一方:user:下单者,rider:骑手', 'cancel_from', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3318', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3536', '823', '拒绝原因', 'refuse_reason', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3319', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3537', '823', '凭证', 'proof', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3320', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3538', '823', '是否拒绝', 'is_refuse', '20', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3321', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3539', '823', '标题', 'title', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3322', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3540', '823', '开始时间是否显示', 'is_start_show', '20', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3323', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3618', '831', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3059', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3611', '829', '骑手审核状态', 'run_status', '3', '0', '0', '0', '未申请审核|0,审核中|1,审核通过|2,审核拒绝|-1', '1', '0', 'center', '', '', '', '', '3067', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3612', '829', '认证学校id', 'auth_sid', '15', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3069', 'select s_id,s_name from gc_school', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3610', '829', '所属学校', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3064', 'select s_id,s_name from gc_school', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3601', '828', '用户id', 'u_id', '24', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3383', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3600', '828', '分类', 'cid', '2', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3181', 'select id,title from gc_dmh_market_category', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3599', '828', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3179', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3598', '828', '详情描述', 'details', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3178', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3597', '828', '原价', 'price', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3177', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3596', '828', '金额', 'pay', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3176', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3592', '828', '学校id', 's_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3172', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3593', '828', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3173', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3594', '828', '图标', 'image', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3174', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3595', '828', '名称', 'title', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3175', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3591', '828', '编号', 'm_id', '1', '1', '1', '0', '', '1', '0', 'center', null, '', '', '', '1', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3619', '831', '编号', 'u_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3620', '831', '昵称', 'nickname', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3061', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3621', '831', '头像', 'avatar', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3062', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3622', '831', 'openid', 'openid', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3060', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3623', '831', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3474', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3624', '831', '余额', 'balance', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3063', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3625', '831', '所属学校', 's_id', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3064', 'select s_id,s_name from gc_school', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3626', '831', '骑手审核状态', 'run_status', '3', '1', '1', '0', '未申请审核|0,审核中|1,审核通过|2,审核拒绝|-1', '1', '0', 'center', '', '', '', '', '3067', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3627', '831', '认证学校id', 'auth_sid', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3069', 'select s_id,s_name from gc_school', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3628', '831', '所属学校名称', 's_name', '1', '0', '0', '0', '', '0', '0', 'center', '', '', '', '', '3065', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3629', '831', '到期时间', 'deadtime', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3141', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3630', '831', '姓名', 't_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3472', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3631', '831', '手机号', 'phone', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '/^1[3456789]\\\\d{9}\$/', '3473', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3638', '832', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3474', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3639', '832', '余额', 'balance', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3063', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3640', '832', '所属学校', 's_id', '2', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3064', 'select s_id,s_name from gc_school', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3641', '832', '骑手审核状态', 'run_status', '3', '1', '1', '0', '未申请审核|0,审核中|1,审核通过|2,审核拒绝|-1', '1', '1', 'center', '', '', '', '', '3067', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3642', '832', '认证学校id', 'auth_sid', '2', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3069', 'select s_id,s_name from gc_school', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3643', '832', '所属学校名称', 's_name', '1', '1', '0', '0', '', '0', '1', 'center', '', '', '', '', '3065', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3644', '832', '到期时间', 'deadtime', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3141', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3645', '832', '姓名', 't_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3472', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3646', '832', '手机号', 'phone', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '/^1[3456789]\\\\d{9}\$/', '3473', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3647', '833', '附件地址', 'attach_file', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3282', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3648', '833', '编号', 'id', '1', '1', '0', null, null, '1', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3649', '833', '学校id', 's_id', '15', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3226', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3650', '833', '平台id', 'wxapp_id', '15', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3227', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3651', '833', '订单的完成状态', 'status', '2', '1', '1', '0', '未支付|1,待接单|2,待取货|3,到送达|7,已完成|4,已过期|5,未完成|6,已取消|8,等待取消抢单|9', '1', '0', 'center', '', '', '', '', '3280', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3652', '833', '图片', 'img', '8', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3277', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3653', '833', '订单号', 'ordersn', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3276', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3654', '833', '商品id', 'goods_id', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3279', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3655', '833', '订单类型', 'type', '2', '1', '1', '0', '取件|1,寄件|2,超市食堂|3,无所不能/饮品|4', '1', '0', 'center', '', '', '', '', '3278', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3656', '833', '订单备注', 'remarks', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3281', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3657', '833', '接单员性别限制', 'sex_limit', '3', '0', '1', '0', '无限制|0|primary,男|1|success,女|2|warning', '1', '0', 'center', '', '', '', '', '3283', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3658', '833', '物品重量', 'weight', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3284', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3659', '833', '件数', 'express_num', '20', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3285', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3660', '833', '开始时间', 'start_time', '12', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3286', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3661', '833', '预计上门时间', 'door_time', '12', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3287', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3662', '833', '发起用户openid', 'start_openid', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3288', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3663', '833', '抢单用户openid', 'end_openid', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3289', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3664', '833', '收货姓名', 'sh_name', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3290', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3665', '833', '收货性别', 'sh_sex', '2', '0', '0', '0', '男|1|success,女|2|warning', '1', '0', 'center', '', '', '', '', '3291', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3666', '833', '收货联系手机号', 'sh_phone', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3292', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3667', '833', '收货学校', 'sh_school', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3293', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3668', '833', '具体收货地址', 'sh_addres', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3294', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3669', '833', '服务人数', 'service_num', '20', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3295', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3932', '795', '支付状态', 'paystate', '20', '1', '0', '0', '未支付|0,已支付|1,已完成|2,退款中|3,退款完成|4', '1', '1', 'center', '', '', '', '', '3932', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3672', '833', '取货具体地址', 'qu_addres', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3298', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3930', '793', '库存', 'stock', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3930', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3929', '793', '新旧程度', 'degree', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3929', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3675', '833', '优惠券id', 'co_id', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3301', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3676', '833', '名称', 'co_name', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3302', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3677', '833', '商品预估价', 'guess_price', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3303', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3678', '833', '商家id', 'store_id', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3304', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3679', '833', '优惠券金额', 'y_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3305', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3680', '833', '总价（跑腿费）', 'total', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3306', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3681', '833', '实付金额', 't_money', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3307', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3682', '833', '跑腿被抽取的手续费', 's_money', '13', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3308', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3683', '833', '学校收取手续费', 'fxs_money', '13', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3309', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3684', '833', '商家抽成费用', 'store_money', '13', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3310', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3685', '833', '商品价格', 'food_money', '13', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3311', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3686', '833', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3312', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3687', '833', '更新时间', 'updatetime', '12', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3313', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3688', '833', '订单过期时间', 'out_time', '12', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3314', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3689', '833', '取消时间', 'cancel_time', '12', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3315', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3690', '833', '超时', 'out_time_num', '25', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3316', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3691', '833', '商品详情', 'good_details', '16', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3317', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3692', '833', '取消抢单来自哪一方:user:下单者,rider:骑手', 'cancel_from', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3318', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3693', '833', '拒绝原因', 'refuse_reason', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3319', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3694', '833', '凭证', 'proof', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3320', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3695', '833', '是否拒绝', 'is_refuse', '20', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3321', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3696', '833', '标题', 'title', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3322', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3697', '833', '开始时间是否显示', 'is_start_show', '20', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3323', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3701', '834', '商品id', 'm_id', '20', null, '1', '0', '', '1', '1', null, null, '', '', '', '3701', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3700', '834', '用户id', 'u_id', '24', null, '1', '0', '', '1', '1', null, null, '', '', '', '3700', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3702', '834', '添加事件', 'create_time', '12', null, '0', '0', '', '1', '1', null, null, '', '', '', '3702', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3703', '795', '支付时间', 'pay_time', '12', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3703', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3708', '836', '添加时间', 'create_time', '12', null, '0', '0', '', '0', '1', null, null, '', '', '', '3708', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3712', '836', '商品id', 'm_id', '1', null, '0', '0', '', '1', '1', null, null, '', '', '', '3712', '', null, '', 'varchar', '250', 'INDEX');
INSERT INTO `gc_field` VALUES ('3709', '836', '平台id', 'wxapp_id', '1', null, '1', '0', '', '1', '1', null, null, '', '', '', '3709', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3710', '834', '学校id', 's_id', '20', null, '1', '0', '', '1', '1', null, null, '', '', '', '3710', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3711', '834', '平台id', 'wxapp_id', '20', null, '1', '0', '', '1', '1', null, null, '', '', '', '3711', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3713', '795', '购买用户id', 'purchase', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3713', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3714', '803', '购买用户id', 'purchase', '1', null, '1', '0', '', '1', '0', null, null, '', '', '', '3714', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3715', '793', '状态', 'state', '2', '1', '1', '0', '上架|0,下架|1', '1', '1', 'center', '', '', '', '', '3715', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3716', '828', '状态', 'state', '2', null, '1', '0', '', '1', '0', null, null, '', '', '', '3716', '', null, '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3717', '809', '分类图片', 'type_image', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3717', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3718', '815', '手机号', 'phone', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3718', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3719', '815', '日期', 'pick_date', '12', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3719', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3720', '815', '认领方式', 'claim_method', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3720', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3721', '815', '备注', 'remarks', '6', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3721', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3722', '838', '编号', 'info_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3723', '838', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3425', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3724', '838', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3426', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3725', '838', '物品名称', 'title', '1', '1', '1', '1', '', '1', '0', 'center', '', '', 'notEmpty', '', '3427', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3726', '838', '地点', 'address', '1', '1', '1', '1', '', '1', '0', 'center', '', '', '', '', '3428', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3727', '838', '用户id', 'u_id', '1', '2', '0', '0', '', '1', '0', 'center', '', '', '', '', '3429', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3728', '838', '所属分类', 'type', '2', '1', '1', '0', '失物招领|1|primary,寻物启事|2|success', '1', '0', 'center', '', '', 'notEmpty', '', '3430', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3729', '838', '媒体类型', 'media_type', '2', '1', '1', '0', '视频|1| primary,图片|2|succes', '1', '0', 'center', '', '', '', '', '3431', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3730', '838', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3432', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3731', '838', '手机号', 'phone', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3718', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3732', '838', '日期', 'pick_date', '12', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3719', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3733', '838', '认领方式', 'claim_method', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3720', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3734', '838', '备注', 'remarks', '6', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3721', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3749', '840', '内容', 'content', '6', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3438', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3748', '840', '信息名称	', 'title', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3437', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3747', '840', '所属信息', 'info_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', '', '', '3436', 'select info_id,title from gc_zh_info', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3746', '840', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3435', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3745', '840', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3434', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3744', '840', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3742', '816', '用户id', 'u_id', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3742', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3855', '820', '用户id', 'u_id', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3855', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3743', '816', '父级id', 'p_id', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3743', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3750', '840', '创建时间', 'createtime', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3439', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3751', '840', '用户id', 'u_id', '1', '0', '0', '0', '', '0', '0', 'center', '', '', '', '', '3742', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3752', '840', '父级id', 'p_id', '1', '0', '0', '0', '', '0', '0', 'center', '', '', '', '', '3743', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3753', '841', '编号', 'class_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3754', '841', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3441', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3755', '841', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3442', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3756', '841', '分类名称', 'class_name', '1', '1', '0', '1', '', '1', '0', 'center', '', '', 'notEmpty', '', '3443', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3757', '841', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3444', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3787', '844', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3461', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3785', '844', '文章内容', 'content', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3459', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3786', '844', '内容', 'contents', '6', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3460', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3784', '844', '所属文章', 'article_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3458', 'select article_id,content from gc_zh_articles', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3783', '844', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3457', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3781', '844', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3782', '844', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3456', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3780', '820', '父级id', 'p_id', '20', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3780', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3768', '819', '图片', 'image', '1', '2', '0', '0', '', '1', '1', 'center', '', '', '', '', '3768', '', '', '', 'varchar', '500', '');
INSERT INTO `gc_field` VALUES ('3769', '843', '编号', 'article_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3770', '843', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3446', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3771', '843', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3447', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3772', '843', '所属分类', 'class_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', '', '', '3448', 'select class_id,class_name from gc_zh_forum_class', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3773', '843', '分类名称', 'class_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3449', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3774', '843', '用户id', 'u_id', '1', '2', '0', '0', '', '0', '0', 'center', '', '', '', '', '3450', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3775', '843', '内容', 'content', '6', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3451', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3776', '843', '媒体类型', 'media_type', '2', '1', '1', '0', '视频|1|success,图片|2|warning', '1', '0', 'center', '', '', '', '', '3452', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3777', '843', '状态', 'status', '3', '1', '1', '0', '通过|1|success,拒绝|2|warning', '1', '0', 'center', '', '', '', '', '3453', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3778', '843', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3454', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3779', '843', '图片', 'image', '1', '2', '0', '0', '', '0', '0', 'center', '', '', '', '', '3768', '', '', '', 'varchar', '500', '');
INSERT INTO `gc_field` VALUES ('3788', '844', '父级id', 'p_id', '20', '0', '0', '0', '', '0', '0', 'center', '', '', '', '', '3780', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3789', '845', '创建时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3474', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3790', '845', 'openid', 'openid', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3060', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3791', '845', '头像', 'avatar', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3062', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3792', '845', '昵称', 'nickname', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3061', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3793', '845', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3059', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3794', '845', '性别', 't_sex', '3', '1', '0', '0', '女|1|success,男|0|danger', '1', '0', 'center', '', '', '', '', '3068', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3795', '845', '编号', 'u_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3796', '845', '手机号', 'phone', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '/^1[3456789]\\\\d{9}\$/', '3473', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3797', '845', '姓名', 't_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3472', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3798', '845', '到期时间', 'deadtime', '1', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3141', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3799', '845', '所属学校名称', 's_name', '1', '0', '0', '0', '', '0', '0', 'center', '', '', '', '', '3065', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3800', '845', '余额', 'balance', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3063', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3801', '845', '骑手审核状态', 'run_status', '3', '0', '0', '0', '未申请审核|0,审核中|1,审核通过|2,审核拒绝|-1', '1', '0', 'center', '', '', '', '', '3067', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3802', '845', '认证学校id', 'auth_sid', '15', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3069', 'select s_id,s_name from gc_school', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3803', '845', '所属学校', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3064', 'select s_id,s_name from gc_school', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3804', '846', '附件地址', 'attach_file', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3282', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3805', '846', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3806', '846', '学校id', 's_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3226', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3807', '846', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3227', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3808', '846', '订单的完成状态', 'status', '1', '1', '1', '0', '未支付|1,待接单|2,待取货|3,到送达|7,已完成|4,已过期|5,未完成|6,已取消|8,等待取消抢单|9,', '1', '1', 'center', '', '', '', '', '3276', '', '', '', 'tinyint', '2', '');
INSERT INTO `gc_field` VALUES ('3809', '846', '图片', 'img', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3277', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3810', '846', '订单号', 'ordersn', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3278', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3811', '846', '商品id', 'goods_id', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3279', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3812', '846', '订单类型', 'type', '2', '1', '1', '0', '取件|1,寄件|2,超市食堂|3,无所不能/饮品|4', '1', '1', 'center', '', '', '', '', '3280', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3813', '846', '订单备注', 'remarks', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3281', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3814', '846', '接单员性别限制', 'sex_limit', '2', '1', '0', '0', '男|1|success,女|2|warning', '1', '1', 'center', '', '', '', '', '3283', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3815', '846', '物品重量', 'weight', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3284', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3816', '846', '件数', 'express_num', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3285', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3817', '846', '开始时间', 'start_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3286', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3818', '846', '预计上门时间', 'door_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3287', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3819', '846', '发起用户openid', 'start_openid', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3288', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3820', '846', '抢单用户openid', 'end_openid', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3289', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3821', '846', '收获姓名', 'sh_name', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3290', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3822', '846', '收货性别', 'sh_sex', '2', '1', '0', '0', '男|1|success,女|2|warning', '1', '1', 'center', '', '', '', '', '3291', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3823', '846', '收获联系手机号', 'sh_phone', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3292', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3824', '846', '收获学校', 'sh_school', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3293', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3825', '846', '具体收货地址', 'sh_addres', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3294', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3826', '846', '服务人数', 'service_num', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3295', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3827', '846', '收货经度参数', 'sh_longitude', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3296', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3828', '846', '收货纬度参数', 'sh_latitude', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3297', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3829', '846', '取货具体地址', 'qu_addres', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3298', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3830', '846', '取货经度参数', 'qu_longitude', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3299', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3831', '846', '取货纬度参数', 'qu_latitude', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3300', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3832', '846', '优惠券id', 'co_id', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3301', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3833', '846', '名称', 'co_name', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3302', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3834', '846', '商品预估价', 'guess_prcie', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3303', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3835', '846', '商家id', 'store_id', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3304', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3836', '846', '优惠券金额', 'y_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3305', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3837', '846', '总价（跑腿费）', 'total', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3306', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3838', '846', '实付金额', 't_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3307', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3839', '846', '跑腿被抽取的手续费', 's_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3308', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3840', '846', '学校收取手续费', 'fxs_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3309', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3841', '846', '商家抽成费用', 'store_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3310', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3842', '846', '商品价格', 'food_money', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3311', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3843', '846', '创建时间', 'createtime', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3312', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3844', '846', '更新时间', 'updatetime', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3313', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3845', '846', '订单过期时间', 'out_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3314', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3846', '846', '取消时间', 'cancel_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3315', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3847', '846', '超时', 'out_time_num', '25', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3316', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3848', '846', '商品详情', 'good_details', '16', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3317', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3849', '846', '取消抢单来自哪一方:user:下单者,rider:骑手', 'cancel_from', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3318', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3850', '846', '拒绝原因', 'refuse_reason', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3319', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3851', '846', '凭证', 'proof', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3320', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3852', '846', '是否拒绝', 'is_refuse', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3321', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3853', '846', '标题', 'title', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3322', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3854', '846', '开始时间是否显示', 'is_start_show', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3323', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3856', '844', '用户id', 'u_id', '1', null, '0', '0', '', '0', '0', null, null, '', '', '', '3856', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3858', '823', '寄件取货联系电话', 'qu_phone', '1', null, '0', '0', '', '0', '1', null, null, '', '', '', '3858', '', null, '', 'varchar', '20', '');
INSERT INTO `gc_field` VALUES ('3859', '823', '寄件取货姓名', 'qu_name', '1', null, '0', '0', '', '0', '1', null, null, '', '', '', '3859', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3860', '823', '寄件 取货  性别 ', 'qu_sex', '20', null, '0', '0', '', '0', '1', null, null, '', '', '', '3860', '', null, '', 'tinyint', '2', '');
INSERT INTO `gc_field` VALUES ('3861', '815', '视频', 'video', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3861', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3862', '843', '视频', 'video', '1', null, '0', '0', '', '0', '0', null, null, '', '', '', '3862', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3863', '843', '是否匿名', 'is_anonymous', '1', null, '0', '0', '', '0', '0', null, null, '', '', '', '3863', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3864', '819', '视频', 'video', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3864', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3865', '819', '是否匿名', 'is_anonymous', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3865', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3866', '823', '用户支付时间', 'pay_time', '20', null, '0', '0', '', '0', '1', null, null, '', '', '', '3866', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3867', '819', '评论数', 'comments_num', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3867', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3868', '819', '点赞数', 'likes_num', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3868', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3869', '819', '收藏数', 'collections_num', '1', '0', '0', '0', '', '0', '1', 'center', '', '', '', '', '3869', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3870', '843', '评论数', 'comments_num', '1', null, '0', '0', '', '0', '0', null, null, '', '', '', '3870', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3871', '843', '点赞数', 'likes_num', '1', null, '0', '0', '', '0', '0', null, null, '', '', '', '3871', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3872', '843', '收藏数', 'collections_num', '1', null, '0', '0', '', '0', '0', null, null, '', '', '', '3872', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3873', '847', '平台id', 'wxapp_id', '15', null, '0', '0', '', '0', '1', null, null, '', '', '', '3873', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3874', '847', '学校id', 's_id', '15', null, '0', '0', '', '0', '1', null, null, '', '', '', '3874', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3875', '847', '用户id', 'u_id', '1', null, '0', '0', '', '0', '1', null, null, '', '', '', '3875', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3876', '847', '文章id', 'articles_id', '1', null, '0', '0', '', '0', '1', null, null, '', '', '', '3876', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3877', '847', '状态', 'status', '1', null, '0', '0', '', '0', '1', null, null, '', '', '', '3877', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3878', '847', '创建时间', 'createtime', '12', null, '0', '0', '', '0', '1', null, null, '', '', '', '3878', '', null, '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3879', '848', '编号', 'business_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3880', '848', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3390', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3881', '848', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3391', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3882', '848', '微信管理员昵称', 'wxadmin_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3392', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3883', '848', '商家分类', 'type_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', '', '', '3393', 'select type_id,type_name from gc_zh_business_type', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3884', '848', '分类名称', 'type_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3394', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3885', '848', '商家营业开始时间', 'start_time', '1', '0', '0', '0', '', '1', '0', 'center', '请输入营业开始时间。例: 08:30（冒号为英文冒号）', '', '', '', '3395', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3886', '848', '商家打烊时间', 'end_time', '1', '0', '0', '0', '', '1', '0', 'center', '请输入打烊时间。例：20:30（冒号为英文冒号）', '', '', '', '3396', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3887', '848', '商家名称', 'business_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3397', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3888', '848', '商家地址', 'business_address', '1', '1', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3398', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3889', '848', '商家电话', 'phone', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3399', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3890', '848', '到期时间', 'expire_time', '7', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3400', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3891', '848', '商家图片', 'business_image', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3401', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3892', '848', '营业状态', 'status', '3', '1', '1', '0', '营业|1|success,打烊|2|warning', '1', '0', 'center', '', '', 'notEmpty', '', '3402', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3893', '848', '商家类型', 'type', '3', '1', '1', '0', '校内|1|success,校外|2|warning', '1', '0', 'center', '', '', 'notEmpty', '', '3403', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3894', '848', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3404', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3895', '849', '编号', 'goods_type_id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3896', '849', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3406', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3897', '849', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3407', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3898', '849', '所属商家', 'business_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', '', '', '3408', 'select business_id,business_name from gc_zh_business', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3899', '849', '商家名称', 'business_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3409', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3900', '849', '分类名称', 'goods_type_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3410', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3901', '849', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3411', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3902', '850', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3903', '850', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3413', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3904', '850', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3414', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3905', '850', '所属分类', 'goods_type_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3415', 'select goods_type_id,goods_type_name from gc_zh_goods_type', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3906', '850', '分类名称', 'goods_type_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3416', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3907', '850', '所属商家', 'business_id', '2', '2', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3417', 'select business_id,business_name from gc_zh_business', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3908', '850', '商家名称', 'business_name', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '3418', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3909', '850', '商品名称', 'goods_name', '1', '1', '1', '1', '', '1', '0', 'center', '', '', 'notEmpty', '', '3419', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3910', '850', '商品价格', 'price', '13', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3420', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3911', '850', '商品图片', 'goods_img', '8', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3421', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3912', '850', '状态', 'status', '3', '1', '1', '0', '上架|1|success,下架|0|danger,草稿|2|info', '1', '0', 'center', '', '', '', '', '3422', '', '', '2', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3913', '850', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3423', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3914', '770', '收货地址是否显示', 'is_address_show', '3', '0', '0', '0', '是|1|success,否|0|danger', '1', '1', 'center', '', '', '', '', '3914', '', '万能任务设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3915', '770', '收货地址是否必填', 'is_address_must', '3', '0', '0', '0', '是|1|success,否|0|danger', '1', '1', 'center', '', '', '', '', '3915', '', '万能任务设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3916', '770', '附件是否显示', 'is_attach_show', '3', '0', '0', '0', '是|1|success,否|0|danger', '1', '1', 'center', '', '', '', '', '3916', '', '万能任务设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3917', '770', '附件是否必填', 'is_attach_must', '3', '0', '0', '0', '是|1|success,否|0|danger', '1', '1', 'center', '', '', '', '', '3917', '', '万能任务设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3918', '770', '服务时间是否显示', 'is_servicetime_show', '3', '0', '0', '0', '是|1|success,否|0|danger', '1', '1', 'center', '', '', '', '', '3918', '', '万能任务设置', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3919', '770', '服务人数是否显示', 'is_servicenum_show', '3', '0', '0', '0', '是|1|success,否|0|danger', '1', '1', 'center', '', '', '', '', '3919', '', '万能任务设置', '1', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3920', '793', '轮播图', 'rotation', '9', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3920', '', '', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3921', '823', '取消原因', 'cancel_reason', '1', null, '0', '0', '', '1', '1', null, null, '', '', '', '3921', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3922', '776', '跑腿总佣金', 'brokerage', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3922', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3923', '851', '编号', 'type_id', '1', '1', '0', '0', '', '0', '0', 'center', '', '', '', '', '1', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3924', '851', '学校id', 's_id', '15', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3385', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3925', '851', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '0', '0', 'center', '', '', '', '', '3386', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3926', '851', '分类名称', 'type_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', 'notEmpty', '', '3387', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3927', '851', '创建时间', 'createtime', '12', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3388', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3928', '851', '分类图片', 'type_image', '8', '0', '0', '0', '', '1', '0', 'center', '', '', '', '', '3717', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3940', '853', '添加时间', 'create_time', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3945', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3941', '853', '状态', 'state', '3', '1', '0', '0', '通过|1,提现中|0,拒绝|2', '1', '1', 'center', '', '', '', '', '3941', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3942', '853', '金额', 'pay', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3942', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3943', '853', '支付宝姓名', 'alipay_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '3943', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3944', '853', '支付宝账户', 'alipay_account', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3944', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3945', '854', '编号', 'id', '1', '1', '0', null, null, '1', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3946', '854', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3938', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3947', '854', '学校id', 's_id', '15', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3939', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3948', '854', '添加时间', 'create_time', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3945', '', '', 'Y-m-d H:i:s', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3949', '854', '状态', 'state', '3', '1', '0', '0', '通过|1,提现中|0,拒绝|2', '1', '0', 'center', '', '', '', '', '3941', '', '', '', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3950', '854', '金额', 'pay', '13', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3942', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3951', '854', '支付宝姓名', 'alipay_name', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3943', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3952', '854', '支付宝账户', 'alipay_account', '1', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3944', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3953', '818', '排序', 'sort', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3443', '', '', '0', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3954', '770', '公众号appid', 'mp_appid', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '1', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3955', '770', '公众号appsecret', 'mp_secret', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '2', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3956', '855', 'id', 'id', '1', null, '0', '0', '', '1', '0', null, null, '', '', '', '3956', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3957', '855', '平台id', 'wxapp_id', '1', null, '1', '0', '', '1', '0', null, null, '', '', '', '3957', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3958', '855', '用户id', 'uid', '24', null, '1', '0', '', '1', '0', null, null, '', '', '', '3958', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3959', '855', '类型', 'type', '1', null, '0', '0', '', '1', '0', null, null, '', '', '', '3959', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3960', '855', '金额', 'price', '1', null, '0', '0', '', '1', '0', null, null, '', '', '', '3960', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3961', '855', '操作', 'operate', '1', null, '1', '0', '', '1', '0', null, null, '', '', '', '3961', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3962', '855', '备注', 'remark', '1', null, '0', '0', '', '1', '0', null, null, '', '', '', '3962', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3963', '855', '添加时间', 'addtime', '1', null, '0', '0', '', '1', '0', null, null, '', '', '', '3963', '', null, '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3964', '857', '编号', 'id', '1', '1', '0', null, null, '0', '1', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3965', '857', '金额', 'price', '13', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '/(^[1-9]([0-9]+)?(\\\\.[0-9]{1,2})?\$)|(^(0){1}\$)|(^[0-9]\\\\.[0-9]([0-9])?\$)/', '3965', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3966', '857', '置顶天数', 'day', '20', '1', '0', '0', '', '1', '1', 'center', '', '', 'notEmpty', '/^[0-9]*\$/', '3966', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3967', '857', '平台id', 'wxapp_id', '15', '0', '1', '0', '', '1', '1', 'center', '', '', '', '', '3967', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3968', '857', '学校id', 's_id', '15', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3968', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3969', '857', '添加时间', 'addtime', '12', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3969', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3970', '757', '赏金抽成', 'reward_rate', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '2907', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3971', '770', '打赏是否开启', 'is_open_reward', '3', '0', '0', '0', '开启|1|success,关闭|0|danger', '1', '1', 'center', '', '', '', '', '3971', '', '开关设置', '1', 'smallint', '6', '');
INSERT INTO `gc_field` VALUES ('3972', '770', '发帖须知', 'posting_instructions', '16', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3972', '', '图文设置', '', 'text', '0', '');
INSERT INTO `gc_field` VALUES ('3973', '818', '图标', 'icon', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3953', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3974', '818', '图片', 'img', '8', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3973', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3975', '858', '编号', 'id', '1', '1', '0', null, null, '0', '0', 'center', null, null, null, null, '1', null, null, null, 'int', '11', null);
INSERT INTO `gc_field` VALUES ('3976', '858', '金额', 'price', '13', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '/(^[1-9]([0-9]+)?(\\\\.[0-9]{1,2})?\$)|(^(0){1}\$)|(^[0-9]\\\\.[0-9]([0-9])?\$)/', '3965', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3977', '858', '置顶天数', 'day', '20', '1', '0', '0', '', '1', '0', 'center', '', '', 'notEmpty', '/^[0-9]*\$/', '3966', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3978', '858', '平台id', 'wxapp_id', '1', '0', '1', '0', '', '1', '0', 'center', '', '', '', '', '3967', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3979', '858', '学校id', 's_id', '1', '1', '1', '0', '', '1', '0', 'center', '', '', '', '', '3968', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3980', '858', '添加时间', 'addtime', '12', '1', '0', '0', '', '1', '0', 'center', '', '', '', '', '3969', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3981', '811', '首页推荐', 'is_recommend', '23', '1', '0', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '3981', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3982', '811', '起步费', 'starting_fee', '13', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3982', '', '', '', 'decimal', '10', '');
INSERT INTO `gc_field` VALUES ('3983', '818', '首页推荐', 'is_recommend', '23', '1', '0', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '3983', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3984', '770', '公众号骑手订阅消息模板ID', 'mp_template_new', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3984', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3985', '770', '公众号用户抢单提醒', 'mp_template_grab', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3985', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3986', '770', '公众号取消订单消息提醒', 'mp_template_cancel', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3986', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3987', '770', '公众号商家订阅消息', 'mp_template_store', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3987', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3988', '770', '公众号二手市场支付订阅消息	', 'mp_template_pay', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3988', '', '公众号配置', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3989', '809', '排序', 'sort', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3989', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3990', '812', '排序', 'sort', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3990', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3991', '813', '排序', 'sort', '20', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3991', '', '', '', 'int', '11', '');
INSERT INTO `gc_field` VALUES ('3992', '811', '经度', 'longitude', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3992', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3993', '811', '纬度', 'latitude', '1', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3993', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3994', '757', '学校名称', 's_name', '1', '1', '1', '0', '', '1', '1', 'center', '', '', '', '', '1', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3996', '818', '跳转链接', 'url', '1', '1', '0', '0', '', '1', '1', 'center', '', '', '', '', '3996', '', '', '', 'varchar', '250', '');
INSERT INTO `gc_field` VALUES ('3997', '818', '是否是分类', 'is_cate', '23', '1', '0', '0', '开启|1,关闭|0', '1', '1', 'center', '', '', '', '', '3997', '', '', '', 'tinyint', '4', '');
INSERT INTO `gc_field` VALUES ('3999', '770', '公众号二维码', 'mp_code', '8', '0', '0', '0', '', '1', '1', 'center', '', '', '', '', '3999', '', '公众号配置', '', 'varchar', '250', '');

-- ----------------------------
-- Table structure for `gc_file`
-- ----------------------------
DROP TABLE IF EXISTS `gc_file`;
CREATE TABLE `gc_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filepath` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `hash` varchar(32) DEFAULT NULL COMMENT '文件hash值',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_file
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_log`
-- ----------------------------
DROP TABLE IF EXISTS `gc_log`;
CREATE TABLE `gc_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_name` varchar(50) DEFAULT NULL COMMENT '应用名称',
  `username` varchar(250) DEFAULT NULL COMMENT '操作用户',
  `url` varchar(250) DEFAULT NULL COMMENT '请求url',
  `ip` varchar(250) DEFAULT NULL COMMENT 'ip',
  `useragent` varchar(250) DEFAULT NULL COMMENT 'useragent',
  `content` text COMMENT '请求内容',
  `errmsg` varchar(250) DEFAULT NULL COMMENT '异常信息',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `type` smallint(6) DEFAULT NULL COMMENT '类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17841 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_log
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_menu`
-- ----------------------------
DROP TABLE IF EXISTS `gc_menu`;
CREATE TABLE `gc_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(9) DEFAULT '0' COMMENT '父级id',
  `controller_name` varchar(32) DEFAULT NULL COMMENT '模块名称',
  `title` varchar(64) DEFAULT NULL COMMENT '模块标题',
  `pk_id` varchar(36) DEFAULT NULL COMMENT '主键名',
  `table_name` varchar(32) DEFAULT NULL COMMENT '模块数据库表',
  `is_create` tinyint(4) DEFAULT NULL COMMENT '是否允许生成模块',
  `status` tinyint(4) DEFAULT NULL COMMENT '0隐藏 10显示',
  `sortid` mediumint(9) DEFAULT '0' COMMENT '排序号',
  `table_status` tinyint(4) DEFAULT NULL COMMENT '是否生成数据库表',
  `is_url` tinyint(4) DEFAULT NULL COMMENT '是否只是url链接',
  `url` varchar(64) DEFAULT NULL,
  `menu_icon` varchar(32) DEFAULT NULL COMMENT 'icon字体图标',
  `tab_menu` varchar(250) DEFAULT NULL COMMENT 'tab选项卡菜单配置',
  `app_id` int(11) DEFAULT NULL COMMENT '所属模块',
  `is_submit` tinyint(4) DEFAULT NULL COMMENT '是否允许投稿',
  `upload_config_id` smallint(6) DEFAULT NULL COMMENT '上传配置id',
  `connect` varchar(20) DEFAULT NULL COMMENT '数据库连接',
  PRIMARY KEY (`menu_id`),
  KEY `controller_name` (`controller_name`) USING BTREE,
  KEY `module_id` (`app_id`)
) ENGINE=MyISAM AUTO_INCREMENT=859 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_menu
-- ----------------------------
INSERT INTO `gc_menu` VALUES ('12', '0', 'System', '系统管理', '', '', '0', '1', '797', '0', '0', '', 'fa fa-gears', '', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('17', '0', '', '后台首页', '', '', '0', '1', '1', '0', '1', 'admin/Index/main', 'fa fa-home', '', '1', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('18', '12', 'User', '管理员管理', 'user_id', 'user', '1', '0', '4', '1', '0', '', 'fa fa-user-secret', '', '1', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('19', '12', 'Role', '角色管理', 'role_id', 'role', '1', '0', '5', '1', '0', '', 'fa fa-user', '', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('21', '12', '', '菜单管理', '', '', '0', '0', '3', '0', '1', 'admin/Sys.Menu/index', '', '', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('41', '12', 'Config', '系统配置', '', '', '1', '1', '673', '0', '0', 'admin/Base/config', 'glyphicon glyphicon-cog', '基本设置|上传配置', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('80', '12', 'Application', '应用管理', '', '', '0', '0', '2', '0', '0', 'admin/Sys.Application/index', '', '', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('670', '12', 'Log', '日志管理', 'id', 'log', '1', '0', '525', '1', null, '', '', '', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('673', '12', 'UploadConfig', '上传配置', 'id', 'upload_config', '1', '0', '722', '0', null, '', '', '', '1', '0', '0', null);
INSERT INTO `gc_menu` VALUES ('722', '0', '', '修改密码', '', '', '0', '0', '692', '0', null, 'admin/Base/password', '', '', '1', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('782', '0', 'School', '学校管理', 's_id', 'school', '1', '1', '757', '0', null, '', 'fa fa-building-o', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('771', '0', '', '提现管理', '', '', '1', '1', '771', '0', null, '', 'fa fa-jpy', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('762', '0', 'Slide', '轮播图管理', 'id', 'slide', '1', '1', '762', '1', null, '', 'fa fa-tv', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('823', '0', 'Order', '订单', 'id', 'dmh_school_order', '1', '1', '799', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('772', '771', 'UserWithdraw', '用户提现', 'id', 'user_withdraw', '1', '1', '772', '1', null, '', 'fa fa-jpy', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('729', '730', 'Tickets', '票务预约', 't_id', 'tickets', '1', '1', '729', '1', null, '', '', '', '1', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('733', '730', 'Dates', '日期管理', 'd_id', 'dates', '1', '1', '733', '1', null, '', '', '', '1', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('759', '0', null, '系统管理', null, null, '0', '1', '759', '0', '0', null, 'fa fa-gears', null, '211', null, null, null);
INSERT INTO `gc_menu` VALUES ('752', '0', 'Account', '账号管理', 'wxapp_id', 'account', '1', '1', '752', '1', null, '', '', '', '1', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('764', '0', 'Coupon', '优惠券管理', 'o_id', 'coupon', '1', '1', '764', '1', null, '', 'fa fa-money', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('760', '759', null, '后台首页', null, null, '0', '1', '1', '0', '1', 'subschool/Index/main', 'fa fa-gears', null, '211', null, null, null);
INSERT INTO `gc_menu` VALUES ('754', '0', null, '系统管理', null, null, '0', '0', '754', '0', '0', null, 'fa fa-gears', null, '210', null, null, null);
INSERT INTO `gc_menu` VALUES ('747', '0', 'Reserve', '预约管理', 'id', 'reserve', '1', '1', '745', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('787', '0', 'UserCoupon', '用户优惠券', 'id', 'user_coupon', '1', null, '787', '1', null, null, null, null, '209', null, null, '');
INSERT INTO `gc_menu` VALUES ('757', '0', 'School', '学校管理', 's_id', 'school', '1', '1', '757', '1', null, '', 'fa fa-building-o', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('755', '754', null, '后台首页', null, null, '0', '1', '1', '0', '1', 'accounts/Index/main', 'fa fa-gears', null, '210', null, null, null);
INSERT INTO `gc_menu` VALUES ('756', '754', null, '修改密码', null, null, '0', '1', '2', '0', '1', 'accounts/Base/password', 'fa fa-gears', null, '210', null, null, null);
INSERT INTO `gc_menu` VALUES ('758', '0', 'SchoolAccount', '账号管理', 'a_id', 'school_account', '1', '1', '758', '1', null, '', 'fa fa-group', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('761', '759', null, '修改密码', null, null, '0', '1', '2', '0', '1', 'subschool/Base/password', 'fa fa-gears', null, '211', null, null, null);
INSERT INTO `gc_menu` VALUES ('769', '0', 'Config', '系统配置1', '', 'system', '1', '0', '525', '0', '0', 'accounts/Base/config', 'glyphicon glyphicon-cog', '基本设置|上传配置', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('770', '774', 'Setting', '小程序设置', 'id', 'setting', '1', '1', '770', '1', null, '', 'fa fa-cog', '小程序设置|财务设置|订阅消息配置|图文设置|开关设置|其他设置|万能任务设置|公众号配置', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('774', '0', '', '系统配置', '', '', '0', '1', '774', '0', null, '', 'fa fa-cog', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('775', '774', 'UploadOss', '远程附件', 'u_id', 'upload_oss', '1', '1', '775', '1', null, '', 'fa fa-file-image-o', '类型选择|阿里云配置|七牛云配置', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('776', '777', 'WechatUser', '用户列表', 'u_id', 'wechat_user', '1', '1', '1', '1', null, '', 'fa fa-user', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('777', '0', '', '用户管理', '', '', '1', '1', '1', '1', null, '', 'fa fa-user', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('832', '777', 'UserAuth', '骑手认证', 'u_id', 'wechat_user', '1', '0', '1', '0', null, '', 'fa fa-user', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('779', '774', 'Sms', '短信配置', 'id', 'sms', '1', '0', '779', '1', null, '', 'fa fa-commenting-o', '基本配置|阿里云配置|创蓝配置', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('822', '0', 'WechatUser', '用户列表', 'u_id', 'wechat_user', '1', '1', '1', '0', null, '', 'fa fa-user', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('785', '0', 'Address', '用户地址', 'a_id', 'address', '1', null, '785', '1', null, null, null, null, '209', null, null, '');
INSERT INTO `gc_menu` VALUES ('788', '0', '', '二手市场', '', '', '0', '1', '789', '0', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('789', '0', 'Dmhexpress', '快递站点管理', 'id', 'dmh_express', '1', '1', '1000', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('790', '0', 'UserWithdraw', '用户提现', 'id', 'user_withdraw', '1', '1', '772', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('792', '788', 'Dmhmarketcategory', '分类', 'id', 'dmh_market_category', '1', '1', '792', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('793', '788', 'Dmhmarketgoods', '商品列表', 'm_id', 'dmh_market_goods', '1', '1', '793', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('796', '788', 'Dmhmarketcategorystay', '商品评论', 'id', 'dmh_market_category_stay', '1', '1', '796', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('795', '788', 'Dmhmarketorder', '订单管理', 'id', 'dmh_market_order', '1', '1', '795', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('829', '830', 'WechatUser', '用户列表', 'u_id', 'wechat_user', '1', '1', '1', '0', null, '', 'fa fa-user', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('798', '0', 'Dmhmodular', '首页模块', 'id', 'dmh_modular', '1', '1', '799', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('799', '0', 'Dmhschoolorder', '分校订单管理', 'id', 'dmh_school_order', '1', '1', '799', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('800', '0', 'Setting', '小程序设置', 'id', 'setting', '1', '1', '770', '0', null, '', 'fa fa-cog', '小程序设置|财务设置|订阅消息配置|图文设置|开关设置|其他设置', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('801', '810', 'Dmhmarketcategory', '分类', 'id', 'dmh_market_category', '1', '1', '792', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('830', '0', '', '用户管理', '', '', '0', '1', '1', '0', null, '', 'fa fa-user', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('803', '810', 'Dmhmarketorder', '订单管理', 'id', 'dmh_market_order', '1', '1', '795', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('804', '810', 'Dmhmarketcategorystay', '商品评论', 'id', 'dmh_market_category_stay', '1', '1', '796', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('805', '0', 'Dmhauthentication', '跑腿认证', 'id', 'dmh_authentication', '1', '1', '798', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('806', '0', 'Dmhmodular', '首页模块', 'id', 'dmh_modular', '1', '1', '798', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('807', '0', 'Dmhexpress', '快递站点管理', 'id', 'dmh_express', '1', '1', '1000', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('808', '0', '', '商家管理', '', '', '1', '1', '808', '0', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('809', '808', 'ZhBusinesType', '商家分类', 'type_id', 'zh_business_type', '1', '1', '809', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('810', '0', '', '二手市场', '', '', '0', null, '810', '0', null, null, null, null, '209', null, null, '');
INSERT INTO `gc_menu` VALUES ('811', '808', 'ZhBusiness', '商家管理', 'business_id', 'zh_business', '1', '1', '811', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('812', '808', 'ZhGoodsType', '商品分类', 'goods_type_id', 'zh_goods_type', '1', '1', '812', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('813', '808', 'ZhGoods', '商品管理', 'id', 'zh_goods', '1', '1', '813', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('814', '0', '', '失物招领', '', '', '1', '1', '814', '0', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('815', '814', 'ZhInfo', '信息列表', 'info_id', 'zh_info', '1', '1', '815', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('816', '814', 'ZhCommentList', '评论列表', 'id', 'zh_comment_list', '1', '1', '816', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('817', '0', '', '圈子管理', '', '', '1', '1', '817', '0', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('818', '817', 'ZhForumClass', '圈子分类', 'class_id', 'zh_forum_class', '1', '1', '818', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('819', '817', 'ZhArticles', '文章管理', 'article_id', 'zh_articles', '1', '1', '819', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('820', '817', 'ZhCommenes', '评论管理', 'id', 'zh_commenes', '1', '1', '820', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('821', '0', 'Slide', '轮播图管理', 'id', 'slide', '1', '1', '762', '0', null, '', 'fa fa-tv', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('828', '810', 'Dmhmarketgoods', '商品列表', 'm_id', 'dmh_market_goods', '1', '1', '793', '0', null, '', '', '', '209', '0', '0', 'mysql');
INSERT INTO `gc_menu` VALUES ('831', '830', 'UserAuth', '跑腿认证', 'u_id', 'wechat_user', '1', '1', '1', '0', null, '', 'fa fa-check-circle-o', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('833', '0', 'Order', '订单管理', 'id', 'dmh_school_order', '1', '1', '770', '0', null, '', '', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('834', '810', 'Dmhgoodsstay', '收藏表', 'id', 'dmh_goods_stay', '1', null, '834', '1', null, null, null, null, '209', null, null, 'mysql');
INSERT INTO `gc_menu` VALUES ('835', '788', 'Dmhfootprint', '我的足迹', 'id', 'dmh_footprint', '1', '0', '835', '1', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('836', '810', 'Dmhfootprint', '我的足迹', 'id', 'dmh_footprint', '1', '1', '835', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('838', '0', 'ZhInfo', '失物招领信息列表', 'info_id', 'zh_info', '1', '1', '815', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('840', '0', 'ZhCommentList', '失物招领评论列表', 'id', 'zh_comment_list', '1', '1', '816', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('841', '0', 'ZhForumClass', '校园圈子分类管理', 'class_id', 'zh_forum_class', '1', '1', '818', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('844', '0', 'ZhCommenes', '校园圈子评论管理', 'id', 'zh_commenes', '1', '1', '820', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('843', '0', 'ZhArticles', '校园圈子文章管理', 'article_id', 'zh_articles', '1', '1', '819', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('845', '0', 'ZhRankingList', '本校骑手佣金排行榜', 'u_id', 'wechat_user', '1', '1', '1', '0', null, '', 'fa fa-user', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('846', '0', 'ZhOrder', '最新或历史订单', 'id', 'dmh_school_order', '1', '1', '799', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('847', '0', 'ZhGiveThumbsUp', '点赞或取消点赞', 'id', 'zh_give_thumbs_up', '1', null, '847', '1', null, null, null, null, '209', null, null, 'mysql');
INSERT INTO `gc_menu` VALUES ('848', '0', 'ZhBusiness', '商家管理', 'business_id', 'zh_business', '1', '1', '811', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('849', '0', 'ZhGoodsType', '商品分类', 'goods_type_id', 'zh_goods_type', '1', '1', '812', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('850', '0', 'ZhGoods', '商品管理', 'id', 'zh_goods', '1', '1', '813', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('851', '0', 'ZhBusinesType', '商家分类', 'type_id', 'zh_business_type', '1', '1', '809', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('852', '759', 'Dmhschool', '学校管理', '', '', '0', '1', '852', '0', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('853', '771', 'Dmhschoolcarry', '学校提现', 'id', 'dmh_school_carry', '1', '1', '853', '1', null, '', '', '', '210', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('854', '0', 'Dmhschoolcarry', '学校提现', 'id', 'dmh_school_carry', '1', '1', '853', '0', null, '', '', '', '211', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('855', '0', 'AccountLog', '余额记录', 'id', 'user_account_log', '1', null, '855', '0', null, null, null, null, '209', null, null, '');
INSERT INTO `gc_menu` VALUES ('858', '0', 'TopSetting', '置顶金额配置', 'id', 'top_setting', '1', '1', '857', '0', null, '', '', '', '209', '0', '0', '');
INSERT INTO `gc_menu` VALUES ('857', '817', 'TopSetting', '置顶金额配置', 'id', 'top_setting', '1', '1', '857', '1', null, '', '', '', '211', '0', '0', '');

-- ----------------------------
-- Table structure for `gc_role`
-- ----------------------------
DROP TABLE IF EXISTS `gc_role`;
CREATE TABLE `gc_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(36) DEFAULT NULL COMMENT '分组名称',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态 10正常 0禁用',
  `pid` smallint(6) DEFAULT NULL COMMENT '所属父类',
  `description` text COMMENT '描述',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_role
-- ----------------------------
INSERT INTO `gc_role` VALUES ('1', '超级管理员', '1', '0', '超级管理员');
INSERT INTO `gc_role` VALUES ('22', '管理员', '1', '0', '');

-- ----------------------------
-- Table structure for `gc_school`
-- ----------------------------
DROP TABLE IF EXISTS `gc_school`;
CREATE TABLE `gc_school` (
  `s_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_name` varchar(250) DEFAULT NULL COMMENT '学校名称',
  `wxapp_id` varchar(250) DEFAULT NULL,
  `plat_rate` int(11) DEFAULT NULL COMMENT '平台抽成',
  `school_rate` int(11) DEFAULT NULL COMMENT '学校抽成',
  `second_rate` int(11) DEFAULT NULL COMMENT '二手市场抽成百分比',
  `edit_status` smallint(6) DEFAULT NULL COMMENT '是否允许分校修改跑腿抽佣',
  `robot_key` varchar(250) DEFAULT NULL COMMENT '机器人key',
  `step` text COMMENT '阶梯选择配置',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `coupon_list` varchar(250) DEFAULT NULL COMMENT '优惠券列表',
  `alipay_name` varchar(255) DEFAULT NULL COMMENT '支付宝姓名',
  `alipay_account` varchar(255) DEFAULT NULL COMMENT '支付宝账户',
  `reward` text COMMENT '打赏金额',
  `reward_rate` varchar(250) DEFAULT NULL COMMENT '赏金抽成',
  PRIMARY KEY (`s_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_school
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_school_account`
-- ----------------------------
DROP TABLE IF EXISTS `gc_school_account`;
CREATE TABLE `gc_school_account` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(250) DEFAULT NULL COMMENT '账号',
  `s_id` smallint(6) DEFAULT NULL COMMENT '管理学校',
  `pwd` varchar(250) DEFAULT NULL COMMENT '密码',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  PRIMARY KEY (`a_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_school_account
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_setting`
-- ----------------------------
DROP TABLE IF EXISTS `gc_setting`;
CREATE TABLE `gc_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `appid` varchar(250) DEFAULT NULL COMMENT '小程序appid',
  `appsecret` varchar(250) DEFAULT NULL COMMENT '小程序密钥',
  `mch_id` varchar(250) DEFAULT NULL COMMENT '商户号',
  `mch_key` varchar(250) DEFAULT NULL COMMENT '商户号密钥',
  `template_new` varchar(250) DEFAULT NULL COMMENT '骑手订阅消息模板ID',
  `template_grab` varchar(250) DEFAULT NULL COMMENT '用户抢单提醒',
  `template_cancel` varchar(250) DEFAULT NULL COMMENT '取消订单消息提醒',
  `template_store` varchar(250) DEFAULT NULL COMMENT '商家订阅消息',
  `template_comment` varchar(250) DEFAULT NULL COMMENT '圈子留言订阅消息',
  `template_pay` varchar(250) DEFAULT NULL COMMENT '二手市场支付订阅消息',
  `user_month_fee` varchar(250) DEFAULT NULL COMMENT '用户会员包月价格',
  `user_year_fee` varchar(250) DEFAULT NULL COMMENT '用户会员包年价格',
  `store_week_fee` varchar(250) DEFAULT NULL COMMENT '商家周卡价格',
  `store_month_fee` varchar(250) DEFAULT NULL COMMENT '商家月卡价格',
  `xcx_logo` varchar(250) DEFAULT NULL COMMENT '小程序个人中心logo',
  `back_logo` varchar(250) DEFAULT NULL COMMENT '分后台登录背景图',
  `vip_content` text COMMENT '会员协议说明',
  `privacy_content` text COMMENT '跑腿认证隐私条款',
  `help_content` text COMMENT '帮助中心',
  `toast_content` text COMMENT '弹框内容',
  `user_vip_switch` smallint(6) DEFAULT NULL COMMENT '会员充值功能',
  `company_pay_switch` smallint(6) DEFAULT NULL COMMENT '企业付款到零钱',
  `take_all_switch` smallint(6) DEFAULT NULL COMMENT '骑手是否可跨校接单',
  `second_check_switch` smallint(6) DEFAULT NULL COMMENT '二手市场开启审核',
  `article_check_switch` smallint(6) DEFAULT NULL COMMENT '论坛文章开启审核',
  `index_quanzi_switch` smallint(6) DEFAULT NULL COMMENT '首页论坛是否显示',
  `index_toast_switch` smallint(6) DEFAULT NULL COMMENT '首页弹窗是否显示',
  `index_rank_switch` smallint(6) DEFAULT NULL COMMENT '首页排行榜是否显示',
  `index_module_switch` smallint(6) DEFAULT NULL COMMENT '首页模块是否显示',
  `index_history_switch` smallint(6) DEFAULT NULL COMMENT '首页历史订单是否显示',
  `is_anonymous_switch` smallint(6) DEFAULT NULL COMMENT '论坛匿名是否开启',
  `runner_auth_switch` smallint(6) DEFAULT NULL COMMENT '个人中心跑腿认证是否显示',
  `refund_cert` text COMMENT '退款证书（apiclient_cert.pem）',
  `refund_key` text COMMENT '退款证书（apiclient_key.pem）',
  `qu_tips` varchar(250) DEFAULT NULL COMMENT '取快递文本框提示语句',
  `ji_tips` varchar(250) DEFAULT NULL COMMENT '寄快递文本框提示语句',
  `shi_tips` varchar(250) DEFAULT NULL COMMENT '食堂超市文本框提示语句',
  `wan_tips` varchar(250) DEFAULT NULL COMMENT '万能任务文本框提示语句',
  `withdraw_tips` varchar(250) DEFAULT NULL COMMENT '提现提示语句配置',
  `home_adv_type` smallint(6) DEFAULT NULL COMMENT '首页广告位类型',
  `home_adv_id` varchar(250) DEFAULT NULL COMMENT '首页广告位id',
  `second_adv_type` smallint(6) DEFAULT NULL COMMENT '二手市场广告位类型',
  `second_adv_id` varchar(250) DEFAULT NULL COMMENT '二手市场广告位id',
  `step_price` varchar(250) DEFAULT NULL COMMENT '阶梯价格',
  `start_fee` varchar(250) DEFAULT NULL COMMENT '起步费',
  `withdraw_min` varchar(250) DEFAULT NULL COMMENT '提现最低金额',
  `is_address_show` smallint(6) DEFAULT NULL COMMENT '收货地址是否显示',
  `is_address_must` smallint(6) DEFAULT NULL COMMENT '收货地址是否必填',
  `is_attach_show` smallint(6) DEFAULT NULL COMMENT '附件是否显示',
  `is_attach_must` smallint(6) DEFAULT NULL COMMENT '附件是否必填',
  `is_servicetime_show` smallint(6) DEFAULT NULL COMMENT '服务时间是否显示',
  `is_servicenum_show` smallint(6) DEFAULT '1' COMMENT '服务人数是否显示',
  `mp_appid` varchar(250) DEFAULT NULL COMMENT '公众号appid',
  `mp_secret` varchar(250) DEFAULT NULL COMMENT '公众号appsecret',
  `is_open_reward` smallint(6) DEFAULT '1' COMMENT '打赏是否开启',
  `posting_instructions` tinytext COMMENT '发帖须知',
  `mp_template_new` varchar(250) DEFAULT NULL COMMENT '公众号骑手订阅消息模板ID',
  `mp_template_grab` varchar(250) DEFAULT NULL COMMENT '公众号用户抢单提醒',
  `mp_template_cancel` varchar(250) DEFAULT NULL COMMENT '公众号取消订单消息提醒',
  `mp_template_store` varchar(250) DEFAULT NULL COMMENT '公众号商家订阅消息',
  `mp_template_pay` varchar(250) DEFAULT NULL COMMENT '公众号二手市场支付订阅消息	',
  `mp_code` varchar(250) DEFAULT NULL COMMENT '公众号二维码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_setting
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_slide`
-- ----------------------------
DROP TABLE IF EXISTS `gc_slide`;
CREATE TABLE `gc_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL COMMENT '名称',
  `s_id` varchar(250) DEFAULT NULL COMMENT '所属学校',
  `img` varchar(250) DEFAULT NULL COMMENT '图片',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `show_type` smallint(6) DEFAULT NULL COMMENT '显示位置',
  `url_type` smallint(6) DEFAULT NULL COMMENT '跳转类型',
  `url` varchar(250) DEFAULT NULL COMMENT 'url地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_slide
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_sms`
-- ----------------------------
DROP TABLE IF EXISTS `gc_sms`;
CREATE TABLE `gc_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `sms_type` varchar(200) DEFAULT NULL COMMENT '平台类型',
  `ali_sms_accesskeyid` varchar(250) DEFAULT NULL COMMENT '阿里云AccessKeyId',
  `ali_sms_accesskeysecret` varchar(250) DEFAULT NULL COMMENT '阿里云AccessKeySecret',
  `ali_sms_signname` varchar(250) DEFAULT NULL COMMENT '阿里云签名名称',
  `ali_sms_tempcode` varchar(250) DEFAULT NULL COMMENT '阿里云模板CODE',
  `cl_account` varchar(250) DEFAULT NULL COMMENT '创蓝账号',
  `cl_pwd` varchar(250) DEFAULT NULL COMMENT '创蓝密码',
  `cl_sign` varchar(250) DEFAULT NULL COMMENT '创蓝签名名称',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `sms_status` tinyint(4) DEFAULT NULL COMMENT '是否使用短信通知',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_sms
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_system`
-- ----------------------------
DROP TABLE IF EXISTS `gc_system`;
CREATE TABLE `gc_system` (
  `name` varchar(50) NOT NULL,
  `data` varchar(255) NOT NULL,
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_system
-- ----------------------------
INSERT INTO `gc_system` VALUES ('water_status', '0');
INSERT INTO `gc_system` VALUES ('site_title', '格创跑腿SAAS管理系统');
INSERT INTO `gc_system` VALUES ('keyword', '小程序开发,APP开发');
INSERT INTO `gc_system` VALUES ('description', '格创跑腿SAAS管理系统');
INSERT INTO `gc_system` VALUES ('site_logo', '/uploads/admin/202101/5ff40b8d7fbd2.jpg');
INSERT INTO `gc_system` VALUES ('file_size', '2');
INSERT INTO `gc_system` VALUES ('copyright', '格创网络');
INSERT INTO `gc_system` VALUES ('file_type', 'gif,png,jpg,jpeg,doc,docx,xls,xlsx,csv,pdf,rar,zip,txt,mp4,flv');
INSERT INTO `gc_system` VALUES ('domain', '');
INSERT INTO `gc_system` VALUES ('water_position', '5');
INSERT INTO `gc_system` VALUES ('water_logo', '');
INSERT INTO `gc_system` VALUES ('appid', 'wxca74ebbff7ea581c');
INSERT INTO `gc_system` VALUES ('appsecret', '7f23b1a52a50946b09c41b47401711e2');
INSERT INTO `gc_system` VALUES ('school_site_title', '分校管理');
INSERT INTO `gc_system` VALUES ('sub_site_title', '小程序管理12');
INSERT INTO `gc_system` VALUES ('agreement', '&lt;p&gt;111&lt;/p&gt;');
INSERT INTO `gc_system` VALUES ('explain', '&lt;p&gt;222&lt;/p&gt;');

-- ----------------------------
-- Table structure for `gc_tickets`
-- ----------------------------
DROP TABLE IF EXISTS `gc_tickets`;
CREATE TABLE `gc_tickets` (
  `t_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `title` varchar(250) DEFAULT NULL COMMENT '名称',
  `img` varchar(250) DEFAULT NULL COMMENT '图片',
  `notice` text COMMENT '购票须知',
  PRIMARY KEY (`t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_tickets
-- ----------------------------
INSERT INTO `gc_tickets` VALUES ('3', '12', '21', '');

-- ----------------------------
-- Table structure for `gc_top_setting`
-- ----------------------------
DROP TABLE IF EXISTS `gc_top_setting`;
CREATE TABLE `gc_top_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(10,2) DEFAULT NULL COMMENT '金额',
  `day` int(11) DEFAULT NULL COMMENT '置顶天数',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `addtime` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `gc_upload_config`
-- ----------------------------
DROP TABLE IF EXISTS `gc_upload_config`;
CREATE TABLE `gc_upload_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL COMMENT '配置名称',
  `upload_replace` tinyint(4) DEFAULT NULL COMMENT '覆盖同名文件',
  `thumb_status` tinyint(4) DEFAULT NULL COMMENT '缩图开关',
  `thumb_width` varchar(250) DEFAULT NULL COMMENT '缩放宽度',
  `thumb_height` varchar(250) DEFAULT NULL COMMENT '缩放高度',
  `thumb_type` smallint(6) DEFAULT NULL COMMENT '缩图方式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_upload_config
-- ----------------------------
INSERT INTO `gc_upload_config` VALUES ('1', '默认配置', '0', '1', '600', '400', '3');

-- ----------------------------
-- Table structure for `gc_upload_oss`
-- ----------------------------
DROP TABLE IF EXISTS `gc_upload_oss`;
CREATE TABLE `gc_upload_oss` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `oss_status` tinyint(4) DEFAULT NULL COMMENT '是否开启远程附件',
  `oss_default_type` varchar(200) DEFAULT NULL COMMENT '类型',
  `ali_oss_accesskeyid` varchar(250) DEFAULT NULL COMMENT 'Access Key ID',
  `ali_oss_accesskeysecret` varchar(250) DEFAULT NULL COMMENT 'Access Key Secret',
  `ali_oss_endpoint` varchar(250) DEFAULT NULL COMMENT 'url',
  `ali_oss_bucket` varchar(250) DEFAULT NULL COMMENT 'bucket',
  `qny_oss_accesskey` varchar(250) DEFAULT NULL COMMENT 'Accesskey',
  `qny_oss_secretkey` varchar(250) DEFAULT NULL COMMENT 'Secretkey',
  `qny_oss_bucket` varchar(250) DEFAULT NULL COMMENT 'bucket',
  `qny_oss_domain` varchar(250) DEFAULT NULL COMMENT 'url',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_upload_oss
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_user`
-- ----------------------------
DROP TABLE IF EXISTS `gc_user`;
CREATE TABLE `gc_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(250) DEFAULT NULL COMMENT '真实姓名',
  `user` varchar(250) DEFAULT NULL COMMENT '用户名',
  `pwd` varchar(250) DEFAULT NULL COMMENT '密码',
  `role_id` varchar(250) DEFAULT NULL COMMENT '所属分组',
  `note` varchar(250) DEFAULT NULL COMMENT '备注',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `avatar` varchar(250) DEFAULT NULL COMMENT '头像',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_user
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_user_account_log`
-- ----------------------------
DROP TABLE IF EXISTS `gc_user_account_log`;
CREATE TABLE `gc_user_account_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1为跑腿，2为二手市场',
  `price` decimal(10,2) NOT NULL,
  `operate` int(11) DEFAULT '0' COMMENT '0为增加，-1为减少',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 COMMENT='用户余额变动记录表';

-- ----------------------------
-- Records of gc_user_account_log
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_user_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `gc_user_coupon`;
CREATE TABLE `gc_user_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `o_id` varchar(250) DEFAULT NULL COMMENT '优惠券id',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `use_status` smallint(6) DEFAULT NULL COMMENT '使用状态（0为未使用，1为已使用）',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` varchar(250) DEFAULT NULL COMMENT '更新时间',
  `c_name` varchar(250) DEFAULT NULL COMMENT '优惠券名称',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_user_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_user_follow`
-- ----------------------------
DROP TABLE IF EXISTS `gc_user_follow`;
CREATE TABLE `gc_user_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` int(11) NOT NULL,
  `f_uid` int(11) NOT NULL COMMENT '被关注的用户id',
  `uid` int(11) NOT NULL COMMENT '操作关注的用户id',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0为未关注，1为已关注',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户关注列表';


-- ----------------------------
-- Table structure for `gc_user_vip_record`
-- ----------------------------
DROP TABLE IF EXISTS `gc_user_vip_record`;
CREATE TABLE `gc_user_vip_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` int(11) NOT NULL,
  `u_id` int(11) DEFAULT NULL,
  `order_id` varchar(100) NOT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `type` int(11) NOT NULL COMMENT '1：包月，2：包年',
  `addtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pay_time` varchar(255) DEFAULT NULL,
  `is_store` int(11) DEFAULT '0' COMMENT '1为商家会员，0为普通会员',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0：未支付，1：已支付',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户购买会员记录';

-- ----------------------------
-- Records of gc_user_vip_record
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_user_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `gc_user_withdraw`;
CREATE TABLE `gc_user_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `account` varchar(250) DEFAULT NULL COMMENT '提现账号',
  `price` varchar(250) DEFAULT NULL COMMENT '提现金额',
  `type` smallint(6) DEFAULT NULL COMMENT '提现类型',
  `status` smallint(6) DEFAULT NULL COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `name` varchar(250) DEFAULT NULL COMMENT '提现姓名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_user_withdraw
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_wechat_user`
-- ----------------------------
DROP TABLE IF EXISTS `gc_wechat_user`;
CREATE TABLE `gc_wechat_user` (
  `u_id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT '平台id',
  `nickname` varchar(250) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT '头像',
  `openid` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT 'openid',
  `mp_openid` varchar(255) DEFAULT NULL COMMENT '公众号openid',
  `unionid` varchar(255) DEFAULT NULL COMMENT '开放平台id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `balance` decimal(10,2) DEFAULT NULL COMMENT '余额',
  `s_id` smallint(6) DEFAULT NULL COMMENT '所属学校',
  `run_status` smallint(6) DEFAULT NULL COMMENT '骑手审核状态',
  `auth_sid` smallint(6) DEFAULT NULL COMMENT '认证学校id',
  `deadtime` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT '到期时间',
  `t_sex` smallint(6) DEFAULT NULL COMMENT '性别',
  `t_name` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT '姓名',
  `phone` varchar(250) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机号',
  `brokerage` decimal(10,2) DEFAULT '0.00' COMMENT '跑腿总佣金',
  `has_store` int(11) NOT NULL DEFAULT '0' COMMENT '是否商家 0否 1是',
  `store_id` int(11) DEFAULT NULL COMMENT '商家id',
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of gc_wechat_user
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_week`
-- ----------------------------
DROP TABLE IF EXISTS `gc_week`;
CREATE TABLE `gc_week` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;



-- ----------------------------
-- Table structure for `gc_zh_articles`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_articles`;
CREATE TABLE `gc_zh_articles` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `class_id` smallint(6) DEFAULT NULL COMMENT '所属分类',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `is_top` int(11) DEFAULT '0' COMMENT '是否置顶',
  `is_expired` int(11) DEFAULT '0' COMMENT '0为过期或未置顶，1为置顶中',
  `content` text COMMENT '内容',
  `media_type` smallint(6) DEFAULT NULL COMMENT '媒体类型',
  `status` smallint(6) DEFAULT NULL COMMENT '状态',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `image` varchar(500) DEFAULT NULL COMMENT '图片',
  `video` varchar(250) DEFAULT NULL COMMENT '视频',
  `is_anonymous` varchar(250) DEFAULT NULL COMMENT '是否匿名',
  `comments_num` int(11) DEFAULT '0' COMMENT '评论数',
  `likes_num` int(11) DEFAULT '0' COMMENT '点赞数',
  `collections_num` int(11) DEFAULT '0' COMMENT '收藏数',
  `views_num` int(11) DEFAULT '0' COMMENT '点击量',
  `paytime` varchar(255) DEFAULT NULL COMMENT '置顶付款时间',
  `deadtime` int(11) DEFAULT NULL COMMENT '置顶到期时间',
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_articles
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_business`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_business`;
CREATE TABLE `gc_zh_business` (
  `business_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `wxadmin_name` int(11) DEFAULT NULL COMMENT '微信管理员昵称',
  `type_id` smallint(6) DEFAULT NULL COMMENT '商家分类',
  `start_time` varchar(250) DEFAULT NULL COMMENT '商家营业开始时间',
  `end_time` varchar(250) DEFAULT NULL COMMENT '商家打烊时间',
  `business_name` varchar(250) DEFAULT NULL COMMENT '商家名称',
  `business_address` varchar(250) DEFAULT NULL COMMENT '商家地址',
  `phone` varchar(250) DEFAULT NULL COMMENT '商家电话',
  `expire_time` int(11) DEFAULT NULL COMMENT '到期时间',
  `business_image` varchar(250) DEFAULT NULL COMMENT '商家图片',
  `status` smallint(6) DEFAULT NULL COMMENT '营业状态',
  `type` smallint(6) DEFAULT NULL COMMENT '商家类型',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `is_recommend` tinyint(4) DEFAULT NULL COMMENT '首页推荐',
  `starting_fee` decimal(10,2) DEFAULT NULL COMMENT '起步费',
  `longitude` varchar(250) DEFAULT NULL COMMENT '经度',
  `latitude` varchar(250) DEFAULT NULL COMMENT '纬度',
  PRIMARY KEY (`business_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_business
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_business_type`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_business_type`;
CREATE TABLE `gc_zh_business_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `type_name` varchar(250) DEFAULT NULL COMMENT '分类名称',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `type_image` varchar(250) DEFAULT NULL COMMENT '分类图片',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_business_type
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_commenes`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_commenes`;
CREATE TABLE `gc_zh_commenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `article_id` smallint(6) DEFAULT NULL COMMENT '所属文章',
  `contents` tinytext COMMENT '内容',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `p_id` int(11) DEFAULT NULL COMMENT '父级id',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_commenes
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_comment_list`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_comment_list`;
CREATE TABLE `gc_zh_comment_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `info_id` smallint(6) DEFAULT NULL COMMENT '所属信息',
  `content` tinytext COMMENT '内容',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `p_id` varchar(250) DEFAULT NULL COMMENT '父级id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_comment_list
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_forum_class`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_forum_class`;
CREATE TABLE `gc_zh_forum_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `class_name` varchar(250) DEFAULT NULL COMMENT '分类名称',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `sort` int DEFAULT '0' COMMENT '排序',
  `icon` varchar(250) DEFAULT NULL COMMENT '图标',
  `img` varchar(250) DEFAULT NULL COMMENT '图片',
  `is_recommend` tinyint(4) DEFAULT NULL COMMENT '首页推荐',
  `url` varchar(250) DEFAULT NULL COMMENT '跳转链接',
  `is_cate` tinyint(4) DEFAULT NULL COMMENT '是否是分类',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_forum_class
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_forum_comment_fav`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_forum_comment_fav`;
CREATE TABLE `gc_zh_forum_comment_fav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` int(11) NOT NULL,
  `s_id` int(11) DEFAULT NULL,
  `c_id` int(11) NOT NULL COMMENT '评论id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '1为点赞，0为取消',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uptime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='文章评论点赞表';

-- ----------------------------
-- Records of gc_zh_forum_comment_fav
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_give_thumbs_up`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_give_thumbs_up`;
CREATE TABLE `gc_zh_give_thumbs_up` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxapp_id` varchar(250) DEFAULT NULL COMMENT '平台id',
  `s_id` varchar(250) DEFAULT NULL COMMENT '学校id',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `articles_id` varchar(250) DEFAULT NULL COMMENT '文章id',
  `status` varchar(250) DEFAULT NULL COMMENT '状态',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_give_thumbs_up
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_goods`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_goods`;
CREATE TABLE `gc_zh_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `goods_type_id` smallint(6) DEFAULT NULL COMMENT '所属分类',
  `business_id` int(11) DEFAULT NULL COMMENT '所属商家',
  `goods_name` varchar(250) DEFAULT NULL COMMENT '商品名称',
  `price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `goods_img` varchar(250) DEFAULT NULL COMMENT '商品图片',
  `status` int(11) DEFAULT '2' COMMENT '状态',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_goods_type`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_goods_type`;
CREATE TABLE `gc_zh_goods_type` (
  `goods_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `business_id` smallint(6) DEFAULT NULL COMMENT '所属商家',
  `goods_type_name` varchar(250) DEFAULT NULL COMMENT '分类名称',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`goods_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_goods_type
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_info`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_info`;
CREATE TABLE `gc_zh_info` (
  `info_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` int(11) DEFAULT NULL COMMENT '学校id',
  `wxapp_id` int(11) DEFAULT NULL COMMENT '平台id',
  `title` varchar(250) DEFAULT NULL COMMENT '物品名称',
  `address` varchar(250) DEFAULT NULL COMMENT '地点',
  `u_id` varchar(250) DEFAULT NULL COMMENT '用户id',
  `type` smallint(6) DEFAULT NULL COMMENT '所属分类',
  `media_type` smallint(6) DEFAULT NULL COMMENT '媒体类型',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `phone` varchar(250) DEFAULT NULL COMMENT '手机号',
  `pick_date` varchar(255) DEFAULT NULL COMMENT '日期',
  `claim_method` varchar(250) DEFAULT NULL COMMENT '认领方式',
  `remarks` tinytext COMMENT '备注',
  `image` varchar(250) DEFAULT NULL COMMENT '图片',
  `video` varchar(250) DEFAULT NULL COMMENT '视频',
  PRIMARY KEY (`info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of gc_zh_info
-- ----------------------------

-- ----------------------------
-- Table structure for `gc_zh_vip_order`
-- ----------------------------
DROP TABLE IF EXISTS `gc_zh_vip_order`;
CREATE TABLE `gc_zh_vip_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) NOT NULL COMMENT '订单号',
  `money` decimal(10,0) NOT NULL COMMENT '订单金额',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '支付状态 0 待支付 1已支付',
  `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `d_order_sn` varchar(255) DEFAULT NULL COMMENT '第三方订单号',
  `vip_type` int(11) DEFAULT NULL COMMENT '充值会员类型 1 周卡 2月卡',
  `wxapp_id` int(11) NOT NULL COMMENT '平台id',
  `s_id` int(11) NOT NULL COMMENT '学校id',
  `u_id` int(11) NOT NULL COMMENT '用户id',
  `business_id` int(11) NOT NULL COMMENT '商家id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of gc_zh_vip_order
-- ----------------------------
";