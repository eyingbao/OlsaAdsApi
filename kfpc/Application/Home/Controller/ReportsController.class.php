<?php
namespace Home\Controller;
use Think\Controller;
//广告系列控制器
class ReportsController extends Controller {
	protected $connectReports;
	
	protected $pieColor = array("#4285f5","#db4436","#fbc12c","#0e9d58","#ab46bd"   ,"#d7d400","#e13072","#229aff");
	
	protected $histogramColor = array("#418bca");
	
	 public function _initialize(){
		 $this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
	 }
	
	public function index(){
		$list = array();
		$year = date('Y');
		$nowYear = $year; //当前年数
		$prevYear = $year;//去年
		$nextYear = $year; //下年

		//找出当月
		$month = date('m');
		if($month < 10){
			//$month = substr($month,1,1);	
		}
		$prevMonth = intval($month) -1;
		if($prevMonth == 0){
			$prevMonth = 12;	
			$year = $year-1;
		}
		$prevPrevMonth = intval($prevMonth) -1;
		$nextMonth = intval($month) +1;
		if($prevMonth < 10){
			$prevMonth='0'.$prevMonth;
		}
		if($prevPrevMonth < 10){
			$prevPrevMonth='0'.$prevPrevMonth;
		}
		$d1 = $year.'-'.$prevMonth.'-01';
		$d2 = $prevYear.'-'.$month.'-01';
		$d3 = "{$year}-{$prevPrevMonth}-01"; 
		$d4 = "{$year}-{$prevMonth}-01";
		
		$d5 = "{$year}-{$prevMonth}-01";
		$d6 = "{$year}-{$prevMonth}-".getMonthLastDay($prevMonth,$year);
		
		$cid = $_GET['cid'];
		$cid = str_replace('-','',$cid);
		if(!intval($cid)){
			exit;
		}
		$request = array('top','campaign','daily','keywords','geo','customerService','customer','blance');
		foreach($request  as $v){
			array_push($list,C('DOMAIN_PC').'/Ads/Index/'.$v.'?cid='.$cid.'&d1='.$d1.'&d2='.$d2);
		}
		array_push($list,C('DOMAIN_PC').'/Ads/Index/top?cid='.$cid.'&d1='.$d3.'&d2='.$d4);
		
		$arr = async_get_url($list);
		if($_GET['cid'] == $bl){}
		$rs['top'] = array();
		$rs['campaign'] = array();
		$rs['daily'] = array();
		$rs['keywords']= array();
		$rs['geo']= array();
		foreach($arr as $k=>$v){
			if($k == 0){
				$rs['top']  = json_decode($v,true);
			}
			if($k == 1){
				$rs['campaign']  = json_decode($v,true);
				if(count($rs['campaign']['table']) <= 10){
					$rs['campaign']['class_name'] = 'campaign_tables_1_10';
				}else if( count($rs['campaign']['table']) > 10 && count($rs['campaign']['table']) <= 15) {
					$rs['campaign']['class_name'] = 'campaign_tables_10_15';
				}else if(count($rs['campaign']['table']) > 15){
					$rs['campaign']['class_name'] = 'campaign_tables_15_20';
				}
				$tt = 0;
				foreach($rs['campaign']['table'] as $k=> $v){
					$tt+=($v['impressions'] * $v['average_position']);
				}
				
				$num = $tt / $rs['campaign']['total']['impressions'];
				$rs['campaign']['total']['average_position'] =sprintf("%.1f",sprintf("%.2f",substr(sprintf("%.3f", $num), 0, -2))); 
				
				foreach($rs['campaign']['histogramList'] as $k=>$v){
					$rs['campaign']['histogram']['table'][$v['campaign_name']] = array($this->pieColor[$k],intval($v['cost']));
				}
				
				foreach($rs['campaign']['pieList'] as $k=>$v){
					$rs['campaign']['pie']['table'][$v['campaign_name']] = array($this->pieColor[$k],intval($v['all_conversions']));
				}
				
				$m = 0;
				arsort($rs['campaign']['pie']['table1']);
				foreach($rs['campaign']['pie']['table1'] as $k2=>$v2){
					$rs['campaign']['pie']['table'][$k2] = array($this->pieColor[$m],intval($v2));
					$m++;
				}
				$temp = array();
				$temp2 = array();
				$i = 0;
				foreach($rs['campaign']['pie']['table'] as $k2=>$v2){
					array_push($temp,array(
						'value' =>$v2[1],
						'color'=> $this->pieColor[$i],
						'highlight'=> $this->pieColor[$i],
						'label'=> $k2
					));
					$i++;
				}
				$temp2['labels'] = array();
				$temp2['datasets'][0] = array(
					'label'=> "histogram",
					'fillColor'=> $this->histogramColor[0],
					'strokeColor'=>  $this->histogramColor[0],
					'highlightFill'=> $this->histogramColor[0],
					'highlightStroke'=>  $this->histogramColor[0],
					'data'=>array()
				);
				foreach($rs['campaign']['histogram']['table'] as $k2=>$v2){
					array_push($temp2['labels'],$k2);
					array_push($temp2['datasets'][0]['data'],intval($v2[1]));
					arsort($temp2['datasets'][0]['data']);
					
				}
				$rs['campaign']['pie']['data'] =  json_encode($temp);
				$rs['campaign']['histogram']['data'] = json_encode($temp2);
				
				if($_GET['cid'] == $bl){
					
				}
			}
			
			if($k == 2){
				$rs['daily']  = json_decode($v,true);
				$temp2['labels'] = array();
				$temp2['datasets'][0] = array(
					'label'=> "line1",
					'fillColor'=> '#fff',
					'strokeColor'=>  $this->pieColor[0],
					'pointColor'=> $this->pieColor[0],
					'pointStrokeColor'=>"#fff",
					'pointHighlightFill'=>'#fff',
					'pointHighlightStroke'=>'rgba(42, 96, 169,1)',
					'data'=>array()
				);
				
				$temp3['labels'] = array();
				$temp3['datasets'][0] = array(
					'label'=> "line2",
					'fillColor'=> '#fff',
					'strokeColor'=>  $this->pieColor[0],
					'pointColor'=> $this->pieColor[0],
					'pointStrokeColor'=>"#fff",
					'pointHighlightFill'=>'#fff',
					'pointHighlightStroke'=>'rgba(42, 96, 169,1)',
					'data'=>array()
				);
				
				$y  = date('Y',time());
				$m  = date('m',time());
				$m = '03';
				$rs['daily']['total']['avg_cost'] = sprintf("%.2f",$rs['daily']['total']['cost'] /  31);
				foreach($rs['daily']['table'] as $k2=>$v2){
					$d = str_replace($y.'-','',$v2['day']);
					$d = str_replace($m.'-','',$d);
					array_push($temp2['labels'],$d);
					array_push($temp2['datasets'][0]['data'],$v2['cost']);
					array_push($temp3['labels'],$d);
					array_push($temp3['datasets'][0]['data'],$v2['all_conversions']);
				}
				$rs['daily']['line2']['data'] = json_encode($temp3);
				$rs['daily']['line1']['data'] = json_encode($temp2);
			}
			if($k == 3){
				$kw = json_decode($v,true);
				$rs['keywords']['table']  = $kw['table'];
				if(is_array($rs['keywords']['table']) && !empty($rs['keywords']['table'])){
					foreach($rs['keywords']['table'] as $k=> $v){
						if($v['keyword_match_type'] =='Phrase'){
							$rs['keywords']['table'][$k]['criteria'] = '" '.$rs['keywords']['table'][$k]['criteria'].' "';
						}else if($v['keyword_match_type'] =='Exact'){
							$rs['keywords']['table'][$k]['criteria'] = '[ '.$rs['keywords']['table'][$k]['criteria'].' ]';
						}
					}
				}
				$kw['histogram1List'] = $rs['keywords']['table'];
				if(is_array($kw['histogram2List']) && !empty($kw['histogram2List'])){
					foreach($kw['histogram2List'] as $k=> $v){
						if($v['keyword_match_type'] =='Phrase'){
							$kw['histogram2List'][$k]['criteria'] = '" '.$kw['histogram2List'][$k]['criteria'].' "';
						}else if($v['keyword_match_type'] =='Exact'){
							$kw['histogram2List'][$k]['criteria'] = '[ '.$kw['histogram2List'][$k]['criteria'].' ]';
						}
					}
				}
				if(count($rs['keywords']['table']) <= 10){
					$rs['keywords']['class_name'] = 'keywords_tables_1_10';
				}else if( count($rs['keywords']['table']) > 10 && count($rs['keywords']['table']) <= 15) {
					$rs['keywords']['class_name'] = 'keywords_tables_10_15';
				}else if(count($rs['keywords']['table']) > 15){
					$rs['keywords']['class_name'] = 'keywords_tables_15_20';
				}
				$top5 = array_slice($rs['keywords']['table'],0,5);
				foreach($top5 as $v){
					$rs['keywords']['top5'] .= $v['criteria'].'，';
				}
				$histogram1 = array();
				$temp2['labels'] = array();
				$temp2['datasets'][0] = array(
					'label'=> "histogram",
					'fillColor'=> $this->histogramColor[0],
					'strokeColor'=>  $this->histogramColor[0],
					'highlightFill'=> $this->histogramColor[0],
					'highlightStroke'=>  $this->histogramColor[0],
					'data'=>array()
				);
				$temp3['labels'] = array();
				$temp3['datasets'][0] = array(
					'label'=> "histogram",
					'fillColor'=> $this->histogramColor[0],
					'strokeColor'=>  $this->histogramColor[0],
					'highlightFill'=> $this->histogramColor[0],
					'highlightStroke'=>  $this->histogramColor[0],
					'data'=>array()
				);
				foreach($kw['histogram1List'] as $k2=>$v2){
					array_push($temp2['labels'],$v2['criteria']);
					array_push($temp2['datasets'][0]['data'],intval($v2['cost']));
					
				}
				foreach($kw['histogram2List'] as $k2=>$v2){
					array_push($temp3['labels'],$v2['criteria']);
					array_push($temp3['datasets'][0]['data'],intval($v2['all_conversions']));
				}
				$rs['keywords']['histogram1']['data'] = json_encode($temp2);
				$rs['keywords']['histogram2']['data'] = json_encode($temp3);
				$rs['keywords']['top5'] = rtrim($rs['keywords']['top5'],'，');
			}
			
			//geo
			if($k == 4){
				$geo = json_decode($v,true);
				$rs['geo']['table']  =$geo[0];
				if(count($rs['geo']['table']) <= 10){
					$rs['geo']['class_name'] = 'geo_tables_1_10';
				}else if( count($rs['geo']['table']) > 10 && count($rs['geo']['table']) <= 15) {
					$rs['geo']['class_name'] = 'geo_tables_10_15';
				}else if(count($rs['geo']['table']) > 15){
					$rs['geo']['class_name'] = 'geo_tables_15_20';
				}
				$top5 = array_slice($geo[0],0,5);
				$j = '';
				foreach($top5 as $v){
					$rs['geo']['top5'] .= $j.$v['cname'];
					$j='，';
				}
				$temp = array();
				$temp2 = array();
				$i = 0;
				$j = 0;
				$rs['geo']['pie1'] = array_slice($rs['geo']['table'],0,5);
				$sumCost = 0;
				foreach($rs['geo']['pie1'] as $k2=>$v2){
					$sumCost+=intval($v2['cost']);
				}
				
				foreach($rs['geo']['pie1'] as $k2=>$v2){
					$rs['geo']['pie1']['table'][$v2['cname']] = array($this->pieColor[$k2],intval(($v2['cost']/$sumCost) *100));
					
				}
				foreach($rs['geo']['pie1']['table'] as $k2=>$v2){
						array_push($temp,array(
							'value' =>$v2[1],
							'color'=>$v2[0],
							'highlight'=> $v2[0],
							'label'=> $k2
						));
						$i++;
					}
				foreach($geo[1] as $k2=>$v2){
					$rs['geo']['pie2']['table'][$v2['cname']] = array($this->pieColor[$k2],intval($v2['all_conversions']));
					
				}
				foreach($rs['geo']['pie2']['table'] as $k2=>$v2){
						array_push($temp2,array(
							'value' =>$v2[1],
							'color'=>$v2[0],
							'highlight'=> $v2[0],
							'label'=> $k2
						));
						$j++;
					}
				$rs['geo']['pie1']['data'] =  json_encode($temp);
				$rs['geo']['pie2']['data'] =  json_encode($temp2);
			}
			if($k == 5){
				
				$rs['customerService']  = json_decode($v,true);
				
				//pr(array($rs['customerService']));
			}
			if($k == 6){
				$rs['customer']  = json_decode($v,true);
			}
			if($k == 7){
				$rs['blance']  = json_decode($v,true);
			}
			if($k == 8){
				$rs['top2']  = json_decode($v,true);
			}
		}
		
		if($_GET['cid'] == $bl){
			//pr(array($rs['campaign']));
		}
		
		$rs['compareds']['cost'] = ( ($rs['top2']['cost']) > 0 )?sprintf("%.2f",(($rs['top']['cost'] - $rs['top2']['cost']) / $rs['top2']['cost'])*100):'N/A';
		$rs['compareds']['clicks'] = ( ($rs['top2']['clicks']) > 0 )?sprintf("%.2f",(($rs['top']['clicks'] - $rs['top2']['clicks']) / $rs['top2']['clicks'])*100):'N/A';
		$rs['compareds']['impressions'] = ( ($rs['top2']['impressions']) > 0 )?sprintf("%.2f",(($rs['top']['impressions'] - $rs['top2']['impressions']) / $rs['top2']['impressions'])*100):'N/A';
		$rs['compared'] = array();
		foreach($rs['compareds'] as $k=>$v){
			if($v != 'N/A'){
				if($v > 0){
					$rs['compared'][$k][0] = '1';
					
				}else{
					$rs['compared'][$k][0] = '0';
					
				}
				$rs['compared'][$k][1] =$v;
			}else{
				$rs['compared'][$k][0] = 'N/A';
			}
		}
		
		
	//	pr(array($rs));
		
		//
		$year = date('Y',time());
		$this->assign('cid',$cid);
		$this->assign('year',$year);
		$this->assign('d1',$d5);
		$this->assign('d2',$d6);
		$this->assign('rs',$rs);
		$this->display();
	}
}