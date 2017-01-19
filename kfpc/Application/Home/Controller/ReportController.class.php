<?php
namespace Home\Controller;
use Think\Controller;

//mcc账户控制器
class ReportController extends Controller {
	//protected $connectReports = 'mysql://ads:jHfFUNJdXxjYKrCB@42.200.33.115/awreports#utf8';
	
	protected $connectReports;
	
	
	 public function _initialize(){
		 $this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
	 }
	
	
	
	
	//获取账户余额
	public function index(){
		$bday =  date('Ymd',strtotime("-1 day"));
		$day1= $_GET['day1'];
		$day2  = date('Ymd',time());
		$cid = $_GET['cid'];
		//$sql= "SELECT SUM(COST) AS COST FROM `aw_reportaccount`  WHERE ACCOUNT_ID = {$cid} AND DAY BETWEEN '{$day1}' AND '{$day2}'";
		
		$sql = "SELECT SUM(COST) AS COST FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$day1}' AND '{$day2}' AND CAMPAIGN_STATUS  = 'enabled'";
		
		$rs = M('',NULL,$this->connectReports)->query($sql);
		$c = M('aw_reportcampaign',NULL,$this->connectReports)->where("`ACCOUNT_ID` = {$cid} AND CAMPAIGN_STATUS = 'enabled' AND DAY = '{$bday} 20:00:00.000000'")->count();
		
		echo '['.$cid.','.$rs[0]['cost'].','.$c.']';
	}
	
	public function getAccountCost(){
		$date =  $_GET['date'];
		
		
		
		//$d = explode('-',$date);
		
		//if($d[0] == $d[1]){
				
		//}
		
	}
	
	public function quickName(){
		//$sql = "SELECT SUM(COST) as SUM_COST , ACCOUNT_ID FROM aw_reportcampaign  WHERE Day BETWEEN  '{$bday}'  AND '{$eday}'  GROUP BY ACCOUNT_ID";		
		$list = M('ads_mcc',NULL,$this->connectReports)->where('id > 0')->field('account_id,name')->select();
		
		$arr = each_array($list,'account_id','name');
		
		echo json_encode($arr);
		//print_r($arr);
	}
	
	
	//olsa日报进度
	public function olsaSchedule(){
		//进度1公式，季度初到昨日的天数 / 整个季度的天数
			$theDaysArr = array();
			$allDays = 0 ;
			$theDays = 0;
			$month = date('m',time());
			$yday = date('d', strtotime('-1 day'));
			
			$eday = date('Y-m-d', strtotime('-0 day'));
			
			
			if($yday[0] == 0){
				$yday = $yday[1];
			}
			$year = date('Y',time());
			$quarter = array();
			for($i = 1 ; $i <= 12 ;$i++){
				if($i % 3 == 0){
					//array_push($quarter,$i-2);	
					$quarter[($i-2).''] = array($i-2,$i-1,$i);
				}
			}
			if($month[0] == 0){
				$month = $month[1];
			}
			array_push($theDaysArr,$month);
			foreach($quarter as $k=> $v){
				if(in_array($month,$v)){
					$now = $k.'';
					break;	
				}
			}
			foreach($quarter[$now] as $v){
				$allDays+=getMonthLastDay($v,$year);
			}
			$so = array_diff($quarter[$now],$theDaysArr);
			foreach($so as $v){
				$theDays+=getMonthLastDay($v,$year);
			}
			$theDays+=$yday;
			$arr[0] = sprintf("%.2f",($yday/$allDays) * 100);
			
			if($now[0]!=0){
				$bday = $year.'-'.'0'.$now.'-01';
			}else{
				$bday = $year.'-'.$now.'-01';
			}
			
			// SELECT SUM(COST) AS COST FROM WHERE ACCOUNT_ID IN (SELECT account FROM ads_olsa)
			
			$rs = M('',NULL,$this->connectReports)->query("SELECT SUM(COST) AS COST FROM aw_reportcampaign WHERE ACCOUNT_ID IN (SELECT account FROM ads_olsa) AND Day Between '{$bday}' AND '{$eday}'");
			
			 $arr[1] = sprintf("%.2f",($rs[0]['cost'] / 2200000) * 100);
			
			echo json_encode($arr);
	}
	
	public function bigAccount(){
		//季度
		$month = date('m',time());
		$year = date('Y',time());
		$eday = date('Y-m-d', strtotime('-1 day'));
		
		$quarter = array();
		for($i = 1 ; $i <= 12 ;$i++){
			if($i % 3 == 0){
				//array_push($quarter,$i-2);	
				$quarter[($i-2).''] = array($i-2,$i-1,$i);
			}
		}
		
		if($month[0] == 0){
			$month = $month[1];
		}
		foreach($quarter as $k=> $v){
			if(in_array($month,$v)){
				$now = $k.'';
				break;	
			}
		}
		if($now[0]!=0){
			$bday = $year.'-'.'0'.$now.'-01';
		}else{
			$bday = $year.'-'.$now.'-01';
		}
		$sql = "SELECT NEW.ACCOUNT_ID FROM (
		
		SELECT SUM(COST) as SUM_COST , ACCOUNT_ID FROM aw_reportcampaign  WHERE Day BETWEEN  '{$bday}'  AND '{$eday}'  GROUP BY ACCOUNT_ID
		
		) AS NEW WHERE NEW.SUM_COST > 90000";		
		
		$allAccount = array();
		
		$list = M('',NULL,$this->connectReports)->query($sql);
		
		echo json_encode($list);	
	}
	
	public function getHasCostAccount(){
		$day =  $_GET['day'];
		$allAccount = array();
		/*if($type == 1){
			$d1 = date('Y-m-d', strtotime('-30 day'));
			$d2 = date('Y-m-d', strtotime('-1 day'));
				
			$sql = "SELECT NEW.ACCOUNT_ID FROM (	
				SELECT SUM(COST) as SUM_COST , ACCOUNT_ID FROM aw_reportaccount  WHERE Day BETWEEN  '{$d1}' AND '{$d2}'  GROUP BY ACCOUNT_ID ) AS NEW WHERE
				NEW.SUM_COST > 0";
			
			$allAccount = array();
			$list = M('',NULL,$this->connectReports)->query($sql);
			foreach($list as $v){
				array_push($allAccount,$v['account_id']);
			}
			echo json_encode($allAccount);
		
		}else if($type == 1){*/
		if($day == 'yesterday'){
			
			$d1 = date('Y-m-d', strtotime('-1 day'));
			
			
			
			//$allAccount = array();
			//$list = M('',NULL,$this->connectReports)->query($sql);
			//echo json_encode($list);

			
		}else{
			
			$d1 = date('Y-m-d', strtotime('-2 day'));
			
			
		}
		
		$sql = "SELECT NEW.ACCOUNT_ID ,NEW.SUM_COST FROM (
			
			SELECT SUM(COST) as SUM_COST , ACCOUNT_ID FROM aw_reportcampaign  WHERE Day LIKE  '%{$d1}%'   GROUP BY ACCOUNT_ID
			
			) AS NEW WHERE NEW.SUM_COST > 0 ";			
			
			$allAccount = array();
			$list = M('',NULL,$this->connectReports)->query($sql);
			echo json_encode($list);
	}
	
	
	public function statusCam(){
		
		$cid = $_GET['cid'];
		;
		
		echo $c;
	}
	
}