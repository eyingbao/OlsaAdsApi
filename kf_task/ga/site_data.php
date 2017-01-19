<?php
$analytics = require_once 'gaconfig.php';
$ga = $_GET['gaid'];
//$ga = 82410989;
$d2 =date("Y-m-d",strtotime("-1 day"));
$d1 =date("Y-m-d",strtotime("-7 day"));
$dimensions = '';
$metrics = 'ga:bounceRate,ga:avgTimeOnPage,ga:avgPageLoadTime';
$sort = 'ga:bounceRate';
try { 
$rs = $analytics->data_ga->get(
	'ga:'.$ga,
	$d1,
	$d2,
	$metrics,
	array(
		'dimensions' =>$dimensions,
		'sort' => $sort
	));
	try { 
		$arr =$rs->getRows();
		if(is_array($arr) && !empty($arr)){
			foreach($arr[0] as $k=>$v){
				$f[$k] = sprintf("%.2f", $v); 
			}
		}else{
			$f = array(0,0,0);
		}
	} catch (Exception $e) { 
		//echo $e->getMessage();
		//exit;
		$f = array(0,0,0);
	}
} catch (Exception $e) { 
//echo $e->getMessage();
//exit;
	$f = array(0,0,0);
}

//$_GET['id'] = 1;
$m['id'] = $_GET['id'];
//$m['id'] =1;
$m['data'] = $f;
echo json_encode($m);
exit;
//echo '<pre>';
//print_r($m);
//echo '</pre>';
?>

