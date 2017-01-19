<?php
require_once 'header_status.php';
define('URL2',DOMAIN_TASK.'/ads/reports.php?cid=');
$list2 = array();
if(is_array($list) && !empty($list)){
	foreach($list as $v){
		array_push($list2,URL2.$v[0]);
	}
}


//print_r($list2);
//exit;

$ar = page($list2,50);
//$br = page($list2,2);

//print_r($ar);
//exit;
//$ar[0] = $br[0];
//exit;

$final = array();
if(is_array($ar) && !empty($ar)){
	foreach($ar as $k=>$v){
		$arr = async_get_url($v);
		foreach($arr as $v2){
			array_push($final,json_decode($v2,true));
		}
	}
	 insertData($final,$con,$db);
}

function insertData($data,&$con,&$db){
	$time = time();
	$dates = date('Y-m-d H:i:s',$time);
	$sql = "INSERT INTO `ads_account_rating_data` (`id`, `kw_point`, `kw_search_all`, `convers`, `campaign_ctr`, `account_cost_err`, `account_cost_d7`, `account_cost_14`, `mcc`, `account_id`, `time`, `dates`) VALUES";
	foreach($data as $v){
		if(is_array($v) && !empty($v)){
			$sql.="(NULL, '{$v['kw_point']}', '{$v['kw_search_all']}', '{$v['convers']}', '{$v['campaign_ctr']}', '{$v['account_cost_err']}', '{$v['account_cost_d7']}', '{$v['account_cost_14']}', '{$v['mcc']}', '{$v['account_id']}','{$time}','{$dates}'),";
		}
	}
	$sql =  rtrim($sql,',');
	//return $sql;
	mysqli_query($con['51'],"SET NAMES UTF8");
	
	$nsql = "CREATE TABLE IF NOT EXISTS `ads_account_rating_data` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `kw_point` int(100) NOT NULL,
				  `kw_search_all` varchar(100) NOT NULL,
				  `convers` varchar(100) NOT NULL,
				  `campaign_ctr` varchar(100) NOT NULL,
				  `account_cost_err` varchar(100) NOT NULL,
				  `account_cost_d7` varchar(100) NOT NULL,
				  `account_cost_14` varchar(100) NOT NULL,
				  `mcc` varchar(12) NOT NULL,
				  `account_id` varchar(100) NOT NULL,
				  `time` int(11) NOT NULL,
				  `dates` datetime NOT NULL,
				  `budget_err` varchar(100) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	mysqli_query($con['51'],$nsql);
	mysqli_query($con['51'],'TRUNCATE ads_account_rating_data');
	mysqli_query($con['51'],$sql);
}
?>