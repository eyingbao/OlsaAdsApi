<?php
namespace Home\Controller;
use Think\Controller;

//广告组系列控制器
class AlertedController extends Controller {
	protected $domain;
	protected $connectReports2;
	public function _initialize(){
		$this->domain = C('DOMAIN_PC');
		$this->connectReports2 = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
		//parent::_initialize();
	}
	public function index(){
		$result = '[]';
		$type = $_POST['val'];
		$cid = str_replace('-','',$_POST['cid']);
		$date = get_date_list();
		$d7a[0] = date("Y-m-d",strtotime("-7 day"));
		$d7a[1] = date("Y-m-d",time());
		$d7 =  str_replace('-',',',$date[3]['value']);
		switch($type){
			case 'xftx': //续费提醒
				$day2  = date('Y-m-d',time());
				$rsa =  M('ads_budget',NULL,$this->connectReports2)->where("cid = '{$cid}'")-> find();
				$day1= str_replace('','',$rsa['start_date']);
				$sql= "SELECT SUM(COST) AS COST FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = {$cid} AND DAY BETWEEN '{$day1}' AND '{$day2}'";
				$blance = $rsa['bce'];
				$blance = ($blance<=0)?0:$blance;
				$cost = M('reportaccount','aw_',$this->connectReports2)->where("ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d7a[0]}' AND '{$d7a[1]}'")->avg('COST');
				$cost = sprintf("%.2f",$cost);
				if($rsa['id']){
					$day = floor($blance/$cost);
					$day = ($day<=0)?0:$day;
					$result =  "[{$cost},{$blance},{$day}]";
				}
			break;
			
			case 'tfqd': //投放渠道
				$d2 = date("Y-m-d",strtotime("-1 day"));
			  	$d1 = date("Y-m-d",strtotime("-7 day"));
			  
			  	//pc搜索
				$sql = "SELECT COUNT(*) AS C FROM aw_reportcriteria 
WHERE NETWORK = 'Search Network' AND DEVICE = 'Computers' AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1}' AND '{$d2}'";
				$rs1 = M('','',$this->connectReports2)->query($sql);
			 	$arr[0] = ($rs1[0]['c'] > 0)?1:0;
				
				
			  	//pc展示
			   $sql = "SELECT COUNT(*) AS C FROM aw_reportcriteria 
WHERE NETWORK = 'Display Network' AND DEVICE = 'Computers' AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1}' AND '{$d2}'";
			   $rs1 = M('','',$this->connectReports2)->query($sql);
			    $arr[1] = ($rs1[0]['c'] > 0)?1:0;
			  
			   //移动搜索
			   $sql = "SELECT COUNT(*) AS C FROM aw_reportcriteria 
WHERE NETWORK = 'Search Network' AND DEVICE = 'Mobile devices with full browsers' AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1}' AND '{$d2}'";
			   $rs1 = M('','',$this->connectReports2)->query($sql);
			   $arr[2] = ($rs1[0]['c'] > 0)?1:0;
			   
			   
			  //移动展示
			   $sql = "SELECT COUNT(*) AS C FROM aw_reportcriteria 
