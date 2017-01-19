<?php
$configs = parse_ini_file('config.ini',true);
define('phpAdsGa_CLIENT_ID',$configs['OAUTH2']['clientId']); //phpAdsGa项目ID
define('phpAdsGa_CLIENT_SECRET',$configs['OAUTH2']['clientSecret']);  //phpAdsGa项目秘钥	
define('phpAdsGa_REFRESH_TOKEN',$configs['OAUTH2']['refreshToken']); //phpAdsGa项目 refresh_token
define('DOMAIN_TASK',$configs['DOMAIN']['DOMAIN_TASK']);  //系统php执行任务的域名访问路径
define('DOMAIN_PC',$configs['DOMAIN']['DOMAIN_PC']);  //系统后台管理域名
define('TASK_JAVA_DIR',$configs['DIR']['TASK_JAVA_DIR']); //aw-reporting文件夹所在的硬盘路径
define('TASK_PHP_DIR',$configs['DIR']['TASK_PHP_DIR']); //kf_task文件夹所在硬盘路径
//aw-reporting数据配置信息
$db['51'] = array(
	'host'=>$configs['DATABASE_KFTASK']['host'],
	'user'=>$configs['DATABASE_KFTASK']['user'],
	'pwd'=>$configs['DATABASE_KFTASK']['pwd'],
	'db'=>$configs['DATABASE_KFTASK']['db'],
);
//pc后台数据库配置信息
$db['115'] = array(
	'host'=>$configs['DATABASE_KFPC']['host'],
	'user'=>$configs['DATABASE_KFPC']['user'],
	'pwd'=>$configs['DATABASE_KFPC']['pwd'],
	'db'=>$configs['DATABASE_KFPC']['db'],
);	
?>