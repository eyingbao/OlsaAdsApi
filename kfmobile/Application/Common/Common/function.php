<?php


//获取广告系列状态
function get_cam_status($name,$cid){
	$sql = "SELECT case CAMPAIGN_STATUS when 'paused' then '已暂停'  when 'removed' then '已移除' else '已启用' end as CAMPAIGN_STATUS FROM `aw_reportcampaign` WHERE CAMPAIGN_NAME = '{$name}' AND `ACCOUNT_ID` = {$cid} ORDER BY DAY DESC LIMIT 0 ,1";
	
	$rs = M('reportcampaign','aw_','mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8')->query($sql);
	
	return $rs[0]['campaign_status'];
}

function pr($rs){
	header("Content-type: text/html; charset=utf-8");
	echo '<pre>';
	
	foreach($rs as $v){
	print_r($v);
	}
	
	
	echo '<pre>';
	exit;	
	
}
function replace_dir($dir){
	
	$dir = str_replace('\\','/',$dir);
	
	
	return $dir;
}

function get_ua($con,$ids,$b=true){
	$sql = "SELECT ua as name ,account as id  FROM ads_ga WHERE account IN ({$ids})";
	$list = M('',NULL,$con)->query($sql);
	$arr = array();
	if(is_array($list) && !empty($list)){
		foreach($list as $v){
			if(!$b){
					$k = filter_mcc($v['id']);
					$arr[$k] = $v['name'];
			}else{
				$arr[$v['id']] = $v['name'];
			}
		}
	}
	return $arr;
}

function get_conversions($con,$ids,$b=true){
	$sql = "SELECT  name ,account as id ,status  FROM ads_conversions WHERE account IN ({$ids})";
	$list = M('',NULL,$con)->query($sql);
	$arr = array();
	if(is_array($list) && !empty($list)){
		foreach($list as $v){
			if(!$b){
					$k = filter_mcc($v['id']);
					$arr[$k] = array($v['name'],$v['status']);
			}else{
				$arr[$v['id']] =array($v['name'],$v['status']);
			}
		}
	}
	
	return $arr;
}

function quick_mcc_name($con,$ids,$b = true){
	$sql = "
	SELECT DISTINCT new.id ,new.name FROM (
	SELECT ACCOUNT_ID as id ,ACCOUNT_DESCRIPTIVE_NAME as name  FROM aw_reportaccount WHERE ACCOUNT_ID IN ({$ids})
	
	) AS new";
	
//	return $sql;
	
	$list = M('',NULL,$con)->query($sql);
	$arr = array();
	if(is_array($list) && !empty($list)){
		foreach($list as $v){
			if(!$b){
					$k = filter_mcc($v['id']);
					$arr[$k] = $v['name'];
			}else{
				$arr[$v['id']] = $v['name'];
			}
				
		
		}
	}
	
	return $arr;
}



function post_curl($url,$data){
	//$data = $_POST;
	//$url = URL.'api/examples/AdWords/v201502/test.php';
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query($data));
	$rs = curl_exec ( $ch );
	curl_close ($ch);
	
	return $rs;
}



/**
 * csv_get_lines 读取CSV文件中的某几行数据
 * @param $csvfile csv文件路径
 * @param $lines 读取行数
 * @param $offset 起始行数
 * @return array
 * */
function csv_get_lines($csvfile, $lines, $offset = 0) {
    if(!$fp = fopen($csvfile, 'r')) {
     return false;
    }
    $i = $j = 0;
 while (false !== ($line = fgets($fp))) {
  if($i++ < $offset) {
   continue; 
  }
  break;
 }
 $data = array();
 while(($j++ < $lines) && !feof($fp)) {
  $data[] = fgetcsv($fp);
 }
 fclose($fp);
    return $data;
}

function Headers($url){
	//echo $url;
	//exit;
	
	header("Location: {$url}");
	exit;
}

function json_msg($suc,$msg){
	$arr['suc'] = $suc;
	$arr['msg'] = $msg;
	echo json_encode($arr);
	exit;
}
function index_array($arr,$ka){
	$list = array();
	foreach($arr as $v){
		array_push($list,"'".$v[$ka]."'");
	}
	return $list;
}

function index_array2($arr,$ka){
	$list = array();
	foreach($arr as $v){
		array_push($list,"'".$v."'");
	}
	return $list;
}

function index_array3($arr,$ka){
	$list = array();
	foreach($arr as $v){
		array_push($list,$v[$ka]);
	}
	return $list;
}

