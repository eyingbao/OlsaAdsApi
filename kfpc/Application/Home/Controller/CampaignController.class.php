<?php
namespace Home\Controller;
use Common\Controller\CommonController;
//广告系列控制器
class CampaignController extends CommonController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
	   	if(IS_POST){
			//get_report_data('campaignBudgetErr',$_POST);
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				
				$sql = "SELECT * FROM 
(
SELECT NEW.ACCOUNT_DESCRIPTIVE_NAME,NEW.ACCOUNT_ID,SUM(NEW.COST) as COST, SUM(NEW.Budget) as Budget,NEW.CAMPAIGN_NAME , ROUND(SUM(NEW.Budget)/SUM(NEW.COST),2) AS per FROM 
(
SELECT ACCOUNT_DESCRIPTIVE_NAME,ACCOUNT_ID,CAMPAIGN_NAME,Budget,SUM(COST) as COST,DAY FROM aw_reportcampaign WHERE ACCOUNT_ID IN({$cids}) AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY CAMPAIGN_ID ,DAY ORDER BY DAY DESC

) AS NEW GROUP BY NEW.CAMPAIGN_NAME

) AS NEW2 WHERE NEW2.per < 60 OR NEW2.per > 90";

				//echo $sql;
				//exit;
				
				$list = M('',NULL,$this->connectReports)->query($sql);
				
				$alist = array();
				//
				foreach($list as $v){
					array_push($alist,array($v['account_descriptive_name'],$v['account_id'],$v['campaign_name'],$v['budget'],$v['cost'],$v['per']));
				}
				$final['list'] = $alist;
				//pr(array($list));
			}
			echo json_encode($final);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
	
	//消耗异常
	public function costErr(){
		if(IS_POST){
			$d[0]  = date('Y-m-d',strtotime("-1 day"));
			$d[1] = date('Y-m-d',strtotime("-2 day"));
			$arrs['all'] = array();
			$idsArr = $_POST['ids'];
			foreach($idsArr as $v){
				array_push($arrs['all'],str_replace('-','',$v));
			}
			$ids = implode(',',$arrs['all']);
			$quick = array(
				'mcc'=>quick_mcc_name($this->connectReports,$ids),
				'cost1'=>array(),
				'cost2'=>array()
			);
			$sql = "SELECT * FROM (
			 SELECT SUM(COST) AS COST, ACCOUNT_ID FROM `aw_reportcampaign` WHERE DAY   LIKE '%{$d[0]}%' GROUP BY ACCOUNT_ID
			 ) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			 //pr(array($sql));
			 $list = M('',NULL,$this->connectReports)->query($sql);
			 
			
			 
			if(is_array($list) && !empty($list)){
				foreach($list as $v){
					$quick['cost1'][$v['account_id']] = $v['cost'];
				}
			}
			
			$sql = "SELECT * FROM (
			 SELECT SUM(COST) AS COST, ACCOUNT_ID FROM `aw_reportcampaign` WHERE DAY   LIKE '%{$d[1]}%' GROUP BY ACCOUNT_ID
			 ) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			$list = M('',NULL,$this->connectReports)->query($sql);
			if(is_array($list) && !empty($list)){
				foreach($list as $v){
					$quick['cost2'][$v['account_id']] = $v['cost'];
				}
			}
			
			$final  = array();
			$sort = array();
			foreach($arrs['all'] as $v){
				 $diff = sprintf("%.2f",$quick['cost1'][$v] - $quick['cost2'][$v]);
				 $mccs = filter_mcc($v);
				 $sort[$mccs] = $diff;
			}
			asort($sort); 
			foreach($sort as $k2=>$v2){
				$v = str_replace('-','',$k2);
				if($v2 >= 50 || $v2 <= -50)
					 array_push(
						$final,
						array(
							'id'=>'#'.++$i,
							'cid'=>$k2,
							'name'=>$quick['mcc'][$v],
							'cost2'=>sprintf("%.2f",$quick['cost2'][$v]),
							'cost1'=>sprintf("%.2f",$quick['cost1'][$v]),
							'diff'=>$v2,
							'per'=>'0%'
						)
					); 
			}
			$f["up"]  = $final;
			$f["down"] = array_reverse($final);
			echo json_encode($f);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		 }
	}
	
	//转化对比
	public function conversionsContrast(){
		 if(IS_POST){
			$d[0] = date('Ymd',strtotime('-7 day'));
			$d[1] = date('Ymd',strtotime('-0 day'));
			$d[3] = date('Ymd',strtotime('-14 day'));
			$d[4] = date('Ymd',strtotime('-7 day'));
			
			//pr(array($d));
			$arrs['all'] = array();
			$idsArr = $_POST['ids'];
			foreach($idsArr as $v){
				array_push($arrs['all'],str_replace('-','',$v));
			}
			$ids = implode(',',$arrs['all']);
			
			//过去7天转化次数
			 $sql7 = "SELECT * FROM (SELECT  SUM(ALL_CONVERSIONS) AS AC , ACCOUNT_ID FROM `aw_reportcampaign` WHERE Day  BETWEEN '{$d[0]}' AND '{$d[1]}' GROUP BY ACCOUNT_ID) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			 
			  $sql14 = "SELECT * FROM (SELECT  SUM(ALL_CONVERSIONS) AS AC , ACCOUNT_ID FROM `aw_reportcampaign` WHERE Day  BETWEEN '{$d[3]}' AND '{$d[4]}' GROUP BY ACCOUNT_ID) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			 
			 $arr7 = array();
			 $arr14 = array();
			 $list7 = M('',NULL,$this->connectReports)->query($sql7);
			 $list14 = M('',NULL,$this->connectReports)->query($sql14);
			 $mcc = quick_mcc_name($this->connectReports,$ids);
			 
			 foreach($list7 as $v){
				 $arr7[$v['account_id']] = $v['ac'];
			 }
			 
			 foreach($list14 as $v){
				 $arr14[$v['account_id']] = $v['ac'];
			 }
			 
			 $final  = array();
			 $sort = array();
			 foreach($arrs['all'] as $v){
				 $diff = intval($arr7[$v] - $arr14[$v]);
				 $mccs = filter_mcc($v);
				 $sort[$mccs] = $diff;
			}
			 asort($sort); 
			 foreach($sort as $k2=>$v2){
				 $v = str_replace('-','',$k2);
				 $diff = intval($arr7[$v] - $arr14[$v]);
				  array_push(
					$final,
					array(
						'id'=>'#'.++$i,
						'cid'=>$k2,
						'name'=>$mcc[$v],
						'last7'=>intval($arr7[$v]),
						'last14'=>intval($arr14[$v]),
						'diff'=>$v2
			 		)
				); 
			 }
			 $f["up"]  = $final;
			 $f["down"] = array_reverse($final);
			echo json_encode($f);
		 }else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		 }
	}
	
	//cpa对比
	public function cpaContrast(){
		 if(IS_POST){
			$d[0] = date('Ymd',strtotime('-7 day'));
			$d[1] = date('Ymd',strtotime('-0 day'));
			$d[3] = date('Ymd',strtotime('-14 day'));
			$d[4] = date('Ymd',strtotime('-7 day'));
			$arrs['all'] = array();
			$idsArr = $_POST['ids'];
			foreach($idsArr as $v){
				array_push($arrs['all'],str_replace('-','',$v));
			}
			$ids = implode(',',$arrs['all']);
			
			//过去7天转化次数
			$sql7 = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(ALL_CONVERSIONS) AS AC , ACCOUNT_ID FROM `aw_reportcampaign` WHERE Day  BETWEEN '{$d[0]}' AND '{$d[1]}' GROUP BY ACCOUNT_ID) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			 
			 $sql14 = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(ALL_CONVERSIONS) AS AC , ACCOUNT_ID FROM `aw_reportcampaign` WHERE Day  BETWEEN '{$d[3]}' AND '{$d[4]}' GROUP BY ACCOUNT_ID) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			 
			 $arr7 = array();
			 $arr14 = array();
			 $list7 = M('',NULL,$this->connectReports)->query($sql7);
			 $list14 = M('',NULL,$this->connectReports)->query($sql14);
			 $mcc = quick_mcc_name($this->connectReports,$ids);
			 
		
			 if(is_array($list7) && !empty($list7)){
				 foreach($list7 as $v){
					 $cpa = intval($v['ac']) == 0?0:$v['cost'] / intval($v['ac']);
					 $arr7[$v['account_id']] =sprintf("%.2f", $cpa);
				 
				 }
			 }
			 if(is_array($list14) && !empty($list14)){
				 foreach($list14 as $v){
					 $cpa = intval($v['ac']) == 0?0:$v['cost'] / intval($v['ac']);
					 $arr14[$v['account_id']] = sprintf("%.2f",$cpa);
				
				 }
			 }
			 $final  = array();
			 $sort = array();
			 foreach($arrs['all'] as $v){
				 $diff = sprintf("%.2f",$arr7[$v] - $arr14[$v]);
				 $mccs = filter_mcc($v);
				 $sort[$mccs] = $diff;
			}
			 asort($sort); 
			 foreach($sort as $k2=>$v2){
				 $v = str_replace('-','',$k2);
				 $diff = intval($arr7[$v] - $arr14[$v]);
				  array_push(
					$final,
					array(
						'id'=>'#'.++$i,
						'cid'=>$k2,
						'name'=>$mcc[$v],
						'last7'=>sprintf("%.2f",isset($arr7[$v])?$arr7[$v]:0),
						'last14'=>sprintf("%.2f",isset($arr14[$v])?$arr14[$v]:0),
						'diff'=>$v2
			 		)
				); 
			 }
			 $f["up"]  = $final;
			 $f["down"] = array_reverse($final);
			echo json_encode($f);
		
		 }else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		 }
	}
}