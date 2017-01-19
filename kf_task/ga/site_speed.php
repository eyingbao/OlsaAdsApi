<?php
	echo 'google analytics_speed...';
	require_once '../config.php';
	require_once 'urls_config.php';
	$con = mysqli_connect($db['51']['host'],$db['51']['user'],$db['51']['pwd'],$db['51']['db']);
	if (!$con){
		die('Could not connect: ' . mysqli_error());
	}
	$sql = "SELECT * FROM ads_ga";
	$result = mysqli_query($con,$sql);
	$list = array();
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
	  {
		$rs[0] = $row['id'];
		$rs[1] = $row['ga_id'];
		
		array_push($list,$rs);
	 }


//$list = array_slice($list,0,2);
//$list = array_slice($list,0,5);

$list = curl($list);



$idsArr = array();
foreach($list as $v){
	array_push($idsArr,$v['id']);
}
//$idsArr = array_unique($idsArr);
/**/
$ids = implode(',',$idsArr);

$sql = "UPDATE ads_ga ";

$sql .= " SET BounceRate = CASE id ";

foreach($list as $v2){
	$v = $v2['data'];
	//foreach($v2['data'] as $v){
		$sql .= " WHEN {$v2['id']} THEN {$v[0]} ";
	//}
}
$sql .= ' END, ';

$sql .= ' Loadtime = CASE id ';

foreach($list as $v2){
	$v = $v2['data'];
		$sql .= " WHEN {$v2['id']} THEN {$v[2]} ";
	
}

$sql .= ' END, ';

$sql .= ' Dwelltime = CASE id ';

foreach($list as $v2){
	$v = $v2['data'];
		$sql .= " WHEN {$v2['id']} THEN {$v[1]} ";
	
}

$sql .= ' END ';

$sql .= ' WHERE id IN ('.$ids.')';

//print_r($sql);
//exit;

//mysql_select_db("awreports", $con);

//$sql = "INSERT INTO `test` (`id`, `test`) VALUES (NULL, '{$sql}')";

mysqli_query($con,$sql);

/*
UPDATE categories
    SET display_order = CASE id
        WHEN 1 THEN 3
        WHEN 2 THEN 4
        WHEN 3 THEN 5
    END,
    title = CASE id
        WHEN 1 THEN 'New Title 1'
        WHEN 2 THEN 'New Title 2'
        WHEN 3 THEN 'New Title 3'
    END
WHERE id IN (1,2,3)
*/

//echo '<pre>';
//print_r($sql);

//echo '</pre>';
//exit;


function curl($list){
	if(is_array($list) && !empty($list)){
		$fs = array();
		$reportList = array();
		$limit =5;
		$count = ceil(count($list) / $limit);
		for($i = 1 ; $i<=$count;$i++){
			$page = ($i-1)*$limit;
			array_push($fs,array_slice($list,$page,$limit));
		}
	}
	
	
	
	//多线程抓取
	$url = urls.'site_data.php?id=';
 	$final = array();
	$fp= array();
	for($i = 0; $i< count($fs);$i++){
		$final= array();
		
		foreach($fs[$i] as $v){
			array_push($final,$url.$v[0].'&gaid='.$v[1]);
		}
		//return $final;
		$rs = async_get_url($final);
		
		foreach($rs as $v){
			$v = json_decode($v,true);
			 array_push($fp,$v);
			//$fp = array_merge($fp,$v);
			//return $v;
			/*if($v!='e'){
				$v =  json_decode($v,true);
				if(is_array($v)){
					$fp = array_merge($fp,$v);
				}
			}*/
		}
	}	
	
	return $fp;
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