function each_array($arr,$key,$val){
	$list = array();
	foreach($arr as $v){
		$list[$v[$key].''] = $v[$val];
	}
	return $list;
}



function check_email($str){ //邮箱正则表达式 
	return (preg_match('/^[_.0-9a-z-a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$/',$str))?true:false; 
}




//验证是否是合法mccId
function is_mcc($id){
	$regex = '/[0-9]{3}-[0-9]{3}-[0-9]{4}$/';
	if(preg_match($regex, $id, $matches)){
		return true;
	}else{
		return false;	
	}
}

function is_email($str){ //邮箱正则表达式 
		return (preg_match('/^[_.0-9a-z-a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,4}$/',$str))?true:false; 
}


//递归
function findChildren($arr,$id,&$pushArr){
	foreach($arr as $v){
		if($v['pid'] == $id){
			//if($v['canManageClients'] == '0'){
				array_push($pushArr,$v['customerid']);
			//}
			findChildren($arr,$v['customerid'],$pushArr);
		}
	}
}


//递归
function findCustomerChildren($arr,$id,&$pushArr){
	foreach($arr as $v){
		if($v['pid'] == $id){
			//if($v['canManageClients'] == '0'){
				array_push($pushArr,$v['customerid']);
			//}
			findCustomerChildren($arr,$v['customerid'],$pushArr);
		}
	}
}


//递归查找栏目
function findColumn($list,&$pushArr){
	$arr = array();
	foreach($list as $v){
		if($v['pid'] == 0){
			$m = $v;
			foreach($list as $v2){
				if($v2['pid'] == $v['id']){
					$m['children'] = array();
					array_push($m['children'],$v2);
				}
			}
			array_push($pushArr,$m);	
		}
	}	
}


function findColumn2($list,$id){
	$i=0;
	foreach($list as $v){
		if($v['pid'] == $id){
			$i++;
			$arr[$i] = $v;
			$arr[$i]['children'] = findColumn2($list,$v['id']);
			
		}
	}
	return $arr;	
}


function optionTree($arr,$id,$j,&$pushArr){
	$j++;
	foreach($arr as $v){
		//$j = $j;
		if($v['pid'] == $id){
			//echo $id;
			//$val='aa';
			$val='';
			$f=$j+$v['level'];
			for($i = 0; $i<$f; $i++){
				$val.='&nbsp;&nbsp;&nbsp;';
			}
			
			$val.="└─";
			
			//echo $val.'<br />';
			$c['id'] = $v['customerid'];
			$c['name'] = $val.$v['name'];
			
			array_push($pushArr,$c);
			optionTree($arr,$v['customerid'],$j,$pushArr);
			
		}
	}
	
	//exit;
}

function get_cids($ids){
	$arr = array();
	foreach($ids as $v){
		array_push($arr,str_replace('-','',$v));
	}
	return $arr;
}

function get_date($date,$b=true){
	$d = explode('-',$date);
	
	if(count($d) == 1){
		$rs[0] = $rs[1] = $d[0];
		
	}else if(count($d) == 2){
		$rs = $d;
	}else{
		
	}
	
	$rs[1] = date("Ymd",strtotime("{$rs[1]} +1 day"));
	
	if(!$b){
		foreach($rs as $k=>$v){
			$rs[$k]= substr($v,0,4).'-'.substr($v,4,2).'-'.substr($v,6,2);
		}
	}
	return $rs;
	
}



//获取日期栏
function get_date_list(){
	//30天，45天，60天，75天，90天
	$list = array(
		/*0=>array(
			'value' => date('Ymd',strtotime('today')),
			'day' => '今日：'.date('Y年m月d日',strtotime('today'))
		),*/
		
		1=>array(
			'value' => date('Ymd',strtotime('-1 day')),
			'day' => '昨日：'.date('Y年m月d日',strtotime('-1 day'))
		),
		
		2=>array(
			'value' =>date('Ymd',strtotime('-2 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去02天：'.date('Y年m月d日',strtotime('-2 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		),
		
		3=>array(
			'value' =>date('Ymd',strtotime('-7 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去07天：'.date('Y年m月d日',strtotime('-7 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
		
		
		4=>array(
			'value' =>date('Ymd',strtotime('-14 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去14天：'.date('Y年m月d日',strtotime('-14 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
		
		5=>array(
			'value' =>date('Ymd',strtotime('-30 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去30天：'.date('Y年m月d日',strtotime('-30 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
		
		6=>array(
			'value' =>date('Ymd',strtotime('-45 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去45天：'.date('Y年m月d日',strtotime('-45 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
		
		7=>array(
			'value' =>date('Ymd',strtotime('-60 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去60天：'.date('Y年m月d日',strtotime('-60 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
		
		8=>array(
			'value' =>date('Ymd',strtotime('-75 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去75天：'.date('Y年m月d日',strtotime('-75 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
		
		9=>array(
			'value' =>date('Ymd',strtotime('-90 day')).'-'.date('Ymd',strtotime('-0 day')),
			'day' => '过去90天：'.date('Y年m月d日',strtotime('-90 day')).' - '.date('Y年m月d日',strtotime('-1 day'))
		
		),
	);
	return $list;
}

function timediff($begin_time,$end_time) 
{ 
	$days=round(($end_time-$begin_time)/3600/24) ;
	return $days;
}

//获取导航栏
function get_nav($cc){
	//$db = M();
	
	$sql = "SELECT ac2.name ,ac1.name AS now_name ,ac2.icon  FROM ads_column AS ac1 INNER JOIN ads_column AS ac2 ON ac1.pid = ac2.id WHERE ac1.controller = '{$cc}'";
	
	
	
	$rs = M()->query($sql);
	
	return $rs[0];
}

//过滤mcc
function filter_mcc($mcc){
	$cid[0] = substr($mcc,0,3);
	$cid[1] = substr($mcc,3,3);
	$cid[2] = substr($mcc,6,4);
	$mcc = $cid[0].'-'.$cid[1].'-'.$cid[2];
	return $mcc;
}


function callback($response, $info, $error){  
	$GLOBALS['date'] .= $response;
}  


function get_curl_url($list,$date,$origin){
	
	$arr =  array();
	
	foreach($list as $v){
		array_push($arr,
			array(  
				'url' => CURL_REPORT."?customerId={$v}&date={$date}&origin={$origin}&other=0&uid=".session('uid'), //秦美人  
				'method' => 'POST',  
				'post_data' => '',  
				'header' => null,  
				'options' => array(  
					CURLOPT_REFERER => "",  
					CURLOPT_COOKIE => "",  
				)
			)
		);
	}
	return $arr;
}

function get_curl_url_single($customerId,$date,$origin){
	
	//$arr =  array();
	
	//foreach($list as $v){
		//array_push($arr,
		return	array(  
				'url' => CURL_REPORT."?customerId={$customerId}&date={$date}&origin={$origin}&other=1&uid=".session('uid'), //秦美人  
				'method' => 'POST',  
				'post_data' => '',  
				'header' => null,  
				'options' => array(  
					CURLOPT_REFERER => "",  
					CURLOPT_COOKIE => "",  
				)
			);
		//);
	//}
	// $arr;
}



//获取记录数据
function get_report_data($controller,$post){
	
	$arr[0] = 6;
	$arr[1] = 19;
	$arr[2] = array(array('渠道-140624山东兴发炭素','2798306010','GSN-Main-160929','carbon tube','graphite tube',3));
	
	echo  json_encode(array($arr));
	exit;
	
	$date = get_date($post['date']);
	$arr = $post['ids'];
	$data = get_curl_url($arr,"{$date[0]},{$date[1]}",$controller);
	
	
	$list = array();
	foreach ($data as $val) {  
		array_push($list,$val['url']);
		
	} 
	//print_r($list);
//	exit;
	$arr = async_get_url($list);
	if(is_array($arr) && !empty($arr)){
		foreach($arr as $v){
			//$v = rtrim($v,',');
			//if(is_array(json_decode($v,true)) || is_numeric($v)){
				//$str.=$v.',';	
		//	}
			$str.=$v;	
		
		}
	}
	$str = rtrim($str,',');
	$json =  '['.$str.']';
	
	$arr=  json_decode($json,true);
	//pr(array($arr));
	echo  $json;
	
	
}


function myfunction($v){
	if ($v===1)
		{
		return true;
		}
	return false;
}
















function getCSV($tit,$header = array(),$list=array()){
	//$tDb = M(str_replace('yyb_','',$rs['table_name']));
	//$lists = $tDb->order('id DESC')->select();
//	$header = array('序号','提交时间','IP地址');
	
	$attr = json_decode($rs['field_attr'],true);
	foreach($attr as $v){
		array_push($header,$v['label']);
	}
	foreach($header as $v){
		$header_str.=mb_convert_encoding($v, "gb2312", "UTF-8").',';
	}
	$header_str = rtrim($header_str,',');
	//$list = array();
	/*$i=0;
	foreach($lists as $k=>$v){
		$i++;
		unset($v['status']);
		$v['id']='#'.$v['id'];
		$v['submit_time'] = date('Y年m月d号 H时:i分:s秒',$v['submit_time']);
		$m =explode('_',$k);
		foreach($v as $k2=>$v2){
			$m =explode('_',$k2);
			
			if($m[0]== 'formtel'){
					$v[$k2] = 'tel:'.$v2;
			}
			if($m[0]== 'formaddress' || $m[0]== 'formcheckbox'){
					$vv = json_decode($v2,true);
					$v[$k2]= implode('-',$vv);
			}
		}
		array_push($list,$v);	
	}
	
	
	//print_r($list);
	//exit;
	$tit = ($rs['title'])?$rs['title']:'我的表单';
	$tit = str_replace(',','',$tit);*/
	
	//$tit = 'aa';
	header( "Cache-Control: public" );
	header( "Pragma: public" );
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=".$tit.".csv");
	header('Content-Type:APPLICATION/OCTET-STREAM');
	ob_start();
	
	
	
	foreach ($list as $fields) {
		foreach ($fields as $key => $value) {
			$file_str.= mb_convert_encoding($value, "gb2312", "UTF-8") . ',';
		}
		$file_str.= "\r\n";
	 }
	ob_end_clean();
	echo $header_str."\r\n";
	echo $file_str;
	/*echo '<script type="text/javascript">alert(1)</script>';*/

}


/**
       * $num: 需要格式化的数字
       * $count_after_dot:小数点后保留的数字个数
     **/
      function format_number($num, $count_after_dot = 2) {
               $count_after_dot = (int)$count_after_dot;
                $pow = pow(10, $count_after_dot);
               $tmp = $num * $pow;
               $tmp = floor($tmp)/$pow;
               $format = sprintf('%%.%df', (int)$count_after_dot);
               $result = sprintf($format,  (float)$tmp);
             return $result;
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

function myMap($v)
{

  		
	
  return(str_replace('-','',$v));
}

function getMonthLastDay($month, $year) {
    switch ($month) {
        case 4 :
        case 6 :
        case 9 :
        case 11 :
            $days = 30;
            break;
        case 2 :
            if ($year % 4 == 0) {
                if ($year % 100 == 0) {
                    $days = $year % 400 == 0 ? 29 : 28;
                } else {
                    $days = 29;
                }
            } else {
                $days = 28;
            }
            break;

        default :
            $days = 31;
            break;
    }
    return $days;
}

function cut($cid){
	
	return substr($cid,0,3).'-'.substr($cid,3,3).'-'.substr($cid,6,10);
}
function is_arrays($arr){
	if(is_array($arr) && !empty($arr)){
		
		return $arr;
	}else{ 
		return array();
	}
}

//获取日期栏
function get_date_list2(){
	$week = date('w');
	$today = date('Y-m-d',strtotime('0 day'));
	$y = date('Y',strtotime('0 day'));
	$m = date('m',strtotime('0 day'));
	$d = date('d',strtotime('0 day'));
	$lastMonth = getlastMonthDays($today);
	//30天，45天，60天，75天，90天
	$list = array(
	
		array(
			'value' => date('Y-m-d',strtotime('-1 day')),
			'day' => '昨日'
		),
		
		array( 
			'value' => date('Y-m-d',strtotime((-$week-1).' day')).'|'.$today,
			'day' => '本周（周一至今天）'
		),
		
		array(
			'value' =>date('Y-m-d',strtotime('-7 day')).'|'.date('Y-m-d',strtotime('0 day')),
			'day' => '过去7天'
		),
		
		array(
			'value' =>date('Y-m-d',strtotime(((-$week-1)*2+1).' day')).'|'.date('Y-m-d',strtotime((-$week+1).' day')),
			'day' => '上周（周一至周日）'
		),
		
		array(
			'value' =>date('Y-m-d',strtotime('-14 day')).'|'.date('Y-m-d',strtotime('0 day')),
			'day' => '过去14天'
		),
		array(
			'value' =>date('Y-m-d',strtotime((-$d).' day')).'|'.date('Y-m-d',strtotime('0 day')),
			'day' => '本月'
		),
		array(
			'value' =>date('Y-m-d',strtotime('-30 day')).'|'.date('Y-m-d',strtotime('0 day')),
			'day' => '过去30天'
		
		),
		array(
			'value' =>$lastMonth[0].'|'.$lastMonth[1],
			'day' => '上月'
		
		),
	);
	return $list;
}

function getlastMonthDays($date){
     $timestamp=strtotime($date);
     $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
     $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
     return array($firstday,$lastday);
 }
?>