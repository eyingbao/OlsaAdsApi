<?php
	require_once 'header.php';
	$time = time();
	$datetime = date('Y-m-d H:i:s',$time);
	$list2 = array();
	if(is_array($list) && !empty($list)){
		foreach($list as $v){
			array_push($list2,URL.'conversions_server.php?cid='.$v[0]);
		}
	}
	
	
	//$ar[0] = array_slice($list2,0,2);
	$str='';
	$ar = page($list2,5);
	
	/*echo '<pre>';
	print_r($ar);
	echo '</pre>';
	exit;*/
	
	$f = array();
	for($i = 0 ; $i < count($ar); $i++){
		//if($i < 2){
			$a = async_get_url($ar[$i]);
			$temp = array_values($a);
			$f = array_merge($f,$temp);
	}
	foreach($f as $v){
		$b = json_decode($v,true);
		$aid = str_replace('-','',$b[0]);
		$str.='(';
		$str.="'{$b[0]}',";
		$str.="'{$aid}',";
		$str.="'{$b[1]}',";
		$str.="'{$b[2]}',";
		$str.=$time.',';
		$str.="'{$datetime}'";
		$str.='),';
	}
	mysqli_query($con['51'],"SET NAMES utf8");
	$str = rtrim($str,',');
	$nsql = "CREATE TABLE IF NOT EXISTS `ads_conversions` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `mcc` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
				  `account` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
				  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
				  `status` tinyint(2) NOT NULL DEFAULT '0',
				  `time` int(11) NOT NULL,
				  `datetime` datetime NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
	mysqli_query($con['51'],$nsql);
	mysqli_query($con['51'],'TRUNCATE ads_conversions');
	$sql = 'INSERT INTO ads_conversions(mcc, account,name,status,time,datetime) VALUES'.$str;
	mysqli_query($con['51'],$sql);
	mysqli_close($con['51']);
?>

