<?php
namespace Ads\Controller;
use Think\Controller;
//账户评级控制器
class SuggestController extends Controller{
	protected $d1;
	protected $d2;
	protected $cid;
	//（分值标准）3优 2良 1差
	protected $sta =  array(
		'costErr'=>array(0,5,10), 		//（账户端）消耗异常
		
		'renewals'=>array(0,5,10),	//（账户端）续费提醒
		
		'budgetErr'=>array(0,5,8),	//（账户端）日预算预警
		
		'convers'=>array(0,5,8),		//（广告系列）转化次数对比
		
		'delivery'=>array(0,5,8),		//（广告系列）投放渠道
		
		'campaignCtr'=>array(0,5,10), //（广告系列）平均点击率
		
		'kwPoint'=>array(0,10,15),	//（广告组）关键词质量得分
		
		'kwCount'=>array(0,5,8),	//（广告组）关键词数量
		
		'ref'=>array(0,0,3),	//（广告组）广告语状态
		
		'adCount'=>array(0,0,3),	//（广告组）广告语条数
		
		'BounceRate'=>array(0,3,5),	//（GA网站数据）跳出率
		
		'Loadtime'=>array(0,5,7),	//（GA网站数据）加载时间
		
		'Dwelltime'=>array(0,3,5),	//（GA网站数据）停留时间
		
		'crmServerCount'=>array(0,3,5),	//（联系频次）CRM库服务记录
	
	);
	protected $connectReports;
	protected $connectCrm;
	
	public function _initialize(){
		$this->d1 = $_GET['d1'];
		$this->d2 = $_GET['d2'];
		$this->cid = $_GET['cid'];
		$this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
		$this->connectCrm =  'mysql://'.C('DB_USER_CRM').':'.C('DB_PWD_CRM').'@'.C('DB_HOST_CRM').'/'.C('DB_NAME_CRM').'#utf8';
		//parent::_initialize();
	}
	
	
	public function costErr(){
		$cid = $_GET['cid'];
		$sql = "SELECT account_cost_err  FROM ads_account_rating_data WHERE account_id = ".$cid;
		$account_cost_err = M('',NULL,$this->connectReports)->query($sql);
		$cost = $account_cost_err[0]['account_cost_err'];		
		if($cost < -100){
			//$arr["suggest"] = '账户是否存在异常，1.广告拒等；2.出价变更；3.流量降低；4.预算变更；';
			//$arr["comment"] = '请检查一下您的账户了解波动情况';
			$cost = '<span class="ll1">'.$cost.'</span>';
			$arr["range"] =$arr["range2"] = $cost;		
			$arr["grade"]  = '<span class="l1">差</span>'; 
			$arr['point'] = '<span class="ll1">'.$this->sta['costErr'][0].'</span>';
			$arr["point2"]  = $this->sta['costErr'][0];
		}else if($cost > 100){
			//$arr["range"] = '消耗波动日均 > 100';
			//$arr["range2"] = '>100';
			$cost = '<span class="ll3">'.$cost.'</span>';
			$arr["range"] =$arr["range2"] = $cost;
			$arr["grade"]  = '<span class="l3">优</span>'; 
			$arr['point'] = '<span class="ll3">+'.$this->sta['costErr'][2].'</span>';
			$arr["point2"]  = $this->sta['costErr'][2];
 		}else{
			//$arr["range"] = $arr["range2"] =   '[-100,100]';
			$cost = '<span class="ll2">'.$cost.'</span>';
			$arr["range"] =$arr["range2"] = $cost;
			$arr["grade"]  = '<span class="l2">良</span>'; 
			$arr['point'] = '<span class="ll2">+'.$this->sta['costErr'][1].'</span>';
			$arr["point2"]  = $this->sta['costErr'][1];
		}
		//$arr["sql1"] = $sql1;
		//$arr["sql2"] = $sql2;
		echo json_encode($arr,JSON_UNESCAPED_UNICODE);
	}
	
	//（账户端）消耗异常
	public function costErr1(){
		$cid = $_GET['cid'];
		//上7天
		$d1 = date("Y-m-d",strtotime("-1 day"));
		$d7 = date("Y-m-d",strtotime("-7 day"));
		
		//上上7天
		$d8 = date("Y-m-d",strtotime("-8 day"));
		$d15 = date("Y-m-d",strtotime("-14 day"));
		
		$sql1= "SELECT  SUM(COST) AS COST   FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} AND DAY BETWEEN '{$d7} 20:00:00' AND '{$d1} 20:00:00'";
		$sql2= "SELECT  SUM(COST) AS COST   FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid} AND DAY BETWEEN '{$d15} 20:00:00' AND '{$d8} 20:00:00'";
		$cost = M('','',$this->connectReports)->query($sql1);
		$cost1 = floor($cost[0]['cost']/7);
		$cost =M('','',$this->connectReports)->query($sql2);
		$cost2 = floor($cost[0]['cost']/7);
		$cost = $cost1 - $cost2;
		if($cost < -100){
			//$arr["suggest"] = '账户是否存在异常，1.广告拒等；2.出价变更；3.流量降低；4.预算变更；';
			//$arr["comment"] = '请检查一下您的账户了解波动情况';
			$cost = '<span class="ll1">'.$cost.'</span>';
			$arr["range"] =$arr["range2"] = $cost;
			$arr["grade"]  = '<span class="l1">差</span>'; 
			$arr['point'] = '<span class="ll1">'.$this->sta['costErr'][0].'</span>';
			$arr["point2"]  = $this->sta['costErr'][0];
		}else if($cost > 100){
			//$arr["range"] = '消耗波动日均 > 100';
			//$arr["range2"] = '>100';
			$cost = '<span class="ll3">'.$cost.'</span>';
			$arr["range"] =$arr["range2"] = $cost;
			$arr["grade"]  = '<span class="l3">优</span>'; 
			$arr['point'] = '<span class="ll3">+'.$this->sta['costErr'][2].'</span>';
			$arr["point2"]  = $this->sta['costErr'][2];
 		}else{
			//$arr["range"] = $arr["range2"] =   '[-100,100]';
			$cost = '<span class="ll2">'.$cost.'</span>';
			$arr["range"] =$arr["range2"] = $cost;
			$arr["grade"]  = '<span class="l2">良</span>'; 
			$arr['point'] = '<span class="ll2">+'.$this->sta['costErr'][1].'</span>';
			$arr["point2"]  = $this->sta['costErr'][1];
		}
		echo json_encode($arr,JSON_UNESCAPED_UNICODE);
	}
	
	
	
