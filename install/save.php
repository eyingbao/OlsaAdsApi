<?php


/*print_r($_POST['kk']);
exit;*/

foreach($_POST['kk']  as $k=>$v){
	$k2 = explode('-',$k);
	$b[$k2[0]][$k2[1]] = $v;
}


if($_POST['admin'] == ''){
	echo '{"suc":false,"msg":"请设置管理员账号"}';
	exit;
}
if($_POST['pwd'] == ''){
	echo '{"suc":false,"msg":"请设置管理员密码"}';
	exit;
}




////////////////////////////////////////// 链接数据库 ////////////////////////////////////////

$con['51'] = mysqli_connect($b['DATABASE_KFPC']['host'],$b['DATABASE_KFPC']['user'],$b['DATABASE_KFPC']['pwd']);
if (!$con['51']){
	echo '{"suc":false,"msg":"数据库无法链接"}';
	exit;
}

if(mysqli_select_db($con['51'],$b['DATABASE_KFPC']['db'])){
	echo '{"suc":false,"msg":"该数据库已存在"}';
	exit;
}else{
	
	$admin['cid'] = $b['ADWORDS']['clientCustomerId'];
	$admin['admin'] = $_POST['admin'];
	$admin['pwd'] = md5($_POST['pwd']);
	//$admin['cid'] = $b['DATABASE_KFPC']['host'];
	
	$sql = "create database {$b['DATABASE_KFPC']['db']} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	mysqli_query($con[51],$sql);
	$con2 = mysqli_connect($b['DATABASE_KFPC']['host'],$b['DATABASE_KFPC']['user'],$b['DATABASE_KFPC']['pwd'],$b['DATABASE_KFPC']['db']);
	//$link = mysqli_select_db($con,$db);
	
	//print_r($admin);
//exit;
	createTable($con2,$b['DATABASE_KFPC']['db'],$admin);
}







/*$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	
print_r($row);
exit;*/



//////////////////////////////////////////////////////////////////////////////////////////////


file_put_contents("../lock.txt",4);






$str = '';
foreach($b as $k=>$v){
	$str.="[{$k}]\n";
	foreach($v as $k2=>$v2){
		$str.="$k2=\"{$v2}\"\n";
	}
}
file_put_contents("../kf_task/config.ini",$str);
file_put_contents("../kfpc/config.ini",$str);
file_put_contents("../kfmobile/config.ini",$str);



echo '{"suc":true,"msg":"success"}';
exit;








function createTable($link,$db,$admin){
	
	mysqli_query($link,"SET NAMES utf8");
	
	$sql = "CREATE TABLE `ads_column` (
			  `id` int(11) NOT NULL,
			  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `pid` smallint(6) NOT NULL,
			  `controller` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
			  `icon` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
			  `status` tinyint(2) NOT NULL,
			  `listorder` int(11) NOT NULL DEFAULT '0'
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";	
	
	mysqli_query($link,$sql);
	
	$sql = "INSERT INTO `ads_column` (`id`, `name`, `pid`, `controller`, `icon`, `status`, `listorder`) VALUES
				(1, '广告系列', 0, 'Home/Campaign', 'fa fa-cube', 1, 102),
				(2, '日预算异常', 1, 'Home/Campaign/index', '', 1, 97),
				(3, '广告组', 0, 'Home/AdGroup', 'fa fa-share-alt', 1, 99),
				(4, '广告组（CTR）异常', 3, 'Home/AdGroup/index', '', 1, 103),
				(5, '系统设置', 0, 'Home/Manager', 'fa fa-cog', 1, 96),
				(6, '用户管理', 5, 'Home/Manager/index', '', 1, 99),
				(7, '消耗异常', 1, 'Home/Campaign/costErr', '', 1, 103),
				(8, '关键词（得分）异常', 3, 'Home/AdGroup/keywordsErr', '', 1, 98),
				(9, '广告异常', 3, 'Home/AdGroup/adErr', '', 1, 101),
				(10, '同步MCC账户', 5, 'Home/Manager/update', '', 0, 0),
				(11, '账户', 0, 'Home/Mcc', 'fa fa-user', 1, 103),
				(12, '余额提醒', 11, 'Home/Mcc/index', '', 1, 98),
				(13, '投放渠道', 1, 'Home/Mcc/deliveryChannel', '', 1, 102),
				(14, 'MCC（总消耗）', 11, 'Home/Mcc/monthCost', '', 1, 101),
				(15, '广告语（CTR）异常', 3, 'Home/AdGroup/adCtrErr', '', 1, 0),
				(16, '广告语（数量）异常', 3, 'Home/AdGroup/adCountErr', '', 1, 102),
				(17, 'MCC（活跃数）', 11, 'Home/Mcc/active', '', 1, 100),
				(18, '转化数据', 11, 'Home/Mcc/tools', '', 1, 0),
				(19, '转化次数对比', 1, 'Home/Campaign/conversionsContrast', '', 1, 101),
				(20, 'CPA对比', 1, 'Home/Campaign/cpaContrast', '', 1, 99),
				(21, '更新MCC', 5, 'Home/Manager/updateMcc', '', 1, 0),
				(22, 'olsa日报', 11, 'Home/Mcc/olsa', '', 0, 102),
				(23, '账户评级', 11, 'Home/Mcc/alertedList', '', 1, 97)";
	mysqli_query($link,$sql);
	
	$sql = "CREATE TABLE `ads_manager` (
				  `id` int(11) NOT NULL,
				  `mcc` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
				  `account` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
				  `nickname` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
				  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
				  `create_time` int(11) NOT NULL,
				  `login_time` int(11) NOT NULL,
				  `status` tinyint(4) NOT NULL,
				  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `tel` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
				
	mysqli_query($link,$sql);
	
	$sql = "INSERT INTO `{$db}`.`ads_manager` (`id`, `mcc`, `account`, `nickname`, `password`, `create_time`, `login_time`, `status`, `email`, `tel`) VALUES
(1, '{$admin['cid']}', '{$admin['admin']}', 'admin', '{$admin['pwd']}', 1435652670, 1435652670, 1, NULL, NULL)";
	
	//echo $sql;
	
	mysqli_query($link,$sql);		
	
	
	$sql = "CREATE TABLE `n_accountinfo` (
  `id` int(11) NOT NULL,
  `customer_Id` varchar(12) DEFAULT NULL,
  `loginName` varchar(50) DEFAULT NULL,
  `loginPassword` varchar(32) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `address` varchar(70) DEFAULT NULL,
  `compaign` varchar(50) DEFAULT NULL,
  `mcc` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC";
	mysqli_query($link,$sql);	
				
}
?>