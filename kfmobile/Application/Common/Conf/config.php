<?php
$configs = parse_ini_file('../../../config.ini',true);
return array(
	'MODULE_ALLOW_LIST'    =>    array('Mobile','Ads'),
    'DEFAULT_MODULE'       =>    'Mobile',
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'ads_', // 数据库表前缀 
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	
	//系统数据库配置信息
	'DB_HOST'   => $configs['DATABASE_KFPC']['host'], // 服务器地址
	'DB_NAME'   => $configs['DATABASE_KFPC']['user'], // 数据库名
	'DB_USER'   => $configs['DATABASE_KFPC']['pwd'], // 用户名
	'DB_PWD'    => $configs['DATABASE_KFPC']['db'], // 密码
	
	//java报表数据库配置信息
	'DB_HOST_JAVA'   => $configs['DATABASE_KFTASK']['host'], 
	'DB_NAME_JAVA'   => $configs['DATABASE_KFTASK']['user'],
	'DB_USER_JAVA'   => $configs['DATABASE_KFTASK']['pwd'],
	'DB_PWD_JAVA'   => $configs['DATABASE_KFTASK']['db'], 
	
	'DOMAIN_MOBILE'=>$configs['DOMAIN']['DOMAIN_MOBILE'], //移动端域名访问路径

);