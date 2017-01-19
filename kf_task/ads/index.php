<?php
	require_once 'header_budget.php';
	$list2 = array();
	if(is_array($list) && !empty($list)){
		foreach($list as $v){
			array_push($list2,URL.'blance.php?cid='.$v[0]);
		}
	}
	
	
	
	$ar = page($list2,10);
	
	//$br = page($list2,2);
	//$ar[0] = $br[0];
	
	//print_r($ar);
	//exit;
	
	$final = array();
	if(is_array($ar) && !empty($ar)){
		foreach($ar as $v){
			$arr = async_get_url($v);
			foreach($arr as $v2){
				array_push($final,$v2);
			}
		}
	}
	if(is_array($final) && !empty($final)){
		$str = '';
		$str2 = '';
		foreach($final  as $v){
			$rs = json_decode($v,true);
			if(is_array($rs) && !empty($rs)){
				if($rs[1] == 'e'){
					$str2.="({$rs[0]},".time()."),";	
				}else{
					$str.="({$rs[0]},{$rs[1]},".time().",'".date('Y-m-d H:i:s',time())."','{$rs[2]}','{$rs[3]}','{$rs[4]}'),";	
				}
			}
		}
		$str =  rtrim($str,',');
		$str2 =  rtrim($str2,',');
	}
	
//	echo $str;
//	exit;
	
	$nsql = "CREATE TABLE IF NOT EXISTS `ads_budget` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `cid` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
				  `budget` decimal(10,2) NOT NULL,
				  `time` int(11) NOT NULL,
				  `datetime` datetime NOT NULL,
				  `start_date` date NOT NULL,
				  `acost` decimal(10,2) DEFAULT NULL,
				  `bce` decimal(10,2) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
	
	mysqli_query($con['51'],$nsql);
	mysqli_query($con['51'],'TRUNCATE ads_budget');
	$sql = "INSERT INTO ads_budget(cid,budget,time,datetime,start_date,acost,bce) VALUES ".$str;
	mysqli_query($con['51'],$sql);
	mysqli_close($con['51']);
?>