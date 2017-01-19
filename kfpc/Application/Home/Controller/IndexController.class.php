<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class IndexController extends CommonController {
	
	protected $index;
	protected $csvDir = NULL ;
   
   public function _initialize(){
		parent::_initialize();
		//$this->csvDir = str_replace('Application\Home\Controller','',dirname(__FILE__)).'csv\\';
	}
   
   
   public function index(){
	   
	  // var_dump($this->cc);
	  // exit;
	   
	   $mcc = $_GET['mcc'];
	   
	   switch($mcc){
		   case 0: //总权限
		   		//echo 3;
				$mccer =  M('ads_mcc',NULL,$this->connectReports)->where('pid = 0 AND canManageClients =1')->find();
		   		$list = M('ads_mcc',NULL,$this->connectReports)->where('level = 1 AND canManageClients =1 AND status = 1 ')->select();
		   break;
		   
		   default: //其他权限		  
		    //echo 2;
			//exit; 
		  	 $mccer =  M('ads_mcc',NULL,$this->connectReports)->where("canManageClients =1 AND customerId = '{$mcc}'")->find();
			 $list = M('ads_mcc',NULL,$this->connectReports)->where("pid = '{$mcc}' AND status = 1")->select();
			
		   break;
	   }
	  
	  // 
	   
	  // print_r($mccer);
	  // exit;
	    $this->assign('mcc',$mccer);
	  $this->assign('list',$list);
	   
	   $this->display();
   }
   
   
   public function downs(){
	   //http://adwords.cn/api/examples/AdWords/v201502/Reporting/DownloadCriteriaReportWithAwql.php
	   
		$list = M('ads_mcc',NULL,$this->connectReports)->where('canManageClients = 0')->field('customerId')->select();
	  //	print_r($list);
		//exit;
	  
	  
	  $date = $_GET['date'] = date('Ymd');
	  $type = '\account\\';
	  //$dir.=$date.$type;
	  // var_dump(file_exists($dir));
	  //exit;
	//  echo $dir.$date;
	  //exit;
	  if(!file_exists($this->csvDir.$date)){
		
		if(mkdir($this->csvDir.$date)){
			mkdir($this->csvDir.$date.$type);  
		}
	}
	 // exit;
	 
	 foreach($list as $v){
		 
		 
	 
	 
	 
	    $url = C('DOMAIN_PC').'/api/examples/AdWords/v201506/Reporting/DownloadCriteriaReportWithAwql.php';
		//$data['cid'] = '971-996-6574';
		$data['cid']   =  $v['customerid'];
		$data['path'] = $this->csvDir.$date.$type.$data['cid'].'.csv';
		
	    $info = post_curl($url,$data);
		
		$arr = csv_get_lines($data['path'] ,20000,0);
		array_pop($arr);
		array_pop($arr);
		$finalArr = array();
		foreach($arr[0] as $k=>$v){
			foreach($arr[1] as $k2 =>$v2){
				if($k2 == $k){
					if($v == 'Cost'){
						$finalArr[str_replace(' ' ,'',$v)] = str_replace('0' ,'',$v2)/100;
					}else{
						$finalArr[str_replace(' ' ,'',$v)] = $v2;
					}
					
					
					
					break;
				}
			}
		}
	   $finalArr['customerId'] = $data['cid'];
	   
	   M('AccountPerformanceReport')->data($finalArr)->add();
	   
	//print_r($finalArr);
		
		}
		//echo $info;
		   
   }
   
   //读取scv
   public function b(){
	   
	   //541-796-6427 出口易 99
	   //963-940-8925 大客   98
	   //263-789-8221 普客 97
	   //588-471-6822 休眠 0
	   //674-539-3439 DGA
	   
	  // UPDATE ad_mcc SET status =0 where customerId = '588-471-6822' OR customerId = '674-539-3439'
	   
	   $sql = "UPDATE ads_mcc SET status = 1 WHERE canManageClients =1";
	   echo $sql;
   }
   
   //读取scv
   public function a(){
	   
	  // echo $this->csvDir;
	  // exit;
	 
	 ///  $arr = csv_get_lines('Public/csv/report.csv',20000,0);
	    $arr = csv_get_lines($this->csvDir.'/book.csv',20000,0);
		//echo $this->csvDir;
		
		//print_r($arr);
		//$content = 'NmWSpQ穇2Wartune		317-764-8459';
		
		$rs = array();
		
		//print_r($arr);
		//exit;
		foreach($arr as $v){
			//preg_match("/[0-9]{3}-[0-9]{3}-[0-9]{4}/", $v[0], $m);
			//echo($v[0]);
			if(!empty($v[0])){
				array_push($rs,"'".$v[0]."'");
			}
			//print_r(str_replace(' ','',$v[0]));
			//array_push($rs,str_replace(' ','',$v[0]));
		}
		
		/*foreach($rs as $v){
			//$m=NULL;
			preg_match_all("/[0-9]{3}-/", $v, $mc);
			print_r($mc);
		}*/
		$ids = implode(',',$rs);
		//echo $ids;
		
		$sql = "UPDATE ads_mcc SET status = 1 WHERE customerId IN({$ids})";
	   echo $sql;
	  // $a = file_exists("Public/csv/report.csv");
	  // var_dump($a);
	   
   }
   
    public function index2(){
        //$data = $_POST;
		$url = C('DOMAIN_PC').'/api/examples/AdWords/v201506/test.php';
		
		$info = post_curl($url,$data);
		//$json = '';
		$list = $lt = json_decode($info,true);
		//print_r($list);
		//exit;
		if(is_array($list) && !empty($list)){
			$i = 0;
			//print_r($list);
			$arr = array();
			foreach($list['list'] as $k=>$v){
				$cid[0] = substr($v['customerId'],0,3);
					$cid[1] = substr($v['customerId'],3,3);
					$cid[2] = substr($v['customerId'],6,4);
					$v['customerId'] = $cid[0].'-'.$cid[1].'-'.$cid[2];
				
				
				
				if($i == 0){
					$this->index[$v['level']]['pid'] = 0; 
					$this->index[$v['level']]['customerId'] = $v['customerId']; 
					//$['customerId'] = substr
					array_push($arr,$v);
				}else{
				//	echo $v['level'] - $list['list'][$k-1]['level'].'<br />';
				
					$this->index[$v['level']]['pid'] = $this->index[$v['level']-1]['customerId'];
					$this->index[$v['level']]['customerId'] = $v['customerId'];
					
					$v['pid'] =	$this->index[$v['level']]['pid'];
					
					/*switch($v['level'] - $lt[$i-1]['level']){
						case 0:
							$v['pid'] =	 $lt[$i-1]['pid'];
						
						break;
						
						case 1:
							$v['pid'] =	 $lt[$i-1]['customerId'];
						
						break;
						
						case -1:
							$v['pid'] =	 $lt[$i-2]['pid'];
						
						break;
						
						case -2:
							$v['pid'] =	 $lt[$i-3]['pid'];
						
						break;
						
						case -3:
							$v['pid'] =	 $lt[$i-4]['pid'];
						
						break;
						
						case -4:
							$v['pid'] =	 $lt[$i-5]['pid'];
						
						break;
						
						case -5:
							$v['pid'] =	 $lt[$i-6]['pid'];
						
						break;
						
					}*/
					
				//	if(empty($v) || isset($v))
					
					//echo $v['level'] - $list[$k-1]['level'].'<br />';
					array_push($arr,$v);
				}
				
				$lt = $arr;
				
				$i++;
				
			}
			
		}
		
		$mccDB = M('ads_mcc',NULL,$this->connectReports);
		
		//$arrs[] = $arr[0];
		
		//print_r($mccDB);
		//echo '<pre>';
		
		//print_r($this->index);
		foreach($arr as $v){
			$mccDB->data($v)->add();
			//print_r($v);
		}
		//print_r($arr);
	//	echo '</pre>';
		//print_r($arr);
		//exit;
    }
	
	
	//预算列表
	public function getBudgetList(){
		$this->display('budgetList');
	}
}