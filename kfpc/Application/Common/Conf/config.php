<?php
return array(
	'MODULE_ALLOW_LIST'    =>    array('Home','Mobile','Ads'),
    'DEFAULT_MODULE'       =>    'Home',
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'ads_', // 数据库表前缀 
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	
	//系统数据库配置信息
	'DB_HOST'   => '', // 服务器地址
	'DB_NAME'   => '', // 数据库名
	'DB_USER'   => '', // 用户名
	'DB_PWD'    => '', // 密码
	
	//java报表数据库配置信息
	'DB_HOST_JAVA'   => '', 
	'DB_NAME_JAVA'   => '',
	'DB_USER_JAVA'   => '',
	'DB_PWD_JAVA'   => '', 
	
	'DOMAIN_TASK'=>'', //系统php执行的域名访问路径
	'DOMAIN_PC'=>'', //系统后台管理域名访问路径
	'ROOT_MCC'=>'', //adwords 顶级MCC账户ID
);