	//（账户端）续费提醒
	public function renewals(){
		$cid = $_GET['cid'];
		$day2  = date('Y-m-d',time());
		$rsa =  M('ads_budget',NULL,$this->connectReports)->where("cid = '{$cid}'")-> find();
		$day1= str_replace('','',$rsa['start_date']);
		$blance =  $rsa['bce'];
		$blance = ($blance<=0)?0:$blance;
		$day1 = date("Y-m-d",time());
		$day7 = date("Y-m-d",strtotime("-7 day"));
		$cost = M('reportcampaign','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$day7} 20:00:00' AND '{$day1} 20:00:00'")->SUM('COST');
		$cost = sprintf("%.2f",$cost / 7) ;
		$day = floor($blance/$cost);
		$day = ($day<=0)?0:$day;
		$arr["range"] = '';
		$arr["suggest"] ='';
		$arr["comment"] ='';
		$arr["grade"]  = '<span class="l3">优</span>'; 
		if($day < 15){
			//$arr["suggest"] = '为避免流失潜在客户，影响账户质量度，请及时续费保证广告持续投放！';
			//$arr["comment"] = '账户余额不足，请尽快充值！';
			$arr["range"] = $arr["range2"] = '<span class="ll1">'.$day.'</span>';
			$arr["grade"]  = '<span class="l1">差</span>'; 
			$arr['point'] = '<span class="ll1">'.$this->sta['renewals'][0].'</span>';
			$arr["point2"]  = $this->sta['renewals'][0];
		}else if($day > 15){
			$arr["range"] = $arr["range2"] = '<span class="ll3">'.$day.'</span>';
			$arr["grade"]  = '<span class="l3">优</span>'; 
			$arr["point2"]  = $this->sta['renewals'][2];
			$arr['point'] = '<span class="ll3">+'.$this->sta['renewals'][2].'</span>';
		}else{
			$arr["range"] = $arr["range2"] = '<span class="ll2">'.$day.'</span>';
			$arr["grade"]  = '<span class="l2">良</span>'; 
			$arr["point2"]  = $this->sta['renewals'][1];
			$arr['point'] = '<span class="ll2">+'.$this->sta['renewals'][1].'</span>';
		}
		
		if($day ==0){
			$c = '<span class="reds">'.$day.'</span>';
		}else{
			$c = '<span class="greenss">'.$day.'</span>';
		}
		$arr['redays'] = $day;
		echo json_encode($arr,JSON_UNESCAPED_UNICODE);
	}
	
	//（账户端）日预算预警
	//预算使用率=过去7天总费用/7/账户总预算
	public function budgetErr(){
		$cid = $_GET['cid'];
		$day1 = date("Y-m-d",strtotime("-1 day"));
		$day7 = date("Y-m-d",strtotime("-7 day"));
		$bud=	M('','',$this->connectReports)->query("select SUM(NEW.BUDGET) AS budget FROM (SELECT  BUDGET FROM aw_reportcampaign WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$day1} 20:00:00' AND '{$day1} 20:00:00'   GROUP BY CAMPAIGN_NAME) AS NEW");
		$budget = $bud[0]['budget'];
		$cost = M('reportcampaign','aw_',$this->connectReports)->where("ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$day7} 20:00:00' AND '{$day1} 20:00:00' AND CAMPAIGN_STATUS  = 'enabled'")->sum('COST');
		$per = sprintf("%.2f",$cost / 7 / $budget) * 100 ;
		if($per < 60){
			
			/*$arr["suggest"] = '1.扩充关键词，添加关键词的变体形式；
2.洞察商机找寻潜力国家进行添加；
3.提高出价，增加广告的曝光率；
4.预算充足情况下，可以分配使用谷歌其他功能；';
			$arr["comment"] = '流量偏低，广告得不到充分展示';*/
			
			$arr["range"] = $arr["range2"] = '<span class="ll1">'.$per.'%</span>'; 
			$arr["grade"]  = '<span class="l1">差</span>'; 
			$arr["point2"]  = $this->sta['budgetErr'][0];
			$arr['point'] = '<span class="ll1">'.$this->sta['budgetErr'][0].'</span>';
		}else if($per >=  60 && $per <=90){
			//$arr["range"] = '预算使用率[60%,90%]';
			//$arr["range2"] = '[60%,90%]';
			/*$arr["suggest"] = '继续加油，定期进行否定排除，监管点击率和关键词的排名情况；';
			$arr["comment"] = '预算利用的不错；
4.预算充足情况下，可以分配使用谷歌其他功能；';*/
			$arr["range"] = $arr["range2"] = '<span class="ll3">'.$per.'%</span>'; 
			$arr["grade"]  = '<span class="l3">优</span>'; 
			$arr["point2"]  = $this->sta['budgetErr'][2];
			$arr['point'] = '<span class="ll3">+'.$this->sta['budgetErr'][2].'</span>';
		}else{
			//$arr["range"] = '预算使用率>90%';
			//$arr["range2"] = '>90%';
			/*$arr["suggest"] = '1.预算提示工具增加预算；
2.否定排除，提高关键词精准度；';
			$arr["comment"] = '预算受限了，广告下线时间早';*/
			$arr["range"] = $arr["range2"] = '<span class="ll2">'.$per.'%</span>'; 
			$arr["grade"]  = '<span class="l2">良</span>'; 	
			$arr["point2"]  = $this->sta['budgetErr'][1];
			$arr['point'] = '<span class="ll2">'.$this->sta['budgetErr'][1].'</span>';
		}
		if($per < 0 ){
			
		}else{
			
		}
		$arr['per'] = $per;
		echo json_encode($arr,JSON_UNESCAPED_UNICODE);
	}
	
	
	public function convers(){
		$cid = $_GET['cid'];
		$sql = "SELECT convers  FROM ads_account_rating_data WHERE account_id = ".$cid;
		$convers = M('',NULL,$this->connectReports)->query($sql);
		$c = $convers[0]['convers'];
		if($c > 5){
			$ar["range"] = $ar["range2"]= '<span class="ll3">'.$c.'</span>';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"]  = $this->sta['convers'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['convers'][2].'</span>';
		}else if($c < -5){
			$ar["range"] = $ar["range2"]= '<span class="ll1">'.$c.'</span>';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['convers'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['convers'][0].'</span>';
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll2">'.$c.'</span>';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar["point2"]  = $this->sta['convers'][1];
			$ar['point'] = '<span class="ll2">'.$this->sta['convers'][1].'</span>';
			
		}
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	//（广告系列）转化次数对比
	public function convers1(){
		$cid = $_GET['cid'];
		//上30天
		$d30a1 = date("Y-m-d",strtotime("-30 day"));
		$d30a2 = date("Y-m-d",strtotime("-1 day"));
		
		//上上30天
		$d30b1 = date("Y-m-d",strtotime("-60 day"));
		$d30b2 = date("Y-m-d",strtotime("-31 day"));
		
		$sql = "SELECT SUM(ALL_CONVERSIONS) AS cv FROM aw_reportcampaign WHERE DAY BETWEEN '{$d30a1} 20:00:00' AND '{$d30a2} 20:00:00' AND ACCOUNT_ID = '{$cid}'
union all
SELECT SUM(ALL_CONVERSIONS) AS cv FROM aw_reportcampaign WHERE DAY BETWEEN '{$d30b1} 20:00:00' AND '{$d30b2} 20:00:00' AND ACCOUNT_ID = '{$cid}'";
		$list = M('',NULL,$this->connectReports2)->query($sql);
		$c = $list[0]['cv'] - $list[1]['cv'];
		$c = intval($c);
		if($c > 0){
			$ar["range"] = $ar["range2"]= '<span class="ll3">'.$c.'</span>';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"]  = $this->sta['convers'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['convers'][2].'</span>';
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll1">'.$c.'</span>';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['convers'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['convers'][0].'</span>';
		}
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	//（广告系列）投放渠道
	public function delivery(){
		$cid = $_GET['cid'];
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-7 day"));
		$sql1 = "SELECT COUNT(*) AS C FROM aw_reportcampaign 
WHERE NETWORK = 'Search Network'  AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00'";
		$rs1 = M('','',$this->connectReports)->query($sql1);
		$arr['search'] = ($rs1[0]['c'] > 0)?true:false;
		$sql = "SELECT COUNT(*) AS C FROM aw_reportcampaign 
WHERE NETWORK = 'Display Network'  AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00'";
		$rs1 = M('','',$this->connectReports)->query($sql);
		$arr['show'] = ($rs1[0]['c'] > 0)?true:false;
		
		//pc再营销
		$sql = "SELECT  COUNT(*) AS C FROM aw_reportcriteria  WHERE   CRITERIA_TYPE = 'User List' AND COST > 0  AND NETWORK = 'Search Network' AND DEVICE = 'Computers' AND ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00'";
	  	$rs1 = M('','',$this->connectReports)->query($sql);
	  	$arr['pc_cost'] = ($rs1[0]['c'] > 0)?true:false;
		if(  $arr['search']  && !$arr['show']  &&  !$arr['pc_cost']  ) {
		  $ar["range"] = $ar["range2"] =   '<span class="ll2">仅搜索广告</span>';
		  $ar["grade"]  = '<span class="l2">良</span>'; 
		  $ar["point2"]  = $this->sta['delivery'][1];
		  $ar['point'] = '<span class="ll2">+'.$this->sta['delivery'][1].'</span>';
		}else if( $arr['search']  && $arr['show'] ){
		  $ar["range"] = $ar["range2"] =  '<span class="ll3">搜索+展示广告</span>';
		  $ar["grade"]  = '<span class="l3">优</span>'; 
		  $ar["point2"]  = $this->sta['delivery'][2];
		  $ar['point'] = '<span class="ll3">+'.$this->sta['delivery'][2].'</span>';
	  	}else if( $arr['show'] && !$arr['pc_cost']){
		  $ar["range"] = $ar["range2"] =  '<span class="ll1">有展示无再营销</span>';
		  $ar["grade"]  = '<span class="l1">'.$this->sta['delivery'][0].'</span>'; 
		  $ar["point2"]  = $this->sta['delivery'][0];
		  $ar['point'] = '<span class="ll1">'.$this->sta['delivery'][0].'</span>';
	  	}else if(  !$arr['search']  && !$arr['show'] ){
		  $ar["range"] = $ar["range2"] =  '<span class="ll1">无广告</span>';
		  $ar["grade"]  = '<span class="l1">'.$this->sta['delivery'][0].'</span>'; 
		  $ar["point2"]  = $this->sta['delivery'][0];
		  $ar['point'] = '<span class="ll1">'.$this->sta['delivery'][0].'</span>';
	  	}
	   	echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	
	public function campaignCtr(){
		$cid = $_GET['cid'];
		$sql = "SELECT campaign_ctr  FROM ads_account_rating_data WHERE account_id = ".$cid;
		$campaign_ctr = M('',NULL,$this->connectReports)->query($sql);
		$c = $campaign_ctr[0]['campaign_ctr'];
		$rs2[0]['ctr'] = $c;
		if($rs2[0]['ctr'] < 1){
			if($rs2[0]['ctr'] == '' || !isset($rs2[0]['ctr'])){
				$ar["range"] = $ar["range2"]= '<span class="lc">N/A</span>';
			}else{
				$ar["range"] = $ar["range2"]= '<span class="ll1">'.$rs2[0]['ctr'].'%<span>';
			}
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar['point'] = '<span class="ll1">'.$this->sta['campaignCtr'][0].'</span>';
			$ar['point2'] = $this->sta['campaignCtr'][0];
			
		}else if($rs2[0]['ctr'] > 3){
			$ar["range"] = $ar["range2"]='<span class="ll3">'.$rs2[0]['ctr'].'%<span>';
			
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar['point'] = '<span class="ll3">+'.$this->sta['campaignCtr'][2].'</span>';
			$ar['point2'] = $this->sta['campaignCtr'][2];
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll2">'.$rs2[0]['ctr'].'%<span>';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar['point'] = '<span class="ll2">+'.$this->sta['campaignCtr'][1].'</span>';
			$ar['point2'] = $this->sta['campaignCtr'][1];
		}
		//$ar['sql'] = $sql;
		$ar['ctr'] = $rs2[0]['ctr'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	//（广告系列）平均点击率
	public function campaignCtr1(){
		$cid = $_GET['cid'];
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-7 day"));
		$sql = "SELECT ROUND(( SUM(CLICKS) / SUM(IMPRESSIONS) ) * 100,2) AS CTR FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network'";
		$rs2 = M('',NULL,$this->connectReports)->query($sql);
		if($rs2[0]['ctr'] < 1){
			$ar["range"] = $ar["range2"]= '<span class="ll1">'.$rs2[0]['ctr'].'%<span>';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar['point'] = '<span class="ll1">'.$this->sta['campaignCtr'][0].'</span>';
			$ar['point2'] = $this->sta['campaignCtr'][0];
		}else if($rs2[0]['ctr'] > 3){
			$ar["range"] = $ar["range2"]='<span class="ll3">'.$rs2[0]['ctr'].'%<span>';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar['point'] = '<span class="ll3">+'.$this->sta['campaignCtr'][2].'</span>';
			$ar['point2'] = $this->sta['campaignCtr'][2];
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll2">'.$rs2[0]['ctr'].'%<span>';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar['point'] = '<span class="ll2">+'.$this->sta['campaignCtr'][1].'</span>';
			$ar['point2'] = $this->sta['campaignCtr'][1];
		}
		$ar['ctr'] = $rs2[0]['ctr'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	public function kwPoint(){
		$cid = $_GET['cid'];
		$sql = "SELECT kw_point  FROM ads_account_rating_data WHERE account_id = ".$cid;
		$kw_point = M('',NULL,$this->connectReports)->query($sql);
		$c = $kw_point[0]['kw_point'];
		$rs2[0]['c'] = $c;
		if($rs2[0]['c'] >= 0 && $rs2[0]['c'] <= 20){
			$ar["range"] = $ar["range2"]='<span class="ll3">'.$rs2[0]['c'].'%</span>';
			//$ar["suggest"] = '继续保持；';
			//$ar["comment"] = '真棒！';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"]  = $this->sta['kwPoint'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['kwPoint'][2].'</span>';
			
		//}else if($rs2[0]['c'] > 20 && $rs2[0]['c'] <= 40){
			
		}else if($rs2[0]['c'] >= 60){
			$ar["range"] = $ar["range2"]='<span class="ll1">'.$rs2[0]['c'].'%</span>';
			//$ar["suggest"] = '现在账户中广告相关性比较差，目标网页质量比较差，搜索网络点击率低；';
			//$ar["comment"] = '关键词质量度太差，影响广告展示';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['kwPoint'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['kwPoint'][0].'</span>';
		}else{
			$ar["range"] = $ar["range2"]='<span class="ll2">'.$rs2[0]['c'].'%</span>';
			//$ar["suggest"] = '提高排名，提高点击率；广告语表现差的进行调整；';
			//$ar["comment"] = '不错，继续加油！';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar["point2"]  = $this->sta['kwPoint'][1];
			$ar['point'] = '<span class="ll2">+'.$this->sta['kwPoint'][1].'</span>';
		/*}else if($rs2[0]['c'] > 30 && $rs2[0]['c'] < 40){
			$ar["range"] = '< 40%';
			$ar["suggest"] = '调整广告提高产品相关性，提高排名提高点击率；';
			$ar["comment"] = '关键词质量度有点偏低了，需要调整下';
			$ar["grade"]  = '<span class="l2">良</span>'; */
			
		}
		$ar['per2'] = $rs2[0]['c'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	
	//（广告组）关键词质量得分
	public function kwPoint1(){
		$cid = $_GET['cid'];
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-1 day"));
		//$d1 = date("Y-m-d",strtotime("-7 day"));
		$sql="SELECT ROUND( (a.c / b.c) ,2) * 100 AS c FROM

(SELECT COUNT(*) AS C FROM `aw_reportkeywords` WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network' AND QUALITY_SCORE <3  AND STATUS = 'enabled') AS a,

(SELECT COUNT(*) AS C FROM `aw_reportkeywords` WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network' AND STATUS = 'enabled' ) AS b";
		$rs2 = M('',NULL,$this->connectReports)->query($sql);
		$rs2[0]['c'] = intval($rs2[0]['c']);
		
		if($rs2[0]['c'] >= 0 && $rs2[0]['c'] <= 20){
			$ar["range"] = $ar["range2"]='<span class="ll3">'.$rs2[0]['c'].'%</span>';
			//$ar["suggest"] = '继续保持；';
			//$ar["comment"] = '真棒！';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"]  = $this->sta['kwPoint'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['kwPoint'][2].'</span>';
			
		//}else if($rs2[0]['c'] > 20 && $rs2[0]['c'] <= 40){
			
		}else if($rs2[0]['c'] >= 60){
			$ar["range"] = $ar["range2"]='<span class="ll1">'.$rs2[0]['c'].'%</span>';
			//$ar["suggest"] = '现在账户中广告相关性比较差，目标网页质量比较差，搜索网络点击率低；';
			//$ar["comment"] = '关键词质量度太差，影响广告展示';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['kwPoint'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['kwPoint'][0].'</span>';
		}else{
			$ar["range"] = $ar["range2"]='<span class="ll2">'.$rs2[0]['c'].'%</span>';
			//$ar["suggest"] = '提高排名，提高点击率；广告语表现差的进行调整；';
			//$ar["comment"] = '不错，继续加油！';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar["point2"]  = $this->sta['kwPoint'][1];
			$ar['point'] = '<span class="ll2">+'.$this->sta['kwPoint'][1].'</span>';
		/*}else if($rs2[0]['c'] > 30 && $rs2[0]['c'] < 40){
			$ar["range"] = '< 40%';
			$ar["suggest"] = '调整广告提高产品相关性，提高排名提高点击率；';
			$ar["comment"] = '关键词质量度有点偏低了，需要调整下';
			$ar["grade"]  = '<span class="l2">良</span>'; */
			
		}
		$ar['per2'] = $rs2[0]['c'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	public function kwCount(){
		$cid = $_GET['cid'];
		$sql = "SELECT kw_search_all  FROM ads_account_rating_data WHERE account_id = ".$cid;
		$kw_search_all = M('',NULL,$this->connectReports)->query($sql);
		$c = $kw_search_all[0]['kw_search_all'];
		$rs2[0]['c'] = $c;
		
		if($rs2[0]['c'] < 5){
			
			$ar["range"] = $ar["range2"]= '<span class="ll1">'.$rs2[0]['c'].'</span>';
			//$ar["suggest"] = '使用关键词变体形式扩充关键词；使用不同的匹配方式；使用关键词工具找询合适的关键词；';
			//$ar["comment"] = '关键词太少了，广告得不到充分的展示';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['kwCount'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['kwCount'][0].'</span>';
		}else if($rs2[0]['c'] >= 10 && $rs2[0]['c'] <= 50){
			$ar["range"] = $ar["range2"]= '<span class="ll2">'.$rs2[0]['c'].'</span>';
			//$ar["suggest"] = '定期进行否定排除使关键词精准，监管点击率和关键词的排名情况；';
			//$ar["comment"] = '账户符合账户构建的黄金法则';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar["point2"]  = $this->sta['kwCount'][1];
			$ar['point'] = '<span class="ll2">+'.$this->sta['kwCount'][1].'</span>';
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll3">'.$rs2[0]['c'].'</span>';
			//$ar["suggest"] = '按关键词相关性重新分组；按关键词的点击率重新分组；按关键词的质量得分重新分组；';
			//$ar["comment"] = '关键词太多了，影响本组关键词的展示';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"]  = $this->sta['kwCount'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['kwCount'][2].'</span>';
		}
		
		$ar['kwcount']  = $rs2[0]['c'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	//（广告组）关键词数量
	public function kwCount1(){
		$cid = $_GET['cid'];
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-7 day"));
		
		$sql = "SELECT COUNT(*) AS C FROM `aw_reportkeywords` WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network'";
		
		$rs2 = M('',NULL,$this->connectReports)->query($sql);
		
		if($rs2[0]['c'] < 5){
			
			$ar["range"] = $ar["range2"]= '<span class="ll1">'.$rs2[0]['c'].'</span>';
			//$ar["suggest"] = '使用关键词变体形式扩充关键词；使用不同的匹配方式；使用关键词工具找询合适的关键词；';
			//$ar["comment"] = '关键词太少了，广告得不到充分的展示';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['kwCount'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['kwCount'][0].'</span>';
		}else if($rs2[0]['c'] >= 10 && $rs2[0]['c'] <= 50){
			$ar["range"] = $ar["range2"]= '<span class="ll2">'.$rs2[0]['c'].'</span>';
			//$ar["suggest"] = '定期进行否定排除使关键词精准，监管点击率和关键词的排名情况；';
			//$ar["comment"] = '账户符合账户构建的黄金法则';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			$ar["point2"]  = $this->sta['kwCount'][1];
			$ar['point'] = '<span class="ll2">+'.$this->sta['kwCount'][1].'</span>';
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll3">'.$rs2[0]['c'].'</span>';
			//$ar["suggest"] = '按关键词相关性重新分组；按关键词的点击率重新分组；按关键词的质量得分重新分组；';
			//$ar["comment"] = '关键词太多了，影响本组关键词的展示';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"]  = $this->sta['kwCount'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['kwCount'][2].'</span>';
		}
		
		$ar['kwcount']  = $rs2[0]['c'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
		
	}
	
	//（广告组）广告语状态
	//拒登
	public function  ref(){
		$sql = "SELECT COUNT(*) AS C  FROM `aw_reportad`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND STATUS = 'enabled' AND CREATIVE_APPROVAL_STATUS  = 'disapproved'";
		$rs1 = M('','',$this->connectReports)->query($sql);
		if($rs1[0]['c'] > 0 ){
			$ar["range"] = $ar["range2"] ='<span class="ll1">拒登</span>';
			//$ar["suggest"] = '查看广告拒登原因，改正后重新提交广告以免影响广告的正常展示；';
			//$ar["comment"] = '您的广告与Google的广告政策不符';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["ref"] = 1;
			$ar["point2"] = $this->sta['ref'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['ref'][0].'</span>';
		}else{
			$ar["range"] = $ar["range2"] ='<span class="ll3">已批准</span>';
			//$ar["suggest"] = '';
			//$ar["comment"] = '';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["ref"] = 0;
			$ar["point2"] = $this->sta['ref'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['ref'][2].'</span>';
		}
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	//（广告组）广告语条数
	//广告语条数为1条
	public function adCount(){
		$cid = $_GET['cid'];
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-7 day"));
		
		$sql = "SELECT COUNT(*) AS C FROM  (SELECT COUNT(*) AS C ,  ADGROUP_ID , ADGROUP_NAME FROM `aw_reportad`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network' GROUP BY ADGROUP_ID) AS NEW WHERE NEW.c = 1";
		
		$rs2 = M('',NULL,$this->connectReports)->query($sql);
		if($rs2[0]['c'] > 0){
			$ar["range"] = $ar["range2"]= '<span class="ll1">1</span>';
			//$ar["suggest"] = '使用2-3条广告语，A/B测试那条广告语更吸引人点击率更高,点击差的进行修改添加，以此类推；';
			//$ar["comment"] = '查看不出哪条广告语更吸引';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"] = $this->sta['adCount'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['adCount'][0].'</span>';
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll3">&gt;=2</span>';
			//$ar["suggest"] = '';
			//$ar["comment"] = '';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			$ar["point2"] = $this->sta['adCount'][2];
			$ar['point'] = '<span class="ll3">+'.$this->sta['adCount'][2].'</span>';
		}
		//$ar['count'] = $sql;
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	
	//（GA网站数据）跳出率
	public function BounceRate(){
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-7 day"));
		$cid = $_GET['cid'];
		$list = M('ads_ga',NULL,$this->connectReports)->where("account = ".$cid)->select();
		$ua = $list[0];
		if(isset($ua)  && !empty($ua)){
			$er = $ua['bouncerate'];
			if($er > 90 || $er <=0){
				$ar["range"] = $ar["range2"]= '<span class="ll1">'.$er.'%</span>';
				$ar["grade"]  = '<span class="l1">差</span>'; 
				$ar["point2"]  = $this->sta['BounceRate'][0];
				$ar['point'] = '<span class="ll1">'.$this->sta['BounceRate'][0].'</span>';
			}else if($er < 60 && $er > 0 ){
				$ar["range"] = $ar["range2"]= '<span class="ll3">'.$er.'%</span>';
				$ar["grade"]  = '<span class="l3">优</span>'; 
				$ar["point2"]  = $this->sta['BounceRate'][2];
				$ar['point'] = '<span class="ll3">+'.$this->sta['BounceRate'][2].'</span>';
			}else{
				$ar["range"] = $ar["range2"]= '<span class="ll2">'.$er.'%</span>';
				$ar["grade"]  = '<span class="l2">良</span>'; 
				$ar["point2"]  = $this->sta['BounceRate'][1];
				$ar['point'] = '<span class="ll2">+'.$this->sta['BounceRate'][1].'</span>';
			}
		}else{
			$ar["range"] = $ar["range2"]= '<span class="lc">N/A</span>';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['BounceRate'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['BounceRate'][0].'</span>';
		}
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	
	//（GA网站数据）加载时间
	public function Loadtime(){
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-1 day"));
		$cid = $_GET['cid'];
		$list = M('ads_ga',NULL,$this->connectReports)->where("account = ".$cid)->select();
		$ua = $list[0];
		if(isset($ua)  && !empty($ua)){
			$er = $ua['loadtime'];
			if($er > 15 || $er <=0 ){
				$ar["range"] = $ar["range2"]= '<span class="ll1">'.$er.'(S)</span>';
				$ar["grade"]  = '<span class="l1">差</span>'; 
				$ar["point2"]  = $this->sta['Loadtime'][0];
				$ar['point'] = '<span class="ll1">'.$this->sta['Loadtime'][0].'</span>';
			}else if($er < 8 && $er > 0){
				$ar["range"] = $ar["range2"]= '<span class="ll3">'.$er.'(S)</span>';
				$ar["grade"]  = '<span class="l3">优</span>'; 
				$ar["point2"]  = $this->sta['Loadtime'][2];
				$ar['point'] = '<span class="ll3">+'.$this->sta['Loadtime'][2].'</span>';
			}else{
				$ar["range"] = $ar["range2"]= '<span class="ll2">'.$er.'(S)</span>';
				$ar["grade"]  = '<span class="l2">良</span>'; 
				$ar["point2"]  = $this->sta['Loadtime'][1];
				$ar['point'] = '<span class="ll2">+'.$this->sta['Loadtime'][1].'</span>';
			}
		}else{
			$ar["range"] = $ar["range2"]= '<span class="lc">N/A</span>';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['Loadtime'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['Loadtime'][0].'</span>';
		}
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	
	//（GA网站数据）停留时间
	public function Dwelltime(){
		$cid = $_GET['cid'];
		$list = M('ads_ga',NULL,$this->connectReports)->where("account = ".$cid)->select();
		$ua = $list[0];
		if(isset($ua)  && !empty($ua)){
			$er = $ua['dwelltime'];
			if($er > 120 || $er <=0){
				$ar["range"] = $ar["range2"]= '<span class="ll3">'.$er.'(S)</span>';
				$ar["grade"]  = '<span class="l3">优</span>'; 
				$ar["point2"]  = $this->sta['Dwelltime'][2];
				$ar['point'] = '<span class="ll3">+'.$this->sta['Dwelltime'][2].'</span>';
			}else if($er < 50 && $er > 0){
				$ar["range"] = $ar["range2"]= '<span class="ll1">'.$er.'(S)</span>';
				$ar["grade"]  = '<span class="l1">差</span>'; 
				$ar["point2"]  = $this->sta['Dwelltime'][0];
				$ar['point'] = '<span class="ll1">'.$this->sta['Dwelltime'][0].'</span>';
			}else{
				$ar["range"] = $ar["range2"]= '<span class="ll2">'.$er.'(S)</span>';
				$ar["grade"]  = '<span class="l2">良</span>'; 
				$ar["point2"]  = $this->sta['Dwelltime'][1];
				$ar['point'] = '<span class="ll2">+'.$this->sta['Dwelltime'][1].'</span>';
			}
		}else{
			$ar["range"] = $ar["range2"]= '<span class="lc">N/A</span>';
			$ar["grade"]  = '<span class="l1">差</span>'; 
			$ar["point2"]  = $this->sta['Dwelltime'][0];
			$ar['point'] = '<span class="ll1">'.$this->sta['Dwelltime'][0].'</span>';
		}
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	
	//（联系频次）CRM库服务记录
	public function crmServerCount(){
		//上30天
		$cid = $_GET['cid'];
		$aid = filter_mcc($cid);
		
		//$ar[0] = $aid;
		//echo json_encode($ar);
		//exit;
		
		$map['account_Id']=$aid;
		$fromUserId= M('n_accountinfo',NULL,$this->connectCrm)->where($map)->getfield("customer_Id");	
		
		//echo $fromUserId;
		//exit;
		
		if($fromUserId){
		//$fromUserId=$aid;
		//$ar[0] = $fromUserId;
		//echo json_encode($ar);
		//exit;
		
		$sql = "select COUNT(*) AS c
				from n_servicelog sl 
				left join n_contacts c on sl.linkman = c.id
				left join n_productname p on sl.productName = p.id
				left join n_servicestate s on sl.serviceState = s.id
				left join n_servicesource se on sl.serveSource = se.id
				left join n_questiontype q on sl.questionType = q.id
				
				
				where sl.fromUserId = ".$fromUserId." and timeOfApplication>".(time()-30*24*3600)."
				order by sl.id desc
				";	 
			$rs= M('',NULL,$this->connectCrm)->query($sql);	
			$c =  intval($rs[0]['c']);
			
			if($c > 4){
				$ar["range"] = $ar["range2"]= '<span class="ll3">'.$c.'</span>';
				$ar["grade"]  = '<span class="l3">优</span>'; 
				$ar["point2"]  = $this->sta['crmServerCount'][2];
				$ar['point'] = '<span class="ll3">+'.$this->sta['crmServerCount'][2].'</span>';
				
			}else if( $c < 2){
				$ar["range"] = $ar["range2"]= '<span class="ll1">'.$c.'</span>';
				$ar["grade"]  = '<span class="l1">差</span>'; 
				$ar["point2"]  = $this->sta['crmServerCount'][0];
				$ar['point'] = '<span class="ll1">'.$this->sta['crmServerCount'][0].'</span>';
			}else{
				$ar["range"] = $ar["range2"]= '<span class="ll2">'.$c.'</span>';
				$ar["grade"]  = '<span class="l2">良</span>'; 
				$ar["point2"]  = $this->sta['crmServerCount'][1];
				$ar['point'] = '<span class="ll2">+'.$this->sta['crmServerCount'][1].'</span>';
			}
		}else{
			$ar["range"] = $ar["range2"]= '<span class="ll1">0</span>';
				$ar["grade"]  = '<span class="l1">差</span>'; 
				$ar["point2"]  = $this->sta['crmServerCount'][0];
				$ar['point'] = '<span class="ll1">'.$this->sta['crmServerCount'][0].'</span>';
		}
			echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	public function converAndGa(){
		$cid = $_GET['cid'];
		$sql = "SELECT  SUM(COST) AS COST , SUM(IMPRESSIONS) AS IMPRESSIONS, SUM(CLICKS) AS CLICKS  FROM `aw_reportcampaign` WHERE `ACCOUNT_ID` = {$cid}";
		$c1 = M('ads_conversions',NULL,$this->connectReports)->where("account = '{$cid}'")->count();
		$c2 = M('ads_ga',NULL,$this->connectReports)->where("account = '{$cid}'")->count();
		$c1 = intval($c1)?true:false;
		$c2 = intval($c1)?true:false;
		$arr['name'] = array($c1,$c2);
		if(!$c1){
			$arr["range"].= '转化代码未添加<br />' ;
			$arr["suggest"] ='请及时添加转化代码。';
		}
		if(!$c2){
			$arr["range"] = 'GA分析代码未添加' ;
			$arr["suggest"] ='请及时添加GA分析代码。';
		}
		
		if($c1 && $c2){
		}
		 
		 if($c1 && $c2){
			$arr["grade"] = '<span class="l3">优</span>'; 
		 }else if($c1 && !$c2){
			 $arr["grade"] = '<span class="l2">良</span>'; 
		 }else if(!$c1 && $c2){
			 $arr["grade"]  = '<span class="l2">良</span>'; 
		 }else{
			 $arr["grade"]  = '<span class="l1">差</span>'; 
		 }
		echo json_encode($arr,JSON_UNESCAPED_UNICODE);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//取消
	public function adCtr(){
		$cid = $_GET['cid'];
		$d2 = date("Y-m-d",strtotime("-1 day"));
		$d1 = date("Y-m-d",strtotime("-7 day"));
		
		$sql = "SELECT AVG(CTR) AS CTR FROM `aw_reportad`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network'";
		
		$rs2 = M('',NULL,$this->connectReports2)->query($sql);
		
		
		//pr(array($rs2));
		//$ar["range"] = $rs2;
		
		
		
		if($rs2[0]['ctr'] < 1){
			$ar["range"] = $ar["range2"]='<1%';
			/*$ar["suggest"] = '1.重新撰写广告语增加与本组产品的相关性；
2.目标网址链接到对应的产品页面；
3.使用号召性的语言提高广告的点击率；
4.不同角度撰写2条以上的广告进行效果对比；
5.建议使用当地的小语种进行推广；';
			$ar["comment"] = '广告语表现的太差了';*/
			$ar["grade"]  = '<span class="l1">差</span>'; 
			
		
		}else if($rs2[0]['ctr'] < 3){
			$ar["range"] =$ar["range2"]= '>3%';
			//$ar["suggest"] = '';
			//$ar["comment"] = '';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			
		}else{
			$ar["range"] =$ar["range2"]= '[1%,3%]';
			//$ar["suggest"] = '';
			//$ar["comment"] = '';
			$ar["grade"]  = '<span class="l2">良</span>'; 
		}
		
		$ar['ctr'] = $rs2[0]['ctr'];
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
	
	//取消
	public function kwCtr(){
		$sql = "SELECT AVG(CTR) AS CTR FROM `aw_reportkeywords`  WHERE ACCOUNT_ID = '{$cid}' AND DAY BETWEEN '{$d1} 20:00:00' AND '{$d2} 20:00:00' AND NETWORK  = 'Search Network'";
		$rs2 = M('',NULL,$this->connectReports2)->query($sql);
		
		if( $rs2[0]['ctr'] > 4  && $rs2[0]['ctr'] < 5){
			$ar["range"] = '[4%,5%)';
			//$ar["suggest"] = '继续保持；';
			//$ar["comment"] = '很棒，赞一个！';
			$ar["grade"]  = '<span class="l3">优</span>'; 
			
		}else if($rs2[0]['ctr'] > 2  && $rs2[0]['ctr'] <3){
			$ar["range"] = '[2%,3%）';
			//$ar["suggest"] = '改进一下广告语，添加一些号召性的语言；';
			//$ar["comment"] = '不错，还需要加油！';
			$ar["grade"]  = '<span class="l2">良</span>'; 
			
		}else if($rs2[0]['ctr'] > 1  && $rs2[0]['ctr'] <2){
			$ar["range"] = '[1%,2%）';
			//$ar["suggest"] = '查看下广告语是否跟关键词匹配，排名要保持在1-3位；';
			//$ar["comment"] = '关键词表现不好';
			$ar["grade"]  = '<span class="l2">中</span>'; 
			
		}else if($rs2[0]['ctr'] < 1){
			$ar["range"] = '< 1%';
			//$ar["suggest"] = '做否定关键词排查，修改更精准的广告描述；';
			//$ar["comment"] = '关键词质量太差啦！';
			$ar["grade"]  = '<span class="l1">差</span>'; 
		}
		
		
		
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
	}
}