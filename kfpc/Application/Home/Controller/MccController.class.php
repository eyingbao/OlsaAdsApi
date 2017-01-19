<?php
namespace Home\Controller;
use Common\Controller\CommonController;

//mcc账户控制器
class MccController extends CommonController {
	
	private $cky = array(array(),array());
	private $dk = array(array(),array());
	private $gn = array(array(),array());
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function getMcc(){
		$customerId = $_GET['customerId'];
		echo M('ads_mcc',NULL,$this->connectReports)->where("customerId = '{$customerId}'")->getField('name');
	}
   
	//投放渠道
	public function deliveryChannel(){
		if(IS_POST){
			//get_report_data('deliveryChannel',$_POST);
			$date = get_date($_POST['date'],false);
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql="SELECT a.c1,b.c2,c.c3,d.c4,e.c5,a.ACCOUNT_DESCRIPTIVE_NAME,a.ACCOUNT_ID FROM(
					SELECT  SUM(COST) AS c1 ,ACCOUNT_ID,ACCOUNT_DESCRIPTIVE_NAME FROM `aw_reportcriteria` WHERE  ACCOUNT_ID IN ({$cids})  AND DEVICE = 'Computers' AND NETWORK = 'Display Network' AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY ACCOUNT_ID
					) AS a 
					
					LEFT JOIN  
					
					(
					SELECT  SUM(COST) AS c2 ,ACCOUNT_ID FROM `aw_reportcriteria` WHERE  ACCOUNT_ID IN ({$cids})  AND  (DEVICE = 'Tablets with full browsers' OR DEVICE = 'Mobile devices with full browsers') AND NETWORK = 'Display Network'  AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY ACCOUNT_ID
					
					) AS b ON a.ACCOUNT_ID = b.ACCOUNT_ID
					
					
					LEFT JOIN(  
					
					SELECT  SUM(COST) AS c3 , ACCOUNT_ID FROM `aw_reportcriteria` WHERE  ACCOUNT_ID IN ({$cids})  AND DEVICE = 'Computers' AND NETWORK = 'Search Network' AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY ACCOUNT_ID
					
					) AS c ON b.ACCOUNT_ID = c.ACCOUNT_ID
					
					INNER JOIN(  
					
					SELECT  SUM(COST) AS c4, ACCOUNT_ID FROM `aw_reportcriteria` WHERE  ACCOUNT_ID IN ({$cids})  AND  (DEVICE = 'Tablets with full browsers' OR DEVICE = 'Mobile devices with full browsers') AND NETWORK = 'Search Network'  AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY ACCOUNT_ID
					
					) AS d ON c.ACCOUNT_ID = d.ACCOUNT_ID
					
					
					LEFT JOIN(
					
					SELECT  SUM(COST) as c5, ACCOUNT_ID FROM `aw_reportcriteria` WHERE  ACCOUNT_ID IN ({$cids}) AND CRITERIA_TYPE ='User List' AND DEVICE = 'Computers' AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}'
					
					) AS e ON d.ACCOUNT_ID = e.ACCOUNT_ID";
					
				//echo $sql;
				//exit;
					
					$list = M('',NULL,$this->connectReports)->query($sql);
					$alist = array();
					
