<?php
namespace Mobile\Controller;
use Common\Controller\MobileController;
//报表控制器
class IndexController extends MobileController {
	protected $d1;
	protected $d2;
	protected $cid;
	protected $connectCrm;
	
	public function index(){
		$uid = session('uuid');
		$bb =  M('n_accountinfo',NULL)->where("id = {$uid}")->find();
		$rs[0]['customer_id']=$bb['customer_id'];
		if(!$rs[0]['customer_id']){
			$this->display('nomcc');
		}else{
			$geos = M('ads_geo',NULL,$this->connectReports)->getField('id,cname',true);
			for($j = 1; $j<=2 ; $j++){
				$dt[$j] = array();
				for($i = 0; $i<7;$i++){
					$day = (-7 * $j)+$i;
					$dt[$j][$i] = date('Y-m-d',strtotime($day.' day'));
				}
			}		
			for($j = 1; $j<=2 ; $j++){
				$dt2[$j] = array();
				for($i = 0; $i<7;$i++){
					$day = (-7 * $j)+$i;
					$dt2[$j][$i] = date('m-d',strtotime($day.' day'));
				}
			}
			$cids = str_replace('-','',$rs[0]['customer_id']);
			$rs2 = M('reportaccount','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cids}'")->find();
			$mccid[0]= $rs[0]['customer_id'];
			$mccid[1] = str_replace('-','',$mccid[0]);
			$bd= M('reportbudget','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$mccid[1]}'")->sum('AMOUNT');
			$date[0] = date('Y-m-d',strtotime('0 day'));
			$date[5] = date('Y-m-d',strtotime('-5 day'));
			$date[7] = date('Y-m-d',strtotime('-7 day'));
			$list = array();
			$request = array('lostBudget','top7cost','top5keywords','geo1','geo2','blance');
			foreach($request  as $v){
				if($v == 'top5keywords' || $v == 'geo1' || $v == 'geo2'){
					array_push($list,$this->domain.'Ads/Mobile/'.$v.'?cid='.$mccid[1].'&d1='.$date[7].'&d2='.$date[0].'&limit=5');
				}else{
					array_push($list,$this->domain.'Ads/Mobile/'.$v.'?cid='.$mccid[1].'&d1='.$date[7].'&d2='.$date[0]);
				}
			}
			$arr = async_get_url($list);
			foreach($arr as $k=>$v){
				$arr[$k] = json_decode($v,true);
			}
			foreach($arr[3] as $k2=>$v2){
				$arr[3][$k2]['geo'] = $geos[$v2['country_criteria_id']];
			}
			foreach($arr[4] as $k2=>$v2){
				$arr[4][$k2]['geo'] = $geos[$v2['country_criteria_id']];
			}
			$list = $arr;
			$final['cost'] = array();
			foreach($final as $k3=>$v3){
				foreach($dt[1] as $k=>$v){
					$final[$k3][$k] = 0;
					foreach($list[1] as $v2){
						if($v == $v2['day2']){
							$final[$k3][$k] = $v2[$k3];
							break;
						}
					}
				}
			}
			$cname = $bb['compaign'];
			$this->assign('cname',$cname);
			$this->assign('rs',$rs2);
			$this->assign('list',$list);
			$this->assign('contact',$list[6][1]);
			$this->assign('li',$list[6][0]);
			$this->assign('cost',json_encode($final['cost']));
			$this->assign('dt',json_encode($dt2[1]));
			$geoJson[0] = $arr[3];
			$geoJson[1] = $arr[4];
			$geoJson = json_encode($geoJson);
			$this->assign('geoJson',$geoJson);
			$this->display('mcc');
		}
	}
	
	public function mccReport(){
		$uid = session('uuid');
		if(IS_GET){
			 $bb = M('n_accountinfo',NULL)->where("id = {$uid}")->find();
			
			$rs[0]['customer_id']=$bb['customer_id'];
			if(!$rs[0]['customer_id']){
				$this->display('nomcc');
			}else{
				$cname = $bb['compaign'];
				$this->assign('cname',$cname);
				$cids = str_replace('-','',$rs[0]['customer_id']);
				$rs2 = M('reportaccount','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cids}'")->getField('account_id');
				$rs2['account_descriptive_name']=$canme;
				$mccid[0]= $rs[0]['customer_id'];
				$mccid[1] = str_replace('-','',$mccid[0]);
				$date = get_date_list2();
				$this->assign('rs',$rs2);
				$this->assign('mccid',$mccid[1]);
				$this->assign('contact',$contact);
				$this->assign('date',$date);
				$this->display('mcc_report');
			}
		}else{
			
			$d = explode('|',$_POST['date']);
			$cid = $_POST['cid'];
			
			switch($_POST['name']){
				case 'line':
					
					$date[0] = date('Y-m-d',strtotime('0 day'));
					
					$date[7] = date('Y-m-d',strtotime('-8 day'));
					
					$date[8] = date('Y-m-d',strtotime('-7 day'));
					
					$date[15] = date('Y-m-d',strtotime('-15 day'));
					
					//$d[0] = $d[1] = array();
					
					for($j = 1; $j<=2 ; $j++){
						$dt[$j] = array();
						for($i = 0; $i<7;$i++){
							$day = (-7 * $j)+$i;
							$dt[$j][$i] = date('Y-m-d',strtotime($day.' day'));
						}
					}
					
					for($j = 1; $j<=2 ; $j++){
						$dt2[$j] = array();
						for($i = 0; $i<7;$i++){
							//if($i == 0 || $i== 3 || $i==6){
								$day = (-7 * $j)+$i;
								$dt2[$j][$i] = date('m-d',strtotime($day.' day'));
							//}else{
								//$dt2[$j][$i] = '';
							//}
						}
					}
				
					
					$sql = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS, SUM(CLICKS) AS CLICKS , SUM(CONVERSION_VALUE) AS CONVERSION_VALUE ,  Day FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} GROUP BY Day) AS new  WHERE new.Day BETWEEN '{$date[7]}' AND '{$date[0]}' ORDER BY new.Day ASC";
					
					$list[1] = M('reportcampaign','aw_',$this->connectReports)->query($sql);
					
					
					$sql = "SELECT * FROM (SELECT SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS, SUM(CLICKS) AS CLICKS , SUM(CONVERSION_VALUE) AS CONVERSION_VALUE ,  Day FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} GROUP BY Day) AS new  WHERE new.Day BETWEEN '{$date[15]}' AND '{$date[8]}' ORDER BY new.Day ASC";
					
					$list[2] = M('reportcampaign','aw_',$this->connectReports)->query($sql);
					
					foreach($list as $k=> $v){
						$list[$k] = is_arrays($v);
					}
					foreach($list as $k=> $v){
						foreach($v as $k2=> $v2){
							$c = intval($v2['conversion_value']);
							$list[$k][$k2]['conversion_value'] = $c;
							if($c > 10){
								$list[$k][$k2]['conversion_value'] =$c/10;
							}
							$list[$k][$k2]['day'] = str_replace(' 20:00:00','',$v2['day']);	
						}
						
					}
					
					//pr(array($list,$dt));
					for($i=1;$i<=2;$i++){
						$final[$i]['clicks'] = array();
						$final[$i]['impressions'] = array();
						$final[$i]['cost'] = array();
						$final[$i]['conversion_value'] = array();
						
						foreach($final[$i] as $k3=>$v3){
							foreach($dt[$i] as $k=>$v){
								$final[$i][$k3][$k] = 0;
								foreach($list[$i] as $v2){
									if($v == $v2['day']){
										$final[$i][$k3][$k] = $v2[$k3];
										//$final['conversions'][$k] = $v2['conversion_value'];
										//$final['cost'][$k] = $v2['cost'];
										//$final['impressions'][$k] = $v2['impressions'];
										break;
									}
								}
							}
						}
						
					}
					
					/*$final[0]['clicks'] = array();
					$final[0]['impressions'] = array();
					$final[0]['cost'] = array();
					$final[0]['conversion_value'] = array();
					foreach($final[0] as $k3=>$v3){
						foreach($d as $k=>$v){
							$final[0][$k3][$k] = 0;
							foreach($list[0] as $v2){
								if($v == $v2['day']){
									$final[0][$k3][$k] = $v2[$k3];
									//$final['conversions'][$k] = $v2['conversion_value'];
									//$final['cost'][$k] = $v2['cost'];
									//$final['impressions'][$k] = $v2['impressions'];
									break;
								}
							}
						}
					}
					
					$final[1]['clicks'] = array();
					$final[1]['impressions'] = array();
					$final[1]['cost'] = array();
					$final[1]['conversion_value'] = array();
					foreach($final[1] as $k3=>$v3){
						foreach($d2 as $k=>$v){
							$final[1][$k3][$k] = 0;
							foreach($list[1] as $v2){
								if($v == $v2['day']){
									$final[1][$k3][$k] = $v2[$k3];
									//$final['conversions'][$k] = $v2['conversion_value'];
									//$final['cost'][$k] = $v2['cost'];
									//$final['impressions'][$k] = $v2['impressions'];
									break;
								}
							}
						}
					}*/
					
					$final[3]  = $dt2[1];
					
					//pr(array($final));
					
					
					json_msg(true,$final);
					
				break;
				
				
				case 'keywords':
					$list = array();
					if(count($d) ==1){
						array_push($list,$this->domain.'Ads/Mobile/top5Keywords?cid='.$cid.'&d1='.$d[0]);
					}else{
						array_push($list,$this->domain.'Ads/Mobile/top5Keywords?cid='.$cid.'&d1='.$d[0].'&d2='.$d[1]);
					}
					$arr = async_get_url($list);
					$list = json_decode($arr[0],true);;
				break;
				case 'cost':
					$list = array();
					if(count($d) == 1){
						array_push($list,$this->domain.'Ads/Mobile/top7Cost?cid='.$cid.'&d1='.$d[0]);
					}else{
						array_push($list,$this->domain.'Ads/Mobile/top7Cost?cid='.$cid.'&d1='.$d[0].'&d2='.$d[1]);
					}
					$arr = async_get_url($list);
					$list = json_decode($arr[0],true);;
					
					/*$list = is_arrays($list);
					if(count($list) != 0){
						foreach($list as $k=>$v){
							$list[$k]['day'] = substr($v['day'],6,20);
							$temp = explode(' ',$list[$k]['day']);
							$temp2  = explode('-',$temp[0]);
							$list[$k]['day']  = $temp2[0].'月'.$temp2[1].'日'; 
							$list[$k]['conversion_value'] = intval($v['conversion_value']);
						}
					}*/
				break;
				
				case 'geo':
				
					$geos = M('ads_geo',NULL,$this->connectReports)->getField('id,cname',true);
					$lists = array();
				
				
					if(count($d) == 1){
						array_push($lists,$this->domain.'Ads/Mobile/geo1?cid='.$cid.'&d1='.$d[0]);
						//$list1 = M('reportgeo','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND Day  BETWEEN '{$d[0]}' AND '{$d[1]}'")-> field('COST,IMPRESSIONS,CLICKS,COUNTRY_CRITERIA_ID')->order('COST DESC')->select(); ///地区花费
					
					}else{
						array_push($lists,$this->domain.'Ads/Mobile/geo1?cid='.$cid.'&d1='.$d[0].'&d2='.$d[1]);
						//$list1 = M('reportgeo','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND Day  LIKE  '%{$d[0]}%'")-> field('COST,IMPRESSIONS,CLICKS,COUNTRY_CRITERIA_ID')->order('COST DESC')->select(); ///地区花费
				
					}
					$arr = async_get_url($lists);
					$list[0] = json_decode($arr[0],true);
					//echo "ACCOUNT_ID = '{$cid}' AND TIMESTAMP  BETWEEN '{$d[0]}' AND '{$d[1]}'";
					//exit;
					//pr(array($list1));
					//$list[0] = is_arrays($list1);
					$lists = array();
					if(count($d) == 1){
						array_push($lists,$this->domain.'Ads/Mobile/geo2?cid='.$cid.'&d1='.$d[0]);
					//$list2 =  M('reportgeo','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND Day  BETWEEN '{$d[0]}' AND '{$d[1]}'")-> field('COST,IMPRESSIONS,CLICKS,CONVERSION_VALUE,COUNTRY_CRITERIA_ID')->order('CONVERSION_VALUE DESC')->select(); ///地区转换
					
					}else{
						array_push($lists,$this->domain.'Ads/Mobile/geo2?cid='.$cid.'&d1='.$d[0].'&d2='.$d[1]);
						//	$list2 =  M('reportgeo','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND Day  LIKE  '%{$d[0]}%'")-> field('COST,IMPRESSIONS,CLICKS,CONVERSION_VALUE,COUNTRY_CRITERIA_ID')->order('CONVERSION_VALUE DESC')->select(); ///地区转换
					}
					
					$arr = async_get_url($lists);
					$list[1] = json_decode($arr[0],true);
					
					//$list[1] = is_arrays($list2);
					
					
					foreach($list as $k=>$v){
						foreach($v as $k2=>$v2){
							$list[$k][$k2]['geo'] = $geos[$v2['country_criteria_id']];
							$list[$k][$k2]['conversion_value'] = intval($v2['conversion_value']);
						}
					}
					break;
			}
			json_msg(true,$list);
		}
	}
	
	
}