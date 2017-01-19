<?php
	echo 'google analytics & adwords downloading...';
	require_once '../config.php';
	require_once 'urls_config.php';
	$con = mysqli_connect($db['51']['host'],$db['51']['user'],$db['51']['pwd'],$db['51']['db']);
	if (!$con){
		die('Could not connect: ' . mysqli_error());
	}
	$analytics = require_once 'gaconfig.php';
	$list = a($analytics);
	$list=  curl($list);
	$time = time();
	$datetime = date('Y-m-d H:i:s',$time);
	$str='';
	foreach($list as $k=>$v){
		$aid = str_replace('-','',$v[1]);
		$str.='(';
		$str.="'{$v[1]}',";
		$str.="'{$aid}',";
		$str.="'{$k}',";
		$str.=$time.',';
		$str.="'{$datetime}',";
		$str.="'{$v[0]}',";
		$str.="{$v[2]},";
		$str.="'',";
		$str.="'',";
		$str.="''";
		$str.='),';
	}
	
	$str = rtrim($str,',');
	
	
	
	//mysql_select_db("awreports", $con);
	//mysql_query('TRUNCATE ads_ga2');
	//$sql = 'INSERT INTO ads_ga2(mcc, account,ua,time,datetime,ga_id,ua_id,BounceRate,Loadtime,Dwelltime) VALUES'.$str;
	//echo $sql;
	//exit;
	//$result = mysql_query($sql);
	//if(mysql_errno() == 0){
		$nsql = "CREATE TABLE IF NOT EXISTS `ads_ga` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `mcc` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
					  `account` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
					  `ua` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
					  `time` int(11) NOT NULL,
					  `datetime` datetime NOT NULL,
					  `ga_id` int(11) NOT NULL DEFAULT '0',
					  `ua_id` int(11) NOT NULL DEFAULT '0',
					  `BounceRate` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `Loadtime` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `Dwelltime` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
		mysqli_query($con,$nsql);
		mysqli_query($con,'TRUNCATE ads_ga');
		$sql = 'INSERT INTO ads_ga(mcc, account,ua,time,datetime,ga_id,ua_id,BounceRate,Loadtime,Dwelltime) VALUES'.$str;
		$result = mysqli_query($con,$sql);
	//}
	mysqli_close($con);




//$list=  curl2($list);
//echo '<pre>';
//print_r($list);
//echo '</pre>';

//echo json_encode($list);




//









function curl($list){
	if(is_array($list) && !empty($list)){
		$fs = array();
		$reportList = array();
		$limit =2;
		$count = ceil(count($list) / $limit);
		for($i = 1 ; $i<=$count;$i++){
			$page = ($i-1)*$limit;
			array_push($fs,array_slice($list,$page,$limit));
		}
	}
	
	
	
	//多线程抓取
	$url = urls.'webproperties.php?id=';
 	$final = array();
	$fp= array();
	for($i = 0; $i< count($fs);$i++){
		$final= array();
		foreach($fs[$i] as $v){
			array_push($final,$url.$v);
		}
		//return $final;
		$rs = async_get_url($final);
		
		foreach($rs as $v){
			if($v!='e'){
				$v =  json_decode($v,true);
				if(is_array($v)){
					$fp = array_merge($fp,$v);
				}
				
			}
		}
	}	
	
	return $fp;
}

function curl2($list){
	if(is_array($list) && !empty($list)){
		$fs = array();
		$reportList = array();
		$limit = 5;
		$count = ceil(count($list) / $limit);
		for($i = 1 ; $i<=$count;$i++){
			$page = ($i-1)*$limit;
			array_push($fs,array_slice($list,$page,$limit));
		}
	}
	//多线程抓取
	$url = urls.'adwords.php?uaid=';
 	$final = array();
	$f= array();
	for($i = 0; $i< count($fs);$i++){
		$final= array();
		foreach($fs[$i] as $v){
			array_push($final,$url.$v);
		}
		$rs = async_get_url($final);
		foreach($rs as $v){
			$v = str_replace("\r\n", "",$v);
			if($v!= '0'){
				$a = explode(',',$v);
				
				$f[$a[0]] =  $a[1];
			}
		}
	}
	return $f;
}


function a($analytics){
	$arr = array();
	try {
	  $accounts = $analytics->management_accounts->listManagementAccounts();
		
	  foreach($accounts->getItems() as $v){
		  
		  array_push($arr,$v['id']);
	  }
	  return $arr;
	} catch (apiServiceException $e) {
		return $arr;
	} catch (apiException $e) {
		return $arr;
	}
}


function async_get_url($url_array, $wait_usec = 0)
	{
		if (!is_array($url_array))
			return false;
	
		$wait_usec = intval($wait_usec);
	
		$data    = array();
		$handle  = array();
		$running = 0;
	
		$mh = curl_multi_init(); // multi curl handler
	
		$i = 0;
		foreach($url_array as $url) {
			$ch = curl_init();
	
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
			curl_setopt($ch, CURLOPT_MAXREDIRS, 7);
	
			curl_multi_add_handle($mh, $ch); // 把 curl resource 放進 multi curl handler 裡
	
			$handle[$i++] = $ch;
		}
	
		/* 執行 */
		/* 此種做法會造成 CPU loading 過重 (CPU 100%)
		do {
			curl_multi_exec($mh, $running);
	
			if ($wait_usec > 0) // 每個 connect 要間隔多久
				usleep($wait_usec); // 250000 = 0.25 sec
		} while ($running > 0);
		*/
	
		/* 此做法就可以避免掉 CPU loading 100% 的問題 */
		// 參考自: http://www.hengss.com/xueyuan/sort0362/php/info-36963.html
		/* 此作法可能會發生無窮迴圈
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
	
		while ($active and $mrc == CURLM_OK) {
			if (curl_multi_select($mh) != -1) {
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		*/
		/*
		// 感謝 Ren 指點的作法. (需要在測試一下)
		// curl_multi_exec的返回值是用來返回多線程處裡時的錯誤，正常來說返回值是0，也就是說只用$mrc捕捉返回值當成判斷式的迴圈只會運行一次，而真的發生錯誤時，有拿$mrc判斷的都會變死迴圈。
		// 而curl_multi_select的功能是curl發送請求後，在有回應前會一直處於等待狀態，所以不需要把它導入空迴圈，它就像是會自己做判斷&自己決定等待時間的sleep()。
		*/
		do {
			curl_multi_exec($mh, $running);
			curl_multi_select($mh);
		} while ($running > 0);
	
		/* 讀取資料 */
		foreach($handle as $i => $ch) {
			$content  = curl_multi_getcontent($ch);
			$data[$i] = (curl_errno($ch) == 0) ? $content : false;
		}
	
		/* 移除 handle*/
		foreach($handle as $ch) {
			curl_multi_remove_handle($mh, $ch);
		}
	
		curl_multi_close($mh);
	
		return $data;
	}
		
		
		



?>

