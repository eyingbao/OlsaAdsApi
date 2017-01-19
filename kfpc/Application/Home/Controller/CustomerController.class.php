<?php
namespace Home\Controller;
use Think\Controller;

//客户账户控制器
class CustomerController extends Controller {
	protected $children = array();  //登录账户下的所有子账户
	protected $customerChildren = array();  //登录账户下的所有子客户
	protected $sUuid;
	protected $mcc;
	
	protected $csvDir ;
	protected $d1;
	protected $d2;
	protected $url;
	protected $cid;
	
	protected $connectReports;
	
	protected $mccArr = array();
	public function _initialize(){
		//$this->csvDir = str_replace('Application\Home\Controller','',dirname(__FILE__)).'csv\test\\';
		$this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
		if(OS == 'LIN'){
			$this->csvDir = str_replace('Application/Home/Controller','',dirname(__FILE__)).'csv/';
		}else{
			$this->csvDir = str_replace('Application\Home\Controller','',dirname(__FILE__)).'csv\\';
		}
		
		//echo $this->csvDir;
		//exit;
		
		$this->sUuid = session('uid');
		
		$this->d1 = $_GET['d1'];
		$this->d2 = $_GET['d2'];
		$this->url = C('DOMAIN_PC').'/api/examples/AdWords/v201506/Reporting/index.php';
		//$this->cid = $_GET['cid'];
		
		
		if(!$this->sUuid){
			json_msg(0,'登录超时');	
		}
		
		if(isset($_GET['mcc']) && isset($_GET['title'])){
			if($_GET['mcc'] == ''){
				json_msg(false,'请输入搜索内容');
			}
			$sel = 1;
			
		}else if(isset($_GET['mcc']) && !isset($_GET['title'])){
			if($_GET['mcc'] == ''){
				json_msg(false,'请输入搜索内容');
			}
			
			$sel = 1;
		}else if(!isset($_GET['mcc']) && isset($_GET['title'])){
			if($_GET['title'] == ''){
				json_msg(false,'请输入搜索内容');
			}
			
			$sel = 2;
		}else{
			
			$sel = 0;
		}
		$this->mcc = $_GET['mcc'];
		
		switch($sel){
			
			case 0:
				json_msg(false,'请输入搜索内容');	
			break;
			
			
			case 1: //精准搜索
				if(!is_mcc($this->mcc)){
					json_msg(false,'非法mcc');	
				}
				
				$mccList = M('ads_mcc',NULL,$this->connectReports)->where('status = 1')->select(); //所有活跃账户
				findChildren($mccList,$_SESSION['mcc']['id'],$this->children); //获取当前登录账户下的所有子活跃账户
				$mccIdList = M('ads_mcc',NULL,$this->connectReports)->where('status = 1  AND canManageClients = 0')->field('id,customerId')->select(); //所有活跃非管理员账户
				$arr1= array();
				foreach($mccIdList as $v){
					array_push($arr1,$v['customerid']);
				}
				
				$farr = array_intersect($arr1,$this->children);
				if(!in_array($this->mcc,$farr)){
					json_msg(false,'您没有权限');	
				}else{
					$rs = M('ads_mcc',NULL,$this->connectReports)->where("customerId ='{$this->mcc}'")->find();
					array_push($this->mccArr,$rs['id']);
					
				}
				
			break;
			
			
			case 2: //模糊搜素
			
				//$mccIdList = M('ads_mcc',NULL,$this->connectReports)->where("status = 1  AND canManageClients = 0 AND name LIKE '%秦工%'")->field('customerId')->select(); //所有活跃非管理员账户
				$this->mcc = $_GET['mcc'];
				$mccList = M('ads_mcc',NULL,$this->connectReports)->where('status = 1')->select(); //所有活跃账户
				findChildren($mccList,$_SESSION['mcc']['id'],$this->children); //获取当前登录账户下的所有子活跃账户
				$mccIdList = M('ads_mcc',NULL,$this->connectReports)->where('status = 1  AND canManageClients = 0')->field('customerId')->select(); //所有活跃非管理员账户
				$arr1= array();
				foreach($mccIdList as $v){
					array_push($arr1,$v['customerid']);
				}
				
				$farr = array_intersect($arr1,$this->children);
				
				$selArr =  M('ads_mcc',NULL,$this->connectReports)->where("status = 1  AND canManageClients = 0 AND name LIKE '%{$_GET[title]}%'")->getField('id,customerId'); 
				
				if(is_array($selArr) && !empty($selArr)){
					$finalArr = array_intersect($selArr,$farr);
				
					if(count($finalArr) == 0){
						json_msg(false,'您没有权限');	
					}else{
							$this->mccArr = array_keys($finalArr);
					}
				
				}else{
						json_msg(false,'没有找到该账户');	
						
				}
			break;
		}
	}
	
	
	//通过模糊搜索获取客户列表
	public function getList(){
		
		$ids  = implode(',',$this->mccArr);
		$list =  M('ads_mcc',NULL,$this->connectReports)->where("status = 1  AND canManageClients = 0 AND id IN ({$ids})")->field('name,customerId')->select(); 
		foreach($list as $v){
			$str.="<option value=\"{$v[customerid]}\">{$v[name]}</option>";
		}
		json_msg(true,$str);
	}
	
	
	public function index(){
		$curl = new \App\Curl('callback'); 
		$list = array();
		$date = get_date_list();
		
	
		
		$d2 =  str_replace('-',',',$date[2]['value']);
		$d7 =  str_replace('-',',',$date[3]['value']);
		$d14 =  str_replace('-',',',$date[4]['value']);
		
		array_push($list,get_curl_url_single($this->mcc,$d7,'ctrErr'));  //过去7天，广告组ctr异常

		array_push($list,get_curl_url_single($this->mcc,$date[1]['value'].','.$date[1]['value'],'keywordsErr2')); //昨天，关键词质量异常
		
		array_push($list,get_curl_url_single($this->mcc,$d14,'adCtrErr')); //过去14天，广告语ctr异常
		
		array_push($list,get_curl_url_single($this->mcc,$date[1]['value'].','.$date[1]['value'],'adCountErr'));  //昨天，广告语数量异常
		
		array_push($list,get_curl_url_single($this->mcc,$date[0]['value'].','.$date[0]['value'],'adErr'));  //今日，广告异常
		
		
		array_push($list,get_curl_url_single($this->mcc,$d7,'budget'));  //过去7天，余额
		
		array_push($list,get_curl_url_single($this->mcc,$d7,'deliveryChannel'));  //过去7天，投放渠道异常
		
		array_push($list,get_curl_url_single($this->mcc,$d7,'kwCount'));  //过去7天，按广告组分关键词总量
		
		array_push($list,get_curl_url_single($this->mcc,$d14,'monthCost2'));  //过去14天，消耗曲线图
		
		
		array_push($list,get_curl_url_single($this->mcc,$date[1]['value'].','.$date[1]['value'],'campaignBudgetErr')); //昨天，广告系列日预算异常
		
		array_push($list,get_curl_url_single($this->mcc,$d2,'campaignCostErr')); //过去2天，广告系列消耗异常
		
		
			
	$lists = array();
		foreach ($list as $val) {  
			array_push($lists,$val['url']);
			//$request = new \App\CurlRequest($val['url'], $val ['method'], $val ['post_data'], $val ['header'], $val ['options']);  
			//$curl->add($request);  
			
		
		}  
		$arr = async_get_url($lists);
		$str = '';
		if(is_array($arr) && !empty($arr)){
			foreach($arr as $v){
				$str.=$v;
			}
			
		}
		$str = rtrim($str,',');
		$json =  '{"suc":true,"list":['.$str.']}';
		//echo $str;
		//exit;
		//$curl->execute();  
		
		//$arr = async_get_url($list);
		
		
		//$GLOBALS['date'] = rtrim($GLOBALS['date'],',');
		
		echo  $json;
	}
	
	
	
