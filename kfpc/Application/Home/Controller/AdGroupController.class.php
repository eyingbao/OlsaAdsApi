<?php
namespace Home\Controller;
use Common\Controller\CommonController;
//广告组系列控制器
class AdGroupController extends CommonController {
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		 if(IS_POST){
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql="SELECT a.*, b.CAMPAIGN_NAME FROM
(

SELECT ACCOUNT_DESCRIPTIVE_NAME,CAMPAIGN_ID,ADGROUP_NAME, ACCOUNT_ID,IMPRESSIONS,CTR FROM aw_reportadgroup WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND ADGROUP_STATUS = 'enabled' AND IMPRESSIONS > 500 AND CTR < 0.01 

) AS a LEFT JOIN 

(
SELECT DISTINCT CAMPAIGN_ID, CAMPAIGN_NAME FROM aw_reportcampaign WHERE ACCOUNT_ID IN ({$cids})
) AS b 

ON a.CAMPAIGN_ID = b.CAMPAIGN_ID";
				$list = M('',NULL,$this->connectReports)->query($sql);
				$alist = array();
				foreach($list as $v){
					array_push($alist,array($v['account_descriptive_name'],$v['account_id'],$v['campaign_name'],$v['adgroup_name'],$v['impressions'],$v['ctr']));
				}
				
				$sql2="SELECT COUNT(*) AS c FROM aw_reportadgroup WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND ADGROUP_STATUS = 'enabled'";
				$c = M('',NULL,$this->connectReports)->query($sql2);
				
				
				$final['allc1'] = count($alist); 
				$final['allc2'] = $c[0]['c']; 
				$final['allc3'] = sprintf("%.2f",($final['allc1'] / $final['allc2'])*100) ; 
				
				$final['list'] = $alist;
			}
			echo json_encode($final);
		 }else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		 }
	}
   
   //关键词异常
   	public function keywordsErr(){
	 	if(IS_POST){
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql = "SELECT a.c,b.c as c1,a.ACCOUNT_ID FROM

(

SELECT COUNT(*) AS c ,ACCOUNT_ID FROM aw_reportkeywords WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}'AND ACCOUNT_ID IN ({$cids}) AND NETWORK = 'Search Network' AND STATUS = 'enabled' AND QUALITY_SCORE < 4 GROUP BY  ACCOUNT_ID ) as a

INNER JOIN 
(
SELECT COUNT(*) AS c ,ACCOUNT_ID FROM aw_reportkeywords WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND NETWORK = 'Search Network' AND STATUS = 'enabled'  GROUP BY  ACCOUNT_ID 

) AS b 

ON a.ACCOUNT_ID = b.ACCOUNT_ID";
				$akey = M('',NULL,$this->connectReports)->query($sql);
				$final['allc1'] = 0;
			 	$final['allc2'] = 0;
			 	foreach($akey as $v){
					$bkey[$v['account_id']] = $v;
					$final['allc1']+=$v['c'];
					$final['allc2']+=$v['c1'];
				}
				$sql = "SELECT ACCOUNT_DESCRIPTIVE_NAME,CAMPAIGN_NAME,ADGROUP_NAME, ACCOUNT_ID,CAMPAIGN_NAME ,CRITERIA ,QUALITY_SCORE FROM aw_reportkeywords WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND  NETWORK = 'Search Network' AND STATUS = 'enabled' AND QUALITY_SCORE < 4 ";	
				$list = M('',NULL,$this->connectReports)->query($sql);
				$alist = array();
				foreach($list as $v){
					array_push($alist,array($v['account_descriptive_name'],$v['account_id'],$v['campaign_name'],$v['adgroup_name'],$v['criteria'],$v['quality_score']));
				}
				
				if($final['allc2'] == 0 ){
					$per = 'N/A';
				}else{
					$per = sprintf("%.2f",($final['allc1'] / $final['allc2']) * 100);
				}
				$final['allc3'] = $per;
				$final['k'] = $bkey;
				$final['list'] = $alist;
			}
			echo json_encode($final);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
   	}   
   
   	//广告异常
   	public function adErr(){
	   if(IS_POST){
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql = "SELECT ACCOUNT_DESCRIPTIVE_NAME,CAMPAIGN_NAME,ADGROUP_NAME, ACCOUNT_ID,CAMPAIGN_NAME ,HEADLINE FROM aw_reportad WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND  NETWORK = 'Search Network' AND STATUS = 'enabled' AND CREATIVE_APPROVAL_STATUS ='disapproved'";	
				$list = M('',NULL,$this->connectReports)->query($sql);
				$alist = array();
				foreach($list as $v){
					array_push($alist,array($v['account_descriptive_name'],$v['account_id'],$v['campaign_name'],$v['adgroup_name'],$v['headline'],$v['creative_approval_status']));
				}
				$final['list'] = $alist;
			}
			echo json_encode($final);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
	  }
   	}    
   
    //广告语（CTR）异常
	public function adCtrErr(){
		if(IS_POST){
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql = "SELECT a.c,b.c as c1,a.ACCOUNT_ID FROM
(

SELECT COUNT(*) AS c ,ACCOUNT_ID FROM aw_reportad WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}'AND ACCOUNT_ID IN ({$cids}) AND NETWORK = 'Search Network' AND STATUS = 'enabled' AND CTR < 0.01 AND IMPRESSIONS > 1000 GROUP BY  ACCOUNT_ID ) as a

INNER JOIN 
(
SELECT COUNT(*) AS c ,ACCOUNT_ID FROM aw_reportad WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND NETWORK = 'Search Network' AND STATUS = 'enabled'  GROUP BY  ACCOUNT_ID 

) AS b 

ON a.ACCOUNT_ID = b.ACCOUNT_ID";
				$akey = M('',NULL,$this->connectReports)->query($sql);
				$final['allc1'] = 0;
			 	$final['allc2'] = 0;
				foreach($akey as $v){
					$bkey[$v['account_id']] = $v;
					$final['allc1']+=$v['c'];
					$final['allc2']+=$v['c1'];
				}
				$sql = "SELECT ACCOUNT_DESCRIPTIVE_NAME,CAMPAIGN_NAME,ADGROUP_NAME, ACCOUNT_ID,CAMPAIGN_NAME ,HEADLINE ,IMPRESSIONS,CTR FROM aw_reportad WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND  NETWORK = 'Search Network' AND STATUS = 'enabled' AND IMPRESSIONS > 1000 AND CTR < 0.01";	
			 	$list = M('',NULL,$this->connectReports)->query($sql);
				$alist = array();
				foreach($list as $v){
					array_push($alist,array($v['account_descriptive_name'],$v['account_id'],$v['campaign_name'],$v['adgroup_name'],$v['headline'],$v['impressions'],$v['ctr']));
				}
				
				if($final['allc2'] == 0 ){
					$per = 'N/A';
				}else{
					$per = sprintf("%.2f",($final['allc1'] / $final['allc2']) * 100);
				}
				$final['allc3'] = $per;
				$final['k'] = $bkey;
				$final['list'] = $alist;
			}
			echo json_encode($final);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
	
	//广告语（数量）异常
	public function adCountErr(){
		if(IS_POST){
			//get_report_data('adCountErr',$_POST);
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql = "
				SELECT * FROM (
					SELECT COUNT(*) AS c,HEADLINE,ACCOUNT_DESCRIPTIVE_NAME,ADGROUP_NAME, ACCOUNT_ID,CAMPAIGN_NAME FROM aw_reportad WHERE DAY BETWEEN '{$date[0]}' AND '{$date[1]}' AND ACCOUNT_ID IN ({$cids}) AND  NETWORK = 'Search Network' AND STATUS = 'enabled' GROUP BY AD_ID
				) AS new WHERE new.c=1
				
				";	
				$list = M('',NULL,$this->connectReports)->query($sql);
				$alist = array();
				foreach($list as $v){
					array_push($alist,array($v['account_descriptive_name'],$v['account_id'],$v['campaign_name'],$v['adgroup_name'],$v['headline'],$v['c']));
				}
				$final['list'] = $alist;
			}
			echo json_encode($final);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
}