WHERE NETWORK = 'Display Network' AND DEVICE = 'Mobile devices with full browsers' AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1}' AND '{$d2}'";
			  $rs1 = M('','',$this->connectReports2)->query($sql);
			  $arr[3] = ($rs1[0]['c'] > 0)?1:0;
			  
			  
			  $sql = "SELECT   COUNT(*) AS C FROM aw_reportcriteria  WHERE   CRITERIA_TYPE = 'User List' AND COST > 0 AND  
			  DEVICE ='Computers' AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1}' AND '{$d2}'";
			   
			   
			  $rs1 = M('','',$this->connectReports2)->query($sql);
			  $arr[4] = ($rs1[0]['c'] > 0)?1:0;
			  $result =  json_encode($arr);
			  //再营销
			  
			
				
			break;
			
			case 'ysb': //系列消耗预算比X异常 (X>90% OR X < 60%)
			//	$sql = "SELECT COST FROM aw_reportcampaign "
			
				$d2 = date("Y-m-d",strtotime("-1 day"));
				$d1 = date("Y-m-d",strtotime("-7 day"));
				
				$sql = " SELECT * FROM (SELECT ROUND(AVG(COST),2) AS COST ,ROUND(AVG(BUDGET),2) AS BUDGET , CAMPAIGN_NAME  , ROUND(AVG(COST) / AVG(BUDGET),2)*100 AS per FROM aw_reportcampaign WHERE DAY between '{$d1}' AND '{$d2}' AND ACCOUNT_ID = '{$cid}' GROUP BY CAMPAIGN_NAME) AS NEW WHERE NEW.per > 90 OR NEW.per < 60";
				$list = M('','',$this->connectReports2)->query($sql);
				$result =  json_encode($list);
				//pr(array($list));
				//echo $d1.'-'.$d2;
			
			break;
			
			case 'rcz': //系列消耗日差值 Y 异常 (Y > 20)
				$d1 = date("Y-m-d",strtotime("-1 day"));
				$d2 = date("Y-m-d",strtotime("-2 day"));
			
				$sql = "SELECT SUM(COST) AS COST, CAMPAIGN_NAME FROM aw_reportcampaign WHERE DAY LIKE '%{$d1}%' AND ACCOUNT_ID = '{$cid}' GROUP BY CAMPAIGN_NAME";
			//	echo $sql;
				//exit;
				$list = M('','',$this->connectReports2)->query($sql);
				
				$sql2 = "SELECT SUM(COST) AS COST, CAMPAIGN_NAME FROM aw_reportcampaign WHERE DAY LIKE '%{$d2}%' AND ACCOUNT_ID = '{$cid}' GROUP BY CAMPAIGN_NAME";
				
				$list2 = M('','',$this->connectReports2)->query($sql2);
				
				$arr = array();
				$arr1 = array();
				$arr2 = array();
				foreach($list as $k=>$v){
					array_push($arr,$v['campaign_name']);
					$arr1[$v['campaign_name']] = $v['cost'];
				}
				foreach($list2 as $k=>$v){
				
					$arr2[$v['campaign_name']] = $v['cost'];
				}
				$final = array();
				foreach($arr as $k=>$v){
					
					$y1 = $arr1[$v];
					$y2 = $arr2[$v];
					$y = $y1 -$y2;
					if($y > 50 || $y < -50){
						array_push($final,array($v,$y1,$y2,$y));
					}
					
				}
				
				//pr(array($final));
				$result =  json_encode($final);
				
			break;
			case 'groupctr': //广告组CTR <1% OR >3%
					$d2 = date("Y-m-d",strtotime("-1 day"));
					$d1 = date("Y-m-d",strtotime("-7 day"));
				
					//$sql = " SELECT NEW.CTR,NEW.ADGROUP_NAME,rp.CAMPAIGN_NAME FROM (SELECT CTR , ADGROUP_NAME ,CAMPAIGN_ID  FROM aw_reportadgroup WHERE DAY between '{$d1}' AND '{$d2}' AND NETWORK = 'Search Network' AND ACCOUNT_ID = '{$cid}' AND IMPRESSIONS > 500 AND (CTR > 0.05 OR CTR < 0.01)) AS NEW LEFT JOIN aw_reportcampaign AS rp ON rp.CAMPAIGN_ID = NEW.CAMPAIGN_ID";
					
					$sql = "SELECT 

NEW.CTR,NEW.ADGROUP_NAME,rp.CAMPAIGN_NAME 

FROM (


SELECT CTR , ADGROUP_NAME ,CAMPAIGN_ID FROM aw_reportadgroup WHERE DAY between '{$d1}' AND '{$d2}' AND NETWORK = 'Search Network' AND ACCOUNT_ID = '{$cid}' AND IMPRESSIONS > 500 AND (CTR > 0.05 OR CTR < 0.01)

) AS NEW 


INNER JOIN 