					foreach($list as $v){
						array_push($alist,array($v['account_descriptive_name'],filter_mcc($v['account_id']),intval($v['c3'])?1:0,intval($v['c1'])?1:0,intval($v['c5'])?1:0,intval($v['c4'])?1:0,intval($v['c2'])?1:0));
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
	
	//获取账户余额
	public function index(){
		if(IS_POST){
			get_report_data('budget',$_POST);
		}else{
			
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
	
	//获取账户余额
	public function index3(){
		$day1= $_GET['day1'];
		$day2  = date('Ymd',time());
		$cid = $_GET['cid'];
		
		
		$sql= "SELECT SUM(COST) AS COST FROM `aw_reportaccount`  WHERE ACCOUNT_ID = {$cid} AND DAY BETWEEN '{$day1}' AND '{$day2}'";
		$rs = M('',NULL,$this->connectReports)->query($sql);
		
		echo '['.$cid.','.$rs[0]['cost'].']';
	}
	
	//余额提醒
	public function index2(){
		if(IS_POST){
			$day1  = date('Y-m-d',strtotime("-8 day"));
			$day2  = date('Y-m-d',time());
			$ids= array();
			if(is_array($_POST['ids']) && !empty($_POST['ids'])){
				foreach($_POST['ids'] as $v){
					array_push($ids,str_replace('-','',$v));
				}
				$ids = implode(',',$ids);
				$sql = "
				
				SELECT new2.* FROM (
				
				SELECT * , FLOOR(new.bce / new.avg_cost) as day FROM (
				
				SELECT a.*, b.COST , c.AVG_COST FROM 
				
				
				(
				
				SELECT * FROM ads_budget  WHERE cid IN  ({$ids})
				
				
				) AS a INNER JOIN (
				
					SELECT ne3.COST AS cost ,ne3.ACCOUNT_ID FROM ( SELECT SUM(COST) AS COST, ACCOUNT_ID FROM `aw_reportcampaign` WHERE DAY BETWEEN '{$day1}' AND '{$day2}' GROUP BY ACCOUNT_ID ) AS ne3 WHERE ne3. ACCOUNT_ID IN ({$ids}) 
				
				
				) AS b ON a.cid = b.ACCOUNT_ID INNER JOIN (
				
				
				SELECT FLOOR((ne4.COST/7)) AS AVG_COST ,ne4.ACCOUNT_ID FROM ( 
				
				SELECT SUM(COST) AS COST, ACCOUNT_ID FROM `aw_reportcampaign` WHERE DAY BETWEEN '{$day1}' AND '{$day2}' GROUP BY ACCOUNT_ID 
				
				
				) AS ne4 WHERE ne4. ACCOUNT_ID IN ({$ids}) 
				
				) AS c ON c.ACCOUNT_ID =  b.ACCOUNT_ID
				
				) AS new
				
				) AS new2  WHERE new2.day <=30;
				
				";
				$list = M('',NULL,$this->connectReports)->query($sql);
				$final  = array();
				foreach($list as $v){
					 array_push($final,array(
						array(
							'cid'=>substr($v['cid'],0,3).'-'.substr($v['cid'],3,3).'-'.substr($v['cid'],6,4),
							'cname'=>M('aw_reportaccount',NULL,$this->connectReports)->where('ACCOUNT_ID = '.$v['cid'])->getField('ACCOUNT_DESCRIPTIVE_NAME')
						),
						sprintf("%.2f",($v['bce']<=0)?0:$v['bce']),
						sprintf("%.2f",$v['cost']),
						sprintf("%.2f",$v['avg_cost']),
						($v['day']<=0)?0:$v['day']
					));
				}
				echo json_encode($final);
			}
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
	
	public function monthCost(){
		if(IS_POST){
			$date = get_date($_POST['date'],false);
			$date2 = get_date($_POST['date']);
			$dd=floor((strtotime($date2[1])-strtotime($date2[0]))/86400);
			$dd= $dd-1;
			$dat = array();
			$dt = $date[0];
			array_push($dat,substr($dt,5,5));
			for($i=1;$i<30;$i++){
				$dt = date("Y-m-d",strtotime("{$date[0]} +{$i} day"));
				array_push($dat,substr($dt,5,5));
			}
			
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				
				$sql="SELECT SUM(COST) AS COST,DAY FROM aw_reportcampaign WHERE ACCOUNT_ID IN ($cids) AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY DAY";
				
				//echo $sql;
				//exit;
				
				$list = M('',NULL,$this->connectReports)->query($sql);
				
				foreach($list as $v){
					$a[substr($v['day'],5,5)] = $v['cost'];
				}
				
				//
				//$alist = array();
				//echo $sql;
					
			}
			
			foreach($dat as $v){
				if($a[$v]){
						$b[$v] = $a[$v];
				}else{
					$b[$v] = 0;
				}
			}
			
			$c['labels'] =array_keys($b);
			$c['vals'] =array_values($b);
			echo json_encode($c);
			
		//	get_report_data('monthCost',$_POST);
		
		}else{
			$date = get_date($_GET['date']);
			$d1 = strtotime($date[0]);	
			$d2 = strtotime($date[1]);	
			$count = timediff($d1,$d2)+1;
			$dateArr = array();
			for($i = -$count ;$i<0;$i++){
				array_push($dateArr,date('Y-m-d', strtotime($i.' day')));
			}
			$this->assign('dates',json_encode($dateArr));
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
	
	public function active(){
		if(IS_POST){
			//get_report_data('activeMcc',$_POST);
			$date = get_date($_POST['date'],false);
			$date2 = get_date($_POST['date']);
			$dd=floor((strtotime($date2[1])-strtotime($date2[0]))/86400);
			$dd= $dd-1;
			$dat = array();
			$dt = $date[0];
			array_push($dat,substr($dt,5,5));
			for($i=1;$i<30;$i++){
				$dt = date("Y-m-d",strtotime("{$date[0]} +{$i} day"));
				array_push($dat,substr($dt,5,5));
			}
			
			$ids = get_cids($_POST['ids']);
			$final = array();
			if(count($ids)){
				$cids = implode(',',$ids);
				$sql="SELECT COUNT(NEW.DAY) AS c,NEW.DAY FROM (

SELECT SUM(COST) AS COST,ACCOUNT_ID,DAY FROM aw_reportcampaign WHERE ACCOUNT_ID IN ({$cids}) AND DAY BETWEEN '{$date[0]}' AND '{$date[1]}' GROUP BY DAY ,ACCOUNT_ID

) AS NEW WHERE NEW.COST > 0 GROUP BY NEW.DAY";
				$list = M('',NULL,$this->connectReports)->query($sql);
				
				foreach($list as $v){
					$a[substr($v['day'],5,5)] = $v['c'];
				}
				
				
			}
			
			foreach($dat as $v){
				if($a[$v]){
						$b[$v] = $a[$v];
				}else{
					$b[$v] = 0;
				}
			}
			
			$c['labels'] =array_keys($b);
			$c['vals'] =array_values($b);
			echo json_encode($c);
		
		
		}else{
			$date = get_date($_GET['date']);
			$d1 = strtotime($date[0]);	
			$d2 = strtotime($date[1]);	
			$count = timediff($d1,$d2)+1;
			$dateArr = array();
			for($i = -$count ;$i<0;$i++){
				array_push($dateArr,date('Y-m-d', strtotime($i.' day')));
			}
			$this->assign('dates',json_encode($dateArr));
			$this->assign('children',json_encode($this->selChildren));
			$this->assign('cc2',C('DOMAIN_PC').'/'.$this->cc1);
			$this->display();
		}
		
	}
	public function olsa(){
		if(IS_GET){
			$this->display();
		}else{
			$date = date("Y-m-d",time());
			$time = 3600*24;
			$d1 = date('Y-m-d', (strtotime($date) - $time*1));
			$d2 = date('Y-m-d', (strtotime($date) - $time*2));
			$d3 = date('Y-m-d', (strtotime($date) - $time*8));
			$url = array();
			array_push($url,C('DOMAIN_PC')."/Home/Report/quickName");
			array_push($url,C('DOMAIN_PC')."/Home/Report/bigAccount");
			array_push($url,C('DOMAIN_PC')."/Home/Report/olsaSchedule");
			$arrs = async_get_url($url);
			$quick = json_decode($arrs[0],true);
			$final = array(
					'5417966427'=>array(
						'info'=>array(),
						'children'=>array(),
						'ids'=>array(),
						'cost'=>array(
							'now'=>0,
							'yesterday'=>0,
							'beforeyesterday'=>0,
							'lastweek'=>0
						)
						
					),	//出口易
					'2637898221'=>array(
						'info'=>array(),
						'children'=>array(),
						'ids'=>array(),
						'cost'=>array(
							'now'=>0,
							'yesterday'=>0,
							'beforeyesterday'=>0,
							'lastweek'=>0
						)
				
					), //国内
					
					'9639408925'=>array(
						'info'=>array(),
						'children'=>array(),
						'ids'=>array(),
						'cost'=>array(
							'now'=>0,
							'yesterday'=>0,
							'beforeyesterday'=>0,
							'lastweek'=>0
						)
					) //大客
				);
				
				
				$t = json_decode($arrs[1],true);
				$bigAccount = index_array3($t,'account_id');
				
				foreach($final as $k=>$v){
					$list = M('',NULL,$this->connectReports)->query("SELECT account FROM ads_olsa WHERE apid = {$k}");
					$list = index_array3($list,'account');
					$final[$k]['count']= count($list);
					$final[$k]['children']['all']= $list;
					$final[$k]['children']['now'] = array_values(array_diff($final[$k]['children']['all'],$bigAccount));
					$final[$k]['ids']['all'] = implode(',',$final[$k]['children']['all']);
					$final[$k]['ids']['now'] = implode(',',$final[$k]['children']['now']);
				}
				
				$now = array();
				foreach($final as $k=>$v){
					
					$sql = "SELECT SUM(COST) as cost  FROM `aw_reportcampaign` WHERE Day   LIKE '%{$d1}%' AND ACCOUNT_ID 		IN({$final[$k][ids][now]})";
					$rs = M('',NULL,$this->connectReports)->query($sql);
					$final[$k]['cost']['now'] = $rs[0]['cost'];
					
					$sql = "SELECT SUM(COST) as cost  FROM `aw_reportcampaign` WHERE Day   LIKE '%{$d1}%' AND ACCOUNT_ID IN({$final[$k][ids][all]})";
					$rs = M('',NULL,$this->connectReports)->query($sql);
					$final[$k]['cost']['yesterday'] = $rs[0]['cost'];
					
					$sql = "SELECT SUM(COST) as cost  FROM `aw_reportcampaign` WHERE Day   LIKE '%{$d2}%' AND ACCOUNT_ID IN({$final[$k][ids][all]})";
					$rs = M('',NULL,$this->connectReports)->query($sql);
					$final[$k]['cost']['beforeyesterday'] = $rs[0]['cost'];
					
					$sql = "SELECT SUM(COST) as cost  FROM `aw_reportcampaign` WHERE Day   LIKE '%{$d3}%' AND ACCOUNT_ID IN({$final[$k][ids][all]})";
					$rs = M('',NULL,$this->connectReports)->query($sql);
					$final[$k]['cost']['lastweek'] = $rs[0]['cost'];
					
					
					$final[$k]['per1'] = ($final[$k]['cost']['yesterday'] / $final[$k]['cost']['beforeyesterday']) - 1;
					
					$final[$k]['per2'] = ($final[$k]['cost']['yesterday'] / $final[$k]['cost']['lastweek']) - 1;
					
					
			}
			$sum = array(
				'now'=>0,
				'yesterday'=>0,
				'beforeyesterday'=>0,
				'lastweek'=>0,
				'per1'=>0,
				'per2'=>0,
			);
			
			///pr(array($final));
			foreach($final as $k=>$v){
				$sum['now']+= $v['cost']['now'];
				$sum['yesterday']+= $v['cost']['yesterday'];
				$sum['beforeyesterday']+= $v['cost']['beforeyesterday'];
				
				$sum['lastweek']+= $v['cost']['lastweek'];
			
				$sum['per1'] = ($sum['yesterday'] / $sum['beforeyesterday']) - 1;
				$sum['per1'] =	(sprintf("%.2f",$sum['per1'])*100);	
					
				$sum['per2'] = ($sum['yesterday'] / $sum['lastweek']) - 1;
				$sum['per2'] =	(sprintf("%.2f",$sum['per2'])*100);	
				
			}
				
				
				foreach($final as $k=>$v){
					$now[$k]['count'] = $v['count'];
					$now[$k]['info'] = array('cid'=>filter_mcc($k),'name'=>$quick[$k]);
					$now[$k]['cost'] = $v['cost'];	
					$now[$k]['per1'] = (sprintf("%.2f",$v['per1'])*100);	
					$now[$k]['per2'] = (sprintf("%.2f",$v['per2'])*100);	
					
				}
				$f['sum'] = $sum;
				$f['date'] = array($d1,$d2,$d3);
				$f['list'] = $now;
				$f['schedule'] = json_decode($arrs[2],true);
				
				echo json_encode($f);
			
		}
			/*$this->assign('d1',$d1);
			$this->assign('d2',$d2);
			$this->assign('d3',$d3);
		
			$this->assign('list',$now);*/
			
			
			
			
			
		
	}
	
	
	public function olsa2(){
			$d1 = date('Y-m-d', strtotime('-1 day'));
			$d2 = date('Y-m-d', strtotime('-2 day'));
			$d3 = date('Y-m-d', strtotime('-8 day'));

			
/*			$a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");
			$a2=array("e"=>"red","f"=>"green","g"=>"blue","d"=>"yellows");
			
			

$result=array_diff($a1,$a2);
print_r($result);

exit;*/
			
			
			
			//echo $d4;
		//	exit;
			
			
			$final2 = array(
				'541-796-6427'=>array(
				
				),	//出口易
				'263-789-8221'=>array(), //国内
				'963-940-8925'=>array() //大客
			);
			
			$final = array(
				'5417966427'=>array(
					'info'=>array(),
					'children'=>array(
						'all'=>array(),
						'now'=>array(),
						'yesterday'=>array(),
						'beforeyesterday'=>array(),
						'lastweek'=>array()
					),
					'final'=>array(
						//'all'=>array(),
						'now'=>array(),
						'yesterday'=>array(),
						'beforeyesterday'=>array(),
						'lastweek'=>array()
					),
					'cost'=>array(
						//'all'=>array(),
						'now'=>0,
						'yesterday'=>0,
						'beforeyesterday'=>0,
						'lastweek'=>0
					)
					
				),	//出口易
				'2637898221'=>array(
					'info'=>array(),
					'children'=>array(
						'all'=>array(),
						'now'=>array(),
						'yesterday'=>array(),
						'beforeyesterday'=>array(),
						'lastweek'=>array()
					),
					'final'=>array(
						//'all'=>array(),
						'now'=>array(),
						'yesterday'=>array(),
						'beforeyesterday'=>array(),
						'lastweek'=>array()
					),
					'cost'=>array(
						//'all'=>array(),
						'now'=>0,
						'yesterday'=>0,
						'beforeyesterday'=>0,
						'lastweek'=>0
					)
			
				), //国内
				
				'9639408925'=>array(
					'info'=>array(),
				
					'children'=>array(
						'all'=>array(),
						'now'=>array(),
						'yesterday'=>array(),
						'beforeyesterday'=>array(),
						'lastweek'=>array()
					),
					'final'=>array(
						//'all'=>array(),
						'now'=>array(),
						'yesterday'=>array(),
						'beforeyesterday'=>array(),
						'lastweek'=>array()
					),
					'cost'=>array(
						//'all'=>array(),
						'now'=>0,
						'yesterday'=>0,
						'beforeyesterday'=>0,
						'lastweek'=>0
					)
				) //大客
				
			);
			
			
			foreach($final as $k=>$v){
				$list = M('',NULL,$this->connectLocalhostReports)->query("SELECT account FROM ads_olsa WHERE apid = {$k}");
				$list = index_array3($list,'account');
				$final[$k]['children']['all'] = $list;
			}
			$url = array();
			array_push($url,URL."Home/Report/getHasCostAccount?day=yesterday");
			array_push($url,URL."Home/Report/getHasCostAccount?day=beforeyesterday");
			array_push($url,URL."Home/Report/bigAccount");
			array_push($url,URL."Home/Report/quickName");
			
			
			
			$arrs = async_get_url($url);
			$quick = json_decode($arrs[3],true);
			
			$temp = array(
				'yesterday'=>array(),
				'beforeyesterday'=>array()
			);
			
			$temp2 = array(
				'yesterday'=>array(),
				'beforeyesterday'=>array()
			);
			
			
			
			
			
			$bigAccount = array();
			
			$temp['yesterday'] =  json_decode($arrs[0],true);
			
			$temp2['yesterday']= index_array3($temp['yesterday'],'account_id');
			
			$temp['beforeyesterday'] = json_decode($arrs[1],true);
			
			$temp2['beforeyesterday']= index_array3($temp['yesterday'],'account_id');
			
			$costArr = array(
				'yesterday'=>each_array($temp['yesterday'],'account_id','sum_cost'),
				'beforeyesterday'=>each_array($temp['beforeyesterday'],'account_id','sum_cost'),
			);
			
			$t = json_decode($arrs[2],true);
			$bigAccount = index_array3($t,'account_id');
			
			//
			
			foreach($final as $k=>$v){
				
				
				$final[$k]['children']['yesterday'] = array_values(array_intersect($final[$k]['children']['all'],$temp2['yesterday']));
				
				$final[$k]['children']['beforeyesterday'] = array_values(array_intersect($final[$k]['children']['all'],$temp2['beforeyesterday']));
				
				//昨天有消耗的账户要去除  月季度 > 9w的账户
				
				//昨天所有消耗的账户 与 本季度消耗 < 9w的去交集
				$final[$k]['children']['now'] = array_values(array_diff($final[$k]['children']['yesterday'],$bigAccount));
				
				//$final[$k]['children']['now'] = $final[$k]['children']['yesterday'];
				
				
				//$final[$k]['ids']['now'] = implode(',',$final[$k]['children']['now']);
				//$final[$k]['ids']['yesterday'] = implode(',',$final[$k]['children']['yesterday']);
				//$final[$k]['ids']['beforeyesterday'] = implode(',',$final[$k]['children']['beforeyesterday']);
				$final[$k]['children']['all'] = array();
				$final[$k]['cost'] = array();
			}
			
			$now = array();
			
			
			foreach($final as $k=>$v){
				
				//$final[$k]['children'] = NULL;
				
				
			
			}
			//$final = NULL;
			foreach($final as $k=>$v){
				//foreach($v as $v2){
					$tmp = array_slice($v['children'],1,5);
					
					$now[$k]['children'] = $tmp;	
					$now[$k]['cost'] = $v['cost'];
					$now[$k]['final'] = $v['final'];
					
					$now[$k]['info'] = array('cid'=>filter_mcc($k),'name'=>$quick[$k]);
					//$now[$k]['cost'] = array_values($v,-1));	
				//}
			}
			foreach($now as $k=>$v){
			}
			
		//pr(array($now));
			$this->assign('d1',$d1);
			$this->assign('d2',$d2);
			$this->assign('d3',$d3);
		
			$this->assign('list',$now);
			$this->display();
			exit;
			
			
	}
	
	//转换数据
	public function tools(){
		if(IS_POST){
			$idsArr = $_POST['ids'];
			$d = explode('-',$_POST['date']);
			if(count($d) == 1){
				$d[1] = $d[0];	
			}
			$arrs = array(
				'all'=>array(),		//查看总量
				'isGa'=>array(), 	//有效Ga总量
				'notGa'=>array(), 	//无效Ga总量
				'isAds'=>array(),		//有效Ads总量
				'notAds'=>array()		//无效Ads总量
			);
			foreach($idsArr as $v){
				array_push($arrs['all'],str_replace('-','',$v));
			}
			
			$ids = implode(',',$arrs['all']);
			$allArr= array();
			$allArr2= array();
			$notArr= array();
			$isArr = array();
			foreach($idsArr as $v){
				array_push($allArr,str_replace('-','',$v));
			}
			 $sql2 = "SELECT * FROM (SELECT SUM(COST) AS COST ,   SUM(ALL_CONVERSIONS) AS AC , ACCOUNT_ID FROM `aw_reportcampaign` WHERE Day   BETWEEN '{$d[0]}' AND '{$d[1]}' GROUP BY ACCOUNT_ID) AS NEW WHERE ACCOUNT_ID IN({$ids})";
			 
			 $list = M('',NULL,$this->connectReports)->query($sql2);
			 $cpa = array();
			 if(is_array($list) && !empty($list)){
					foreach($list as $v){
						$cpa[$v['account_id']] = array(intval($v['ac']),sprintf("%.2f",$v['cost'] / $v['ac']));
				}
			}
			$quick = array(
				'mcc'=>quick_mcc_name($this->connectReports,$ids),
				'ua'=>get_ua($this->connectReports,$ids),
				'conversions'=>get_conversions($this->connectReports,$ids)
			);
			$arrs['isGa'] = array_keys($quick['ua']);
			$arrs['notGa'] = array_diff($arrs['all'],$arrs['isGa']);
			
			if(is_array($quick['conversions']) && !empty($quick['conversions'])){
				foreach($quick['conversions'] as $k => $v){
					if($v[1] == 0){
						array_push($arrs['notAds'],$k);	
					}
				}
			}
			$arrs['isAds'] = array_keys($quick['conversions']);
			$final['list'] = array();
			if(is_array($arrs['notGa']) && !empty($arrs['notGa'])){
				foreach($arrs['notGa'] as $v){
					$ar = array(
						'id'=>'#'.++$i,
						'name'=>$quick['mcc'][$v],
						'cid'=>filter_mcc($v),
						'gaid'=>'',
						'ga_status'=>0,
						'conversions_name'=>($quick['conversions'][$v][0] == NULL)?'<span style="color:#ccc">N/A</span>':$quick['conversions'][$v][0],
						'conversions'=>$quick['conversions'][$v][1],
						'conversions_value'=>($cpa[$v][0]==NULL)?0:$cpa[$v][0],
						'cpa'=>($cpa[$v][1]==NULL  || $cpa[$v][1]==0.00 )?'<span style="color:#ccc">N/A</span>':$cpa[$v][1]
					);
					array_push($final['list'],$ar);
				}
			}
			
			if(is_array($arrs['isGa']) && !empty($arrs['isGa'])){
				foreach($arrs['isGa'] as $v){
					$ar = array(
						'id'=>'#'.++$i,
						'name'=>$quick['mcc'][$v],
						'cid'=>filter_mcc($v),
						'gaid'=>$quick['ua'][$v],
						'ga_status'=>1,
						'conversions_name'=>$quick['conversions'][$v][0],
						'conversions'=>$quick['conversions'][$v][1],
						'conversions_value'=>($cpa[$v][0]==NULL)?0:$cpa[$v][0],
						'cpa'=>($cpa[$v][1]==NULL || $cpa[$v][1]==0.00)?'<span style="color:#ccc">N/A<span>':$cpa[$v][1]
					);
					array_push($final['list'],$ar);
				}
			}
			
			$per1 = sprintf("%.2f",  ( count($arrs['notGa']) / count($final['list']) * 100));
			$per2 = sprintf("%.2f",	( count($arrs['notAds']) / count($final['list']) * 100));
			$final['count'] =array(
					count($arrs['notGa'])+count($arrs['notAds']),
					count($arrs['notGa']).'（'.$per1.'%）',
					count($arrs['notAds']).'（'.$per2.'%）',
				);
		
			echo json_encode($final);
		}else{
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}
	}
	
	
	protected function alertedTop($type,$ids){
		
		switch($type){
			case 0:
				$w = 'account_cost_d7 < 30';
			break;
			
			case 1:
				$w = 'account_cost_d7 >= 30 AND account_cost_d7 < 100';
			break;
			
			case 2:
				$w = 'account_cost_d7 >= 100 AND account_cost_d7 < 300';
			break;
			
			case 3:
				$w = 'account_cost_d7 >= 300 AND account_cost_d7 <= 700';
			break;
			
			case 4:
				$w = 'account_cost_d7 >700';
			break;	
			
		}
		$sql = "SELECT COUNT(*) AS c ,SUM(new.point) as point , 
		
	      if(  (SUM(new.point) / COUNT(*)) is null,'N/A', FLOOR(SUM(new.point) / COUNT(*)) )
		
		
		 AS AG FROM
					(
					SELECT a.account_id,b.point FROM
					(
					SELECT account_id FROM ads_account_rating_data WHERE {$w} AND account_id IN({$ids})
					) AS a INNER JOIN 
					(
					SELECT point,account_id FROM ads_account_rating
					) AS b 
				
					ON a.account_id =b.account_id
				
				) AS new";
		$rs =  M('',NULL,$this->connectReports)->query($sql);
		return $rs[0];
	}
	//账户评级
	public function alertedList(){
		if(IS_GET){
			$this->assign('children',json_encode($this->selChildren));
			$this->display();
		}else{
			$all = array();
			$idsArr = $_POST['ids'];
			foreach($idsArr as $v){
				array_push($all,'"'.str_replace('-','',$v).'"');
			}
			$ids = implode(',',$all);
			$type = array(
				'日均消耗<30'=>$this->alertedTop(0,$ids),
				'[30,100)'=>$this->alertedTop(1,$ids),
				'[100,300)'=>$this->alertedTop(2,$ids),
				'[300,700]'=>$this->alertedTop(3,$ids),
				'>700'=>$this->alertedTop(4,$ids),
			);
			
			$sql = "SELECT  * FROM ads_account_rating WHERE account_id IN ($ids) ORDER BY point DESC";
			$list2 = M('ads_mcc',NULL,$this->connectReports)->where("account_id IN ($ids)")->getField('account_id,name',true);
			$list = M('',NULL,$this->connectReports)->query($sql);
			foreach($list as $k=>$v){
				$list[$k]['name'] =  $list2[$v['account_id']];
			}
			$arr['top'] = $type;
			$arr['list'] = $list;
			echo json_encode($arr);
		}
	}
}