	protected function getOther($data){
		$info = post_curl($this->url,$data);
		$list = csv_get_lines($data['path'] ,20000,0);
		array_shift($list);
		array_pop($list);
		$sum = $list[count($list)-1];
		return $sum;
	}
	
	
	public function keywords(){
		$tit = $_GET['tit'].'-关键词报告';
		$data['cid'] = $data2['cid'] = $data3['cid']= $this->mcc;
		$data['path'] = $this->csvDir.$data['cid'].'-'.$this->sUuid.'-kw.csv';
		$data2['path'] = $this->csvDir.$data['cid'].'-'.$this->sUuid.'-kw_search.csv';
		$data3['path']= $this->csvDir.$data['cid'].'-'.$this->sUuid.'-kw_content.csv';
		/*$data['report_query'] = 'SELECT Status, Criteria ,CampaignName , AdGroupName  , Cost , Impressions , CostPerConvertedClick,CpcBid,CpmBid,ActiveViewCpm,AverageCpc ,CostPerConversionManyPerClick FROM KEYWORDS_PERFORMANCE_REPORT    DURING 20150915,20150915';*/
		$data['report_query'] = $data2['report_query'] = $data3['report_query'] =  "SELECT Status, Criteria ,CampaignName , AdGroupName   ,CpcBid, Clicks,Impressions , Ctr,  AverageCpc, Cost  ,  AveragePosition ,QualityScore, ValuePerConversionManyPerClick , CostPerConvertedClick , ConversionsManyPerClick,ViewThroughConversions FROM KEYWORDS_PERFORMANCE_REPORT    ";
		$data['report_query'].="DURING {$this->d1},{$this->d2}";
	    $info = post_curl($this->url,$data);
		$list = csv_get_lines($data['path'] ,20000,0);
		
		array_shift($list);
		array_pop($list);
		$sum = $list[count($list)-1];
		array_pop($list);
		foreach($list as $k=>$v){
			$list[$k][1] = str_replace('+',' +',$list[$k][1]);
		}
		$header = array('关键字状态','关键字','广告系列','广告组','最高每次点击费用','点击次数','展示次数','互动率','平均费用','总费用','平均排名','质量得分','转化次数','每次转化费用','转化率','浏览型转化');
		
		foreach($list as $k=> $v){
			foreach($v as $k2=> $v2){
				if($k2== 0){
					switch($v2){
						case 'enabled':
							$val = '已启用';
						break;
						
						case 'paused':
							$val = '已暂停';
						break;
						
						case 'removed':
							$val = '已删除';
						break;
					}
					$list[$k][$k2] = $val;
				}
				
				if( $k2 == 4 || $k2 == 8 || $k2 == 9 || $k2 == 13){
					$list[$k][$k2] = round($v2/1000000,2);
				}
			}
		}
		
		foreach($sum as $k=>$v){
			if($k== 0){
				$sum[$k] = '总计';
			}
			if( $k == 8 || $k ==9 || $k == 13){
				$sum[$k]= round($v/1000000,2);
			}
		}
		
		$data2['report_query'] .=" WHERE AdNetworkType2 != CONTENT DURING {$this->d1},{$this->d2}"; 
		$data3['report_query'] .=" WHERE AdNetworkType2 = CONTENT DURING {$this->d1},{$this->d2}"; 
		
		$sum2 = $this->getOther($data2);
		$sum3 = $this->getOther($data3);
		foreach($sum2 as $k=>$v){
			if($k== 0){
				$sum2[$k] = '总计搜索';
			}
			if($k == 8 || $k ==9 || $k== 13){
					$sum2[$k]= round($v/1000000,2);
				}
		}
		foreach($sum3 as $k=>$v){
			if($k== 0){
				$sum3[$k] = '总计展示';
			}
			if( $k == 8 || $k ==9 || $k == 13){
					$sum3[$k]= round($v/1000000,2);
				}
		}
		//print_r($sum2);
		
		array_push($list,$sum);
		array_push($list,$sum2);
		array_push($list,$sum3);
		getCSV($tit,$header,$list);	
	}
	
