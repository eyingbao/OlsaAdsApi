<?php
namespace Ads\Controller;
use Think\Controller;
//报表控制器
class IndexController extends Controller {
	protected $d1;
	protected $d2;
	protected $cid;
	//protected $connectCrm;
	public function _initialize(){
		$this->d1 = $_GET['d1'];
		$this->d2 = $_GET['d2'];
		$this->cid = $_GET['cid'];
		$this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
		//$this->connectCrm = 'mysql://'.C('DB_USER_CRM').':'.C('DB_PWD_CRM').'@'.C('DB_HOST_CRM').'/'.C('DB_NAME_CRM').'#utf8';
	}
	
	public function top(){
		$sql = "SELECT  SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS, SUM(CLICKS) AS CLICKS  FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid}  AND DAY BETWEEN '{$this->d1}' AND '{$this->d2}'";
		
		
		
		$rs = M('reportcampaign','aw_',$this->connectReports)->query($sql);
		echo json_encode($rs[0]);
	}
	
	//广告系列报告
	public function campaign(){
		
		$sql1 = "SELECT 
						CAMPAIGN_NAME,
						SUM(COST) AS COST
						FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY  
				BETWEEN '{$this->d1}' AND '{$this->d2}'  GROUP BY CAMPAIGN_ID ORDER BY COST DESC LIMIT 0 ,7";
		$histogramList = M('reportcampaign','aw_',$this->connectReports)->query($sql1);
		
		$sql2 = "SELECT 
						CAMPAIGN_NAME,
						ROUND(SUM(ALL_CONVERSIONS)) AS ALL_CONVERSIONS
			
						FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY  
				BETWEEN '{$this->d1}' AND '{$this->d2}'  GROUP BY CAMPAIGN_ID ORDER BY ALL_CONVERSIONS DESC LIMIT 0 ,7";
		$pieList = M('reportcampaign','aw_',$this->connectReports)->query($sql2);
		//enabled
		//removed
		$sql3 = "SELECT  
						
						case CAMPAIGN_STATUS
						
						 when 'paused' then '已暂停'  
      				     
						  when 'removed' then '已移除'
						  
						   else '已启用' end as CAMPAIGN_STATUS,
					
					CAMPAIGN_NAME,
					BUDGET,
					SUM(CLICKS) AS CLICKS,
					SUM(IMPRESSIONS) AS IMPRESSIONS,
					if(
						FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
						FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
					) AS CTR, 				
				if(
					FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
					0,
					FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
				) AS AVERAGE_CPC,
				if(
				FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2) is null,
				0 ,
				FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2)
				
				)  AS COST_PER_ALL_CONVERSION,
				
				if(
				FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS),2) is null,
				0 ,
				FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS) * 100,2)
				
				)  AS ALL_CONVERSION_RATE,	
				
				SUM(COST) AS COST,
				FORMAT(AVG(AVERAGE_POSITION),1) AS AVERAGE_POSITION,
				ROUND(SUM(ALL_CONVERSIONS)) AS ALL_CONVERSIONS
				
				FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY  
				BETWEEN '{$this->d1}' AND '{$this->d2}'  GROUP BY CAMPAIGN_ID ORDER BY COST DESC LIMIT 0 ,20";
				
				
				
		$tableList = M('reportcampaign','aw_',$this->connectReports)->query($sql3);
		
		$sql4 = "SELECT 
					SUM(new.BUDGET) AS BUDGET,
					SUM(new.CLICKS) AS CLICKS,
					SUM(new.IMPRESSIONS) AS IMPRESSIONS,
				
					if(
						FORMAT(  SUM(new.CLICKS) / SUM(new.IMPRESSIONS) * 100  ,2) is null,0,
						FORMAT(  SUM(new.CLICKS) / SUM(new.IMPRESSIONS) * 100  ,2)
					) AS CTR, 
				
					
					if(
						FORMAT(  SUM(new.COST) / SUM(new.CLICKS) ,2) is null,
						0,
						FORMAT(  SUM(new.COST) / SUM(new.CLICKS) ,2)
					) AS AVERAGE_CPC,
					
					
					SUM(new.COST) AS COST,
					FORMAT(AVG(new.AVERAGE_POSITION),1) AS AVERAGE_POSITION,
					
						if(FORMAT(SUM(new.COST) / SUM(new.ALL_CONVERSIONS),2) is null,0 ,FORMAT(SUM(new.COST) / SUM(new.ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION,
					
					
					ROUND(SUM(new.ALL_CONVERSIONS)) AS ALL_CONVERSIONS,
					
					if(
					FORMAT(SUM(new.ALL_CONVERSIONS) / SUM(new.CLICKS),2) is null,
					0 ,
					FORMAT(SUM(new.ALL_CONVERSIONS) / SUM(new.CLICKS) * 100,2)
					
					)  AS ALL_CONVERSION_RATE
					
					
					FROM 
					
					(SELECT 
					
					CAMPAIGN_STATUS,
					
					CAMPAIGN_NAME,
					BUDGET,
					SUM(CLICKS) AS CLICKS,
					
					SUM(IMPRESSIONS) AS IMPRESSIONS,
					
					
					
					
					if(
						FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
						0,
						FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
					) AS AVERAGE_CPC,
					
					
					
					SUM(COST) AS COST,
					
					
					FORMAT(AVG(AVERAGE_POSITION),1) AS AVERAGE_POSITION,
					
					
					ROUND(SUM(ALL_CONVERSIONS)) AS ALL_CONVERSIONS,
					
					
					if(FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2) is null,0 ,FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION,
					
					if(
					FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS),2) is null,
					0 ,
					FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS) * 100,2)
					
					)  AS ALL_CONVERSION_RATE
					
					
					FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY 
					
					
					
					BETWEEN '{$this->d1}' AND '{$this->d2}' GROUP BY CAMPAIGN_ID ORDER BY COST DESC LIMIT 0 ,20) AS new";
					
					
					
		
		$total = M('reportcampaign','aw_',$this->connectReports)->query($sql4);
		$list = array(
			'histogramList'=>$histogramList,
			'pieList'=>$pieList,
			'table'=>$tableList,
			'total'=>$total[0] 
		);
		echo json_encode($list);
	}
	
	
	
	
	//每日报告
	public function daily(){
		$sql = "SELECT 
			left(DAY,10) AS DAY,
			SUM(CLICKS) AS CLICKS,
			SUM(IMPRESSIONS) AS IMPRESSIONS,
			
			if(
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
) AS CTR, 
			
			if(
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
	0,
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
) AS AVERAGE_CPC,

			
			
			SUM(COST) AS COST,
			FORMAT(AVG(AVERAGE_POSITION),1) AS AVERAGE_POSITION,
			ROUND(SUM(ALL_CONVERSIONS)) AS ALL_CONVERSIONS,
		
			if(FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2) is null,0 ,FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION,
			
			
			if(
	FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS),2) is null,
	0 ,
	FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS) * 100,2)
)  AS ALL_CONVERSION_RATE
			
			
			FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY 
			BETWEEN '{$this->d1}' AND '{$this->d2}' GROUP BY DAY ORDER BY DAY ASC";
		$tableList = M('reportcampaign','aw_',$this->connectReports)->query($sql);
		
		$sql2 = "SELECT 
			SUM(CLICKS) AS CLICKS,
			
			SUM(IMPRESSIONS) AS IMPRESSIONS,
			
			if(
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
) AS CTR, 
			
			SUM(COST) AS COST,
			
			if(
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
	0,
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
) AS AVERAGE_CPC,
			
			FORMAT(AVG(COST),2) AS AVG_COST, 
			
			FORMAT(AVG(AVERAGE_POSITION),1) AS AVERAGE_POSITION,
			
			ROUND(SUM(ALL_CONVERSIONS)) AS ALL_CONVERSIONS,
		
		
			if(FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2) is null,0 ,FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION,
			
			
			
			if(
			FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS),2) is null,
			0 ,
			FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS) * 100,2)
		)  AS ALL_CONVERSION_RATE
			
			FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$this->cid}  AND DAY 
			BETWEEN '{$this->d1}' AND '{$this->d2}'";
			$total = M('reportcampaign','aw_',$this->connectReports)->query($sql2);
			$list = array(
				'table'=>$tableList,
				'total'=>$total[0] 
			);
		echo json_encode($list);
	}
	
	
	//关键词报告
	public function keywords(){
		$sql = "
		SELECT * FROM (SELECT 
		CASE STATUS 
		WHEN 'enabled' THEN '已启用'
		WHEN 'paused' THEN '已暂停'
		ELSE '已移除' END AS STATUS,
			CRITERIA,
			KEYWORD_MATCH_TYPE,
			CAMPAIGN_NAME,
			ADGROUP_NAME,
			if(SYSTEM_SERVING_STATUS = 'eligible','有效','由于质量得分很低而很少展示') AS SYSTEM_SERVING_STATUS,
			SUM(CLICKS) AS CLICKS,
			SUM(IMPRESSIONS) AS IMPRESSIONS,
			if(
	FORMAT( SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
) AS CTR, 
			
			if(
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
	0,
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
) AS AVERAGE_CPC,
			
			SUM(COST) AS COST,
			
			FORMAT(AVG(AVERAGE_POSITION),2) AS AVERAGE_POSITION,
			
			SUM(ALL_CONVERSIONS)  AS ALL_CONVERSIONS,
		
			if(FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2) is null,0 ,FORMAT( SUM(COST) / SUM(ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION
		
			FROM `aw_reportkeywords` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY 
			BETWEEN '{$this->d1}' AND '{$this->d2}'  AND NETWORK = 'Search Network'  GROUP BY CRITERIA, CAMPAIGN_NAME,KEYWORD_MATCH_TYPE) AS new ORDER BY new.COST DESC LIMIT 0 ,20";
			$tableList = M('reportcampaign','aw_',$this->connectReports)->query($sql);
		$sql3 = "
		SELECT * FROM (SELECT 
		CASE STATUS 
		WHEN 'enabled' THEN '已启用'
		WHEN 'paused' THEN '已暂停'
		ELSE '已移除' END AS STATUS,
			CRITERIA,
			KEYWORD_MATCH_TYPE,
			CAMPAIGN_NAME,
			ADGROUP_NAME,
			if(SYSTEM_SERVING_STATUS = 'eligible','有效','由于质量得分很低而很少展示') AS SYSTEM_SERVING_STATUS,
			SUM(CLICKS) AS CLICKS,
			SUM(IMPRESSIONS) AS IMPRESSIONS,
			if(
	FORMAT( SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
) AS CTR, 
			
			if(
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
	0,
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
) AS AVERAGE_CPC,
			
			SUM(COST) AS COST,
			
			FORMAT(AVG(AVERAGE_POSITION),2) AS AVERAGE_POSITION,
			
			SUM(ALL_CONVERSIONS)  AS ALL_CONVERSIONS,
		
			if(FORMAT(SUM(COST) / SUM(ALL_CONVERSIONS),2) is null,0 ,FORMAT( SUM(COST) / SUM(ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION
		
			FROM `aw_reportkeywords` WHERE `ACCOUNT_ID` = {$this->cid} AND DAY 
			BETWEEN '{$this->d1}' AND '{$this->d2}'  AND NETWORK = 'Search Network'  GROUP BY CRITERIA, CAMPAIGN_NAME,KEYWORD_MATCH_TYPE) AS new ORDER BY new.ALL_CONVERSIONS DESC LIMIT 0 ,20";
			$histogram2List = M('reportcampaign','aw_',$this->connectReports)->query($sql3);
			
			
			
			$list = array(
				'table'=>$tableList,
				'histogram2List'=>$histogram2List 
			);
			
		echo json_encode($list);
			//echo json_encode($tableList);
		
	}
	
	//地理位置
	public function geo(){
		$sql = "SELECT ag.cname,ar.COUNTRY_CRITERIA_ID,
			SUM(ar.CLICKS) AS CLICKS,
			SUM(ar.IMPRESSIONS) AS IMPRESSIONS,
			
			if(
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
) AS CTR, 
			
			if(
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
	0,
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
) AS AVERAGE_CPC,
			
			SUM(ar.COST) AS COST,
			ROUND(AVG(ar.AVERAGE_POSITION)) AS AVERAGE_POSITION,
			ROUND(SUM(ar.ALL_CONVERSIONS)) AS ALL_CONVERSIONS,
			if(FORMAT(SUM(ar.COST) / SUM(ar.ALL_CONVERSIONS),2) is null,0 ,FORMAT(SUM(ar.COST) / SUM(ar.ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION,
			
			if(
	FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS),2) is null,
	0 ,
	FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS) * 100,2)
)  AS ALL_CONVERSION_RATE
			
			FROM `aw_reportgeo` AS ar LEFT JOIN ads_geo AS ag ON ar.COUNTRY_CRITERIA_ID = ag.id WHERE ar.`ACCOUNT_ID` = {$this->cid} AND ar.DAY 
			BETWEEN '{$this->d1}' AND '{$this->d2}' GROUP BY ar.COUNTRY_CRITERIA_ID ORDER BY COST DESC LIMIT 0 ,20";
			$tableList = M('reportcampaign','aw_',$this->connectReports)->query($sql);
		
		//echo $sql;
		//exit;
		
		$sql2 = "SELECT ag.cname,
			SUM(ar.CLICKS) AS CLICKS,
			SUM(ar.IMPRESSIONS) AS IMPRESSIONS,
			if(
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2) is null,0,
	FORMAT(  SUM(CLICKS) / SUM(IMPRESSIONS) * 100  ,2)
) AS CTR, 
			
			
			if(
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2) is null,
	0,
	FORMAT(  SUM(COST) / SUM(CLICKS) ,2)
) AS AVERAGE_CPC,
			
			
			SUM(ar.COST) AS COST,
			ROUND(AVG(ar.AVERAGE_POSITION)) AS AVERAGE_POSITION,
			ROUND(SUM(ar.ALL_CONVERSIONS)) AS ALL_CONVERSIONS,
			if(FORMAT(SUM(ar.COST) / SUM(ar.ALL_CONVERSIONS),2) is null,0 ,FORMAT(SUM(ar.COST) / SUM(ar.ALL_CONVERSIONS),2))  AS COST_PER_ALL_CONVERSION,
			
			if(
	FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS),2) is null,
	0 ,
	FORMAT(SUM(ALL_CONVERSIONS) / SUM(CLICKS) * 100,2)
)  AS ALL_CONVERSION_RATE
			
			
			FROM `aw_reportgeo` AS ar LEFT JOIN ads_geo AS ag ON ar.COUNTRY_CRITERIA_ID = ag.id WHERE ar.`ACCOUNT_ID` = {$this->cid} AND ar.DAY 
			BETWEEN '{$this->d1}' AND '{$this->d2}' GROUP BY ar.COUNTRY_CRITERIA_ID ORDER BY ALL_CONVERSIONS DESC LIMIT 0,5";
			$tableList2 = M('reportcampaign','aw_',$this->connectReports)->query($sql2);
		
			echo json_encode(array($tableList,$tableList2));
			
	}
	
	
	
	
	//获取CRM客服信息
	public function customerService(){
		$mccID = substr($this->cid,0,3).'-'.substr($this->cid,3,3).'-'.substr($this->cid,6,4);
		$n_accountinfo = M('n_accountinfo',NULL)->where("customer_Id = '{$mccID}'")->find();
		$u = M('manager')->where("mcc = '{$n_accountinfo['mcc']}'")->find();
		$userInfo['username'] = $u['nickname'];
		$userInfo['office'] = $u['tel'];
		$userInfo['email'] = $u['email'];
		echo json_encode($userInfo);
	}
	
	//获取CRM客户信息
	public function customer(){
 		$mccID = substr($this->cid,0,3).'-'.substr($this->cid,3,3).'-'.substr($this->cid,6,4);
		$n_accountinfo = M('n_accountinfo',NULL)->where("customer_Id = '{$mccID}'")->find();
		$orderInfo['companyname'] = $n_accountinfo['compaign'];
		echo json_encode($orderInfo);
	}
	
	//获取余额信息
	public function blance(){
			$day2  = date('Y-m-d',time());
			$cid = $this->cid;
			//echo $cid;
			//exit;
			$rsa =  M('ads_budget',NULL,$this->connectReports)->where("cid = '{$cid}'")-> find();
			
			//echo json_encode($rsa);
			//exit;
			
			$day1= $rsa['start_date'];
			$sql = "SELECT SUM(COST) AS COST FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$day1}' AND '{$day2}'";
			
			
			
			//过去7天
			$day3 = date("Y-m-d",strtotime("-7 day"));

			
			$sql7  = "SELECT SUM(COST) AS COST FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = {$cid} AND DAY BETWEEN '{$day3}' AND '{$day2}'";
			$cost7 = M('',NULL,$this->connectReports)->query($sql7);
			$cost7 = $cost7[0]['cost']/7;
			
			$rs2 = M('',NULL,$this->connectReports)->query($sql);
			
			//pr(array($rs2));
		//
		//	exit;
			
			$blance = sprintf("%.2f",($rsa['budget'] - $rs2[0]['cost']));
			
			$blance=($blance<0)?0:$blance;
			$cost = M('reportaccount','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}'")->order('Day DESC')->limit(0,7)->avg('COST');
		if($rsa['id']){
			$day = ceil($blance/$cost7);
			$day=($day<0)?0:$day;
			echo "[{$blance},{$day}]";	
		}else{
			echo '[0,0]';	
		}
	}	
}