(SELECT DISTINCT CAMPAIGN_ID, CAMPAIGN_NAME FROM aw_reportcampaign WHERE ACCOUNT_ID = '{$cid}') 
AS rp ON rp.CAMPAIGN_ID = NEW.CAMPAIGN_ID";
					
					
					
					//echo $sql;
					//exit;
					
					$list = M('','',$this->connectReports2)->query($sql);
					
					$result =  json_encode($list);
					
			break;
			
			case 'adcount': //广告语数量 X (X<1)
				$d1 = date("Y-m-d",strtotime("-1 day"));
				
				//$sql = "SELECT   ADGROUP_NAME , CAMPAIGN_NAME , HEADLINE , CTR FROM aw_reportad WHERE  STATUS = 'enabled'  AND  CREATIVE_APPROVAL_STATUS != 'disapproved' AND  NETWORK = 'Search Network' AND DAY LIKE '%{$d1}%' AND ACCOUNT_ID = '{$cid}'";
				
				//echo $sql;
				
				$sql = "SELECT NEW.ADGROUP_NAME , NEW.CAMPAIGN_NAME ,NEW.c FROM (SELECT ADGROUP_NAME , CAMPAIGN_NAME , HEADLINE ,COUNT(*) AS c FROM aw_reportad WHERE STATUS = 'enabled' AND CREATIVE_APPROVAL_STATUS != 'disapproved' AND NETWORK = 'Search Network' AND DAY LIKE '%{$d1}%' AND ACCOUNT_ID = '{$cid}' GROUP BY HEADLINE) AS NEW WHERE NEW.c = 1";
				
				$list = M('','',$this->connectReports2)->query($sql);
				$result =  json_encode($list);
			
			break;
			
			case 'adctr': //广告语CTR
				$d2 = date("Y-m-d",strtotime("-1 day"));
				$d1 = date("Y-m-d",strtotime("-14 day"));
				$sql = "SELECT ADGROUP_NAME, CAMPAIGN_NAME ,HEADLINE FROM aw_reportad WHERE STATUS = 'enabled' AND CREATIVE_APPROVAL_STATUS != 'disapproved' AND NETWORK = 'Search Network' AND DAY between '{$d1}' AND '{$d2}' AND CTR < 1 AND IMPRESSIONS > 1000 AND ACCOUNT_ID = '{$cid}' ";
				$list = M('','',$this->connectReports2)->query($sql);
				$result =  json_encode($list);
			break;
			
			case 'kwzl':  //关键词质量
				$d1 = date("Y-m-d",strtotime("-1 day"));
				//QUALITY_SCORE
				$sql = "SELECT ADGROUP_NAME, CAMPAIGN_NAME ,CRITERIA ,QUALITY_SCORE FROM aw_reportkeywords WHERE DAY LIKE '%{$d1}%' AND ACCOUNT_ID = '{$cid}' AND  NETWORK = 'Search Network' AND STATUS = 'enabled' AND QUALITY_SCORE < 4";
				$list = M('','',$this->connectReports2)->query($sql);
				$result =  json_encode($list);
			
			break;
			case 'ggyc': //广告异常
				$d1 = date("Y-m-d",strtotime("-1 day"));
				$sql = "SELECT ADGROUP_NAME, CAMPAIGN_NAME ,HEADLINE FROM aw_reportad WHERE STATUS = 'enabled' AND CREATIVE_APPROVAL_STATUS = 'disapproved' AND NETWORK = 'Search Network' AND DAY LIKE '%{$d1}%'";
				
			//	$sql = "SELECT ADGROUP_NAME, CAMPAIGN_NAME ,HEADLINE FROM aw_reportad WHERE STATUS = 'enabled' AND CREATIVE_APPROVAL_STATUS = 'disapproved' AND NETWORK = 'Search Network'";
				
				$list = M('','',$this->connectReports2)->query($sql);
				$result =  json_encode($list);
			break;
			
			case 'kwsl': //关键词数量
				$d2 = date("Y-m-d",strtotime("-1 day"));
				$d1 = date("Y-m-d",strtotime("-7 day"));
				
				$sql = "
				SELECT * FROM (
				
				SELECT ADGROUP_NAME, CAMPAIGN_NAME ,COUNT(CRITERIA) AS C  FROM aw_reportkeywords WHERE DAY between '{$d1}' AND '{$d2}' AND ACCOUNT_ID = '{$cid}' AND  NETWORK = 'Search Network' AND STATUS = 'enabled'  GROUP BY  CRITERIA 
				
				) AS new WHERE new.c < 10
				
				
				";
				
				$list = M('','',$this->connectReports2)->query($sql);
				$result =  json_encode($list);
			
			break;
		}
		echo $result;
	}
	
	
	
	public function getFinfo(){

		$cid = str_replace('-','',$_POST['cid']);
		
		$arr['cname'] = M('ads_mcc',NULL,$this->connectReports2)->where("customerId = '{$_POST[cid]}'")->getField('name');
		
		//计算过去30天
		$d30 = date("Y-m-d",strtotime("-30 day"));
		$d1= date("Y-m-d",strtotime("-1 day"));
		$arr['dt1'] = array();
		$arr['dt2'] = array();
		$sql = "SELECT SUM(COST) AS COST , DAY FROM aw_reportcampaign WHERE ACCOUNT_ID = '{$cid}' AND DAY between '{$d30}' AND '{$d1}' GROUP BY DAY";
		//echo $sql;
		//exit;
		
		$list = M('aw_reportcampaign',NULL,$this->connectReports2)->query($sql);
		foreach($list as $k=>$v){
			$day = substr($v['day'],5,5);
			array_push($arr['dt1'],$day);
			array_push($arr['dt2'],$v['cost']);
		}
		$arr['list'] = $list;
		$result =  json_encode($arr);
		
		
		/*
		<table class="table table-bordered table-striped no-m" id="budget_info" style=""><thead><tr><th style="text-align:center">近7天日均消耗</th><th style="text-align:center">余额</th><th style="text-align:center">可消耗天数</th></tr></thead><tbody><tr><td style="text-align:center;color:">￥672.71</td><td style="text-align:center">￥12308.27</td><td style="text-align:center;color:">19</td></tr></tbody></table>
		*/
		
		
		echo $result;
	}
	
	
	public function suggest(){
		if(IS_POST){
			$cid = str_replace('-','',$_POST['cid']);
		}else{
			$cid = str_replace('-','',$_GET['cid']);
		}
		$request = array(
			'costErr',//消耗异常
			'renewals',//续费提醒
			'budgetErr',
			'delivery', //广告系列投放渠道
			'campaignCtr',
			'kwPoint',
			'kwCount',
			'ref',
			'adCount',
			'BounceRate', //跳出率
			'Loadtime', //加载时间
			'Dwelltime', //停留时间
			'convers',  //转化次数对比
			'crmServerCount'
		);
		$list = array();
		foreach($request  as $v){
			array_push($list,$this->domain.'/Ads/Suggest/'.$v.'?cid='.$cid);
		}
		//pr(array($list));
		$arr = async_get_url($list);
		
		
		$f = array();
		foreach($arr as $k =>$v){
			$f[$request[$k]] = json_decode($v,true);
		}
		if(IS_POST){
			echo json_encode($f,JSON_UNESCAPED_UNICODE);
		}else{
			$m = array();
			$p = array();
			foreach($f as $k=>$v2){
				$m[$k] = $v2['range2'];
				
				array_push($p,$v2['point2']);
			}
			$p = array_sum($p);
			$m['point'] = $p;
			$m['time'] = time();
			$m['date'] = date('Y-m-d H:i:s',time());
			$m['account_id'] =$cid;
			$m['cid'] =$_GET['cid'];
			//$final['list']
			echo json_encode($m,JSON_UNESCAPED_UNICODE);
		}
		
	}
	
	public function tt(){
		
			
		
	}
	
	
	public function getList(){
		$cid = str_replace('-','',$_GET['cid']);
		$request = array(
			'converAndGa',//转化工具及GA 0
			'costErr',//消耗波动 1
			'renewals',//续费提醒 2
			'budgetErr', //日预算使用率 3
 			'delivery', //广告系列投放渠道 4
			'adCtr', //广告语ctr 5
			'adCount', //6
			'kwCtr', //7
			'kwPoint', //质量得分 <3 占比 8
			'kwCount', //9
			'ref', //10
			'BounceRate', //跳出率
			'Loadtime', //加载时间
			'Dwelltime', //停留时间
		);
		$list = array();
		foreach($request  as $v){
			array_push($list,$this->domain.'/Ads/Suggest/'.$v.'?cid='.$cid);
		}
		$arr = async_get_url($list);
		
		foreach($arr as $k=>$v){
			$ar[$k] = json_decode($v,true);
		
		}
		
		$check = array();
		
		foreach($ar as $k=>$v){
			if($k == 0 ){   //转化工具及GA
				
				$check['gac'] = 0;
			
			}else if($k == 1 ){  // 消耗波动
				
				
			}else if($k == 2 ){ //剩余天数
				
				
			}else if($k == 3 ){ //日预算使用率
				
			}else if($k == 4 ){ //使用再营销 //delivery	
			
			}else if($k == 5 ){  //广告语ctr异常
			
			}else if($k == 6 ){  //广告语条数
				
			}else if($k == 8 ){  //质量得分
			
			}else if($k == 9 ){  //关键词数量
			
			}else if($k == 10){  //是否拒登
				
				
			}
			
			
		}
		
		
		
		echo json_encode($ar);
		//pr(array($arr));
		
	}
	
	
}