	//搜索字词下载
	public function searchKeyword(){
		$tit = $_GET['tit'].'-搜索字词报告';
		$data['cid'] =$this->mcc;
		$data['path'] = $this->csvDir.$data['cid'].'-'.$this->sUuid.'-sk.csv';
		$data['report_query'] = "SELECT MatchTypeWithVariant, KeywordTextMatchingQuery, CampaignName , AdGroupName  ,Clicks, Impressions,Ctr,AverageCpc,Cost,AveragePosition , ValuePerConversionManyPerClick , CostPerConvertedClick , ClickConversionRate,ConversionsManyPerClick,ViewThroughConversions FROM SEARCH_QUERY_PERFORMANCE_REPORT   DURING {$this->d1},{$this->d2}";
		
		$info = post_curl($this->url,$data);
		
		$list = csv_get_lines($data['path'] ,20000,0);
		
		array_shift($list);
		array_pop($list);
		$sum = $list[count($list)-1];
		array_pop($list);
		
		foreach($list as $k=>$v){
			$list[$k][1] = str_replace('+',' +',$list[$k][1]);
		}
		
		$header = array('匹配类型','搜索字词','广告系列','广告组','点击次数','展示次数','点击率','平均每次点击费用','总费用','平均排名','转化次数','每次转化费用','点击转化率','转化率','浏览型转化');
		foreach($list as $k=> $v){
			foreach($v as $k2=> $v2){
				if($k2== 0){
					//phrase (close variant)
					switch($v2){
						case 'broad':
							$val = '广泛匹配';
						break;
						case 'broad (close variant)':
							$val = '广泛匹配（紧密变体）';
						break;
						
						
						case 'exact':
							$val = '完全匹配';
						break;
						
						case 'exact (close variant)':
							$val = '完全匹配（紧密变体）';
						break;
						
						
						case 'phrase':
							$val = '词组匹配';
						break;
						
						case 'phrase (close variant)':
							$val = '词组匹配（紧密变体）';
						break;
					}
					$list[$k][$k2] = $val;
				}
				
				if($k2 == 7    ||  $k2 == 8 || $k2==11){
					$list[$k][$k2] = round($v2/1000000,2);
				}
			}
		}
		foreach($sum as $k=>$v){
			if($k== 0){
				$sum[$k] = '总计';
			}
			if($k == 7    ||  $k == 8 || $k==11){
				$sum[$k]= round($v/1000000,2);
			}
		}
		
		array_push($list,$sum);
		
		getCSV($tit,$header,$list);	
	}
   //CountryCriteriaId
  	//地理位置报告
	public function geo(){
		$geo = M('geo')->getField('id,cname');
		$tit = $_GET['tit'].'-地理位置报告';
		$data['cid'] =$this->mcc;
		$data['path'] = $this->csvDir.$data['cid'].'-'.$this->sUuid.'-geo.csv';
		
		//$data['report_query'] = "SELECT  CountryCriteriaId ,Clicks, Impressions,Ctr,AverageCpc,Cost,AveragePosition , ValuePerConversionManyPerClick , CostPerConvertedClick , ClickConversionRate,ConversionsManyPerClick,ViewThroughConversions FROM GEO_PERFORMANCE_REPORT   DURING {$this->d1},{$this->d2}";
		
		$data['report_query'] = "SELECT  CountryCriteriaId ,Clicks, Impressions,Ctr,AverageCpc,Cost,AveragePosition  ,ConversionsManyPerClick, CostPerConvertedClick , ClickConversionRate,ViewThroughConversions FROM GEO_PERFORMANCE_REPORT   DURING {$this->d1},{$this->d2}";
		$info = post_curl($this->url,$data);
		$list = csv_get_lines($data['path'] ,20000,0);
		array_shift($list);
		array_pop($list);
		$sum = $list[count($list)-1];
		array_pop($list);
		$header = array('国家/地区','点击次数','展示次数','点击率','平均每次点击费用','总费用','平均排名','转化次数','每次转化费用','转化率','浏览型转化');
		foreach($list as $k=> $v){
			foreach($v as $k2=> $v2){
				if($k2== 0){
					$list[$k][$k2] = $geo[$v2];
				}
				if($k2 == 4    ||  $k2 == 5 || $k2==8){
					$list[$k][$k2] = round($v2/1000000,2);
				}
			}
		}
		foreach($sum as $k=>$v){
			if($k== 0){
				$sum[$k] = '总计';
			}
			if($k == 4    ||  $k == 5 || $k==8){
				$sum[$k]= round($v/1000000,2);
			}
		}
		$final = array();
		foreach($list as $v){
			if((str_replace(' ','',$v[0]) == '') || str_replace(' ','',$v[0]) == '--'){
				
			}else{
				array_push($final,$v);
			}
		}
		//print_r($final);
		//exit;
		
		array_push($final,$sum);
		getCSV($tit,$header,$final);	
	}
	
	
	//账户日报下载
	public function dates(){
		//echo $this->csvDir;
		//exit;
		$tit = $_GET['tit'].'-账户日报';
		$data['cid'] =$this->mcc;
		$data['path'] = $this->csvDir.$data['cid'].'-'.$this->sUuid.'-date.csv';
		$data['report_query'] = "SELECT  Date ,Clicks, Impressions,Ctr,AverageCpc,Cost,AveragePosition , ValuePerConversionManyPerClick , CostPerConvertedClick , ClickConversionRate,ViewThroughConversions  ,ConversionsManyPerClick   FROM ACCOUNT_PERFORMANCE_REPORT   DURING {$this->d1},{$this->d2}";
		
		//echo $data['report_query'];
		//exit;
		
		
		$info = post_curl($this->url,$data);
		$list = csv_get_lines($data['path'] ,20000,0);
		array_shift($list);
		array_pop($list);
		$sum = $list[count($list)-1];
		array_pop($list);
		$header = array('日期','点击次数','展示次数','点击率','平均每次点击费用','总费用','平均排名','转化次数','每次转化费用','转化率','浏览型转化','估算的转换总计');
		$dates = array();
		foreach($list as $k=> $v){
			
			foreach($v as $k2=> $v2){
				if($k2 == 0){
					array_push($dates,$v2);
				}
				if($k2 == 4    ||  $k2 == 5 || $k2==8){
					$list[$k][$k2] = round($v2/1000000,2);
				}
			}
		}
		foreach($sum as $k=>$v){
			if($k== 0){
				$sum[$k] = '总计';
			}
			if($k == 4    ||  $k == 5 || $k==8){
				$sum[$k]= round($v/1000000,2);
			}
		}
		arsort($dates);
		//
		//exit;
		$lists = array();
		foreach($dates as $v){
			foreach($list as $v2){
				if($v2[0] == $v){
					array_push($lists,$v2);
					break;
				}
			}
		}
		//print_r($dates);
		//print_r($list);
		//print_r($lists);
		array_push($lists,$sum);
		getCSV($tit,$header,$lists);	
	}
}