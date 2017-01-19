<?php
namespace Ads\Controller;
use Think\Controller;
class MobileController extends Controller {
	protected $connectReports;
	
	public function _initialize(){
		$this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
	}
	
	public function lostBudget(){
		$cid = $_GET['cid'];
		$lost = M('reportaccount','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}'")->order('Day  DESC')->limit(0,1)->avg('SEARCH_LOST_IS_RANK');
		$lost = '['.sprintf("%.2f",$lost).']';	
		echo $lost;
	}
	
	public function topCost(){
		$cid = $_GET['cid'];
		$d1 = $_GET['d1'];
		$d2 = $_GET['d2'];
		$sql = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS SUM(CLICKS) AS CLICKS , SUM(CONVERSION_VALUE) AS CONVERSION_VALUE  Day FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} GROUP BY Day) AS new  WHERE new.Day BETWEEN '{$d1}' AND '{$d2}' ORDER BY new.Day DESC";
		$list = M()->query($sql);
		$list = is_arrays($list);
		if(count($list) != 0){
			foreach($list as $k=>$v){
				$list[$k]['day'] = substr($v['day'],5,20);
				$temp = explode(' ',$list[$k]['day']);
				$temp2  = explode('-',$temp[0]);
				$list[$k]['day']  = $temp2[0].'月'.$temp2[1].'日'; 
			}
		}
		echo json_encode($list);
	}
	
	
	public function top7Cost(){
		$cid = $_GET['cid'];
		$d1 = $_GET['d1'];
		$d2 = $_GET['d2'];
		
		$limit = intval($_GET['limit']);
		if($limit > 0){
			$order = ' LIMIT 0 ,'.$limit;
		}else{
			$order = '';
		}
		
		if(isset($d2)){
			$sql = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS, SUM(CLICKS) AS CLICKS , SUM(ALL_CONVERSIONS) AS CONVERSION_VALUE ,  Day FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} GROUP BY Day) AS new  WHERE new.Day BETWEEN '{$d1}' AND '{$d2}' ORDER BY new.Day DESC".$order;
		
		}else{
			$sql = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS, SUM(CLICKS) AS CLICKS , SUM(ALL_CONVERSIONS) AS CONVERSION_VALUE ,  Day FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} GROUP BY Day) AS new  WHERE new.Day LIKE '%{$d1}%' ORDER BY new.Day DESC".$order;
		}
		
		
		
		$list = M('reportcampaign','aw_',$this->connectReports)->query($sql);
		//$list = M('reportaccount','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' ")-> field('COST,IMPRESSIONS,CONVERSION_VALUE,CLICKS,DAY')->order('Day DESC')->limit(0,7)->select();
		
		//print_r($list);
		$list = is_arrays($list);
		if(count($list) != 0){
			foreach($list as $k=>$v){
				$list[$k]['day2'] = str_replace(' 20:00:00','',$v['day']);	
				$list[$k]['day'] = substr($v['day'],5,20);
				$temp = explode(' ',$list[$k]['day']);
				$temp2  = explode('-',$temp[0]);
				$list[$k]['day']  = $temp2[0].'月'.$temp2[1].'日'; 
				
				
				$c = intval($v['conversion_value']);
				
				$list[$k]['conversion_value'] = $v['conversion_value'];
				/*$list[$k]['conversion_value'] = $c;
				if($c > 100){
					$list[$k]['conversion_value'] =$c/10;
				}*/
			}
		}
		echo json_encode($list);
	}
	
	public function top5Keywords(){
		$cid = $_GET['cid'];
		$d1 = $_GET['d1'];
		$d2 = $_GET['d2'];
		$limit = intval($_GET['limit']);
		if($limit > 0){
			$order = ' LIMIT 0 ,'.$limit;
		}else{
			$order = '';
		}
		//$list = M('reportkeywords','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' ")-> field('COST,CLICKS,CRITERIA,AVERAGE_POSITION')->order('COST DESC')->limit(0,5)->select();
		
		if(isset($d2)){
			$sql = " SELECT SUM(COST) AS COST , SUM(CLICKS) AS CLICKS ,AVG(AVERAGE_POSITION) AS AVERAGE_POSITION , CRITERIA  ,Day FROM `aw_reportkeywords` WHERE `ACCOUNT_ID` = {$cid} AND Day BETWEEN '{$d1}' AND '{$d2}' AND NETWORK = 'Search Network' GROUP BY CRITERIA  ORDER BY COST DESC".$order;
		}else{
			$sql = " SELECT SUM(COST) AS COST , SUM(CLICKS) AS CLICKS ,AVG(AVERAGE_POSITION) AS AVERAGE_POSITION , CRITERIA  ,Day FROM `aw_reportkeywords` WHERE `ACCOUNT_ID` = {$cid} AND Day  LIKE  '%{$d1}%'  AND NETWORK = 'Search Network' GROUP BY CRITERIA  ORDER BY COST DESC".$order;
		}
		
	
		
		$list = M('reportkeywords','aw_',$this->connectReports)->query($sql);
		
		if(count($list) != 0){
			foreach($list as $k=>$v){
				$list[$k]['average_position'] = sprintf("%.1f",$v['average_position']);
			}
			
		}
		
		//echo $sql;
		//exit;
		$list = is_arrays($list);
		echo json_encode($list);
	}
	
	
	
	public function geo1(){
		$cid = $_GET['cid'];
		$d1 = $_GET['d1'];
		$d2 = $_GET['d2'];
		$limit = intval($_GET['limit']);
		if($limit > 0){
			$order = ' LIMIT 0 ,'.$limit;
		}else{
			$order = '';
		}
		
		
		//$list = M('reportgeo','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' ")-> field('COST,IMPRESSIONS,CLICKS,COUNTRY_CRITERIA_ID')->order('COST DESC')->limit(0,5)->select(); ///地区花费
		if(isset($d2)){
			$sql = "SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS ,SUM(CLICKS) AS CLICKS , COUNTRY_CRITERIA_ID FROM `aw_reportgeo` WHERE `ACCOUNT_ID` = {$cid}  AND Day BETWEEN '{$d1}' AND '{$d2}' GROUP BY COUNTRY_CRITERIA_ID ORDER BY COST DESC".$order;
		}else{
			$sql = "SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS ,SUM(CLICKS) AS CLICKS , COUNTRY_CRITERIA_ID FROM `aw_reportgeo` WHERE `ACCOUNT_ID` = {$cid}  AND Day LIKE '%{$d1}%' GROUP BY COUNTRY_CRITERIA_ID ORDER BY COST DESC".$order;
		}
		
		
	
	$list = M('reportgeo','aw_',$this->connectReports)->query($sql);
	
		$list = is_arrays($list);
		echo json_encode($list);
	}
	
	public function geo2(){
		$cid = $_GET['cid'];
		$d1 = $_GET['d1'];
		$d2 = $_GET['d2'];
		$limit = intval($_GET['limit']);
		if($limit > 0){
			$order = ' LIMIT 0 ,'.$limit;
		}else{
			$order = '';
		}
		//$list =  M('reportgeo','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}'")-> field('COST,IMPRESSIONS,CLICKS,CONVERSION_VALUE,COUNTRY_CRITERIA_ID')->order('COST DESC')->limit(0,5)->select(); ///地区转换
		
		if(isset($d2)){
		
			$sql = "SELECT SUM(COST) AS COST , SUM(CLICKS) AS CLICKS ,SUM(ALL_CONVERSIONS) AS CONVERSION_VALUE , COUNTRY_CRITERIA_ID FROM `aw_reportgeo` WHERE `ACCOUNT_ID` = {$cid}  AND Day BETWEEN '{$d1}' AND '{$d2}' GROUP BY COUNTRY_CRITERIA_ID ORDER BY COST DESC".$order;
	
		}else{
			
			$sql = "SELECT SUM(COST) AS COST , SUM(CLICKS) AS CLICKS ,SUM(ALL_CONVERSIONS) AS CONVERSION_VALUE , COUNTRY_CRITERIA_ID FROM `aw_reportgeo` WHERE `ACCOUNT_ID` = {$cid}  AND Day LIKE '%{$d1}%' GROUP BY COUNTRY_CRITERIA_ID ORDER BY COST DESC".$order;
			
			
		}
	$list = M('reportgeo','aw_',$this->connectReports)->query($sql);
	
		$list = is_arrays($list);
		if(count($list) != 0){
			foreach($list as $k=>$v){
				$c = intval($v['conversion_value']);
				
				$list[$k]['conversion_value'] = $v['conversion_value'];
				
				/*if($c > 10){
					$list[$k]['conversion_value'] =$c/10;
				}*/
				
				//$list[$k]['conversion_value'] = intval($v['conversion_value']);
			}
		}
		$list = is_arrays($list);
		echo json_encode($list);
	}
	
	public function blance(){
		
		$day2  = date('Y-m-d',time());
		$cid = $_GET['cid'];
		//$cid = cut($cid);
		
		$rsa =  M('budget','ads_',$this->connectReports)->where("cid = '{$cid}'")-> find();
		
		//print_r($rsa);
		//exit;
		
		//$day1= $rsa['start_date'];
		$day1= str_replace('','',$rsa['start_date']);
		/*$sql= "SELECT SUM(COST) AS COST FROM `aw_reportaccount`  WHERE ACCOUNT_ID = {$cid} AND DAY BETWEEN '{$day1}' AND '{$day2}'";
		$rs2 = M('',NULL,$this->connectReports)->query($sql);
		
		$blance = $rsa['budget'] - $rs2[0]['cost'];
		$blance = ($blance<0)?0:$blance;*/
		
		
			$sql = "SELECT SUM(COST) AS COST FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$day1}' AND '{$day2}'";
		
			$rs2 = M('',NULL,$this->connectReports)->query($sql);
		
		
		
		$blance = sprintf("%.2f",$rsa['budget'] - $rs2[0]['cost']);
		
		
		$blance = ($blance<=0)?0:$blance;
		
		
		
		//echo $rsa['budget'].'---'.$rs2[0]['cost'];
		//exit;
		
		$days1 = date("Y-m-d",strtotime("-0 day"));
		$days7 = date("Y-m-d",strtotime("-7 day"));
		
		//$cost = M('reportcampaign','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}'")->order('Day DESC')->limit(0,7)->avg('COST');
		$cost = M('reportcampaign','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$days7}' AND '{$days1}'")->SUM('COST');
		
		$cost = sprintf("%.2f",$cost / 7) ;
		$day = floor($blance/$cost);
		$day = ($day<=0)?0:$day;
		
		
		if($rsa['id']){
			//$day = ceil($blance/$cost);
			//$day = ($day<0)?0:$day;
			echo "[{$blance},{$day}]";	
			//
		}else{
			echo '[0,0]';	
		}
		
		/*if($rs['id']){
			echo '['.$rs['blance'].']';	
		}else{
			echo '[0]';
		}	*/
	}
	
	
}