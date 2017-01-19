<?php
require_once 'header_status.php';
define('URL2',DOMAIN_PC.'/Home/Alerted/suggest?cid=');
$list2 = array();
if(is_array($list) && !empty($list)){
	foreach($list as $v){
		array_push($list2,URL2.$v[0]);
	}
}
//print_r($list2);
//exit;
$ar = page($list2,5);
//$br = page($list2,5);
//$ar[0] = $br[0];
//print_r($ar);
//exit;

$final = array();
if(is_array($ar) && !empty($ar)){
	foreach($ar as $k=>$v){
		$arr = async_get_url($v);
		foreach($arr as $v2){
			$b  =json_decode($v2,true);
			if(is_array($b) && !empty($b)){
				array_push($final,$b);
			}
		}
	}
	echo insertData($final,$con,$db);
}
function insertData($data,&$con,&$db){
	$sql = "INSERT INTO `ads_account_rating` (`id`, `costErr`, `renewals`, `budgetErr`, `campaignCtr`, `delivery`, `kwPoint`, `kwCount`, `adCount`, `ref`, `BounceRate`, `Loadtime`, `Dwelltime`, `convers`, `crmServerCount`, `time`, `date`, `point` ,`account_id` ,`cid`) VALUES";
	foreach($data as $v){
		$sql.="(NULL, '{$v['costErr']}', '{$v['renewals']}', '{$v['budgetErr']}', '{$v['campaignCtr']}', '{$v['delivery']}', '{$v['kwPoint']}', '{$v['kwCount']}', '{$v['adCount']}', '{$v['ref']}', '{$v['BounceRate']}', '{$v['Loadtime']}', '{$v['Dwelltime']}', '{$v['convers']}', '{$v['crmServerCount']}', '{$v['time']}', '{$v['date']}', '{$v['point']}' , '{$v['account_id']}', '{$v['cid']}'),";
	}
	$sql =  rtrim($sql,',');
	mysqli_query($con['51'],"SET NAMES UTF8");
	//return $sql;
	$nsql = "CREATE TABLE IF NOT EXISTS `ads_account_rating` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `costErr` varchar(300) NOT NULL,
				  `renewals` varchar(300) NOT NULL,
				  `budgetErr` varchar(300) NOT NULL,
				  `campaignCtr` varchar(300) NOT NULL,
				  `delivery` varchar(300) NOT NULL,
				  `kwPoint` varchar(300) NOT NULL,
				  `kwCount` varchar(300) NOT NULL,
				  `adCount` varchar(300) NOT NULL,
				  `ref` varchar(300) NOT NULL,
				  `BounceRate` varchar(300) NOT NULL,
				  `Loadtime` varchar(300) NOT NULL,
				  `Dwelltime` varchar(300) NOT NULL,
				  `convers` varchar(300) NOT NULL,
				  `crmServerCount` varchar(300) NOT NULL,
				  `time` int(11) NOT NULL,
				  `date` datetime NOT NULL,
				  `point` int(11) NOT NULL,
				  `account_id` bigint(11) NOT NULL,
				  `cid` varchar(12) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
	mysqli_query($con['51'],$nsql);
	mysqli_query($con['51'],'TRUNCATE ads_account_rating');
	mysqli_query($con['51'],$sql);
}
?>