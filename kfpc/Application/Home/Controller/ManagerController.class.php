<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class ManagerController extends CommonController {
	
	protected $index;
	protected $csvDir = NULL ;
   
   public function _initialize(){
		//$this->csvDir = str_replace('Application\Home\Controller','',dirname(__FILE__)).'csv\\';
		parent::_initialize();
		
		if($this->mcc['id']  != C('ROOT_MCC')){
			echo json_msg(false,'not have access');
			exit;
		}
		
	}
   
   
   public function index(){
	  // echo 3;
		//$this->assign('mcc',$mccer);
	  //	
	  
	  //$list  =  M('manager')->select();
	  
	 // $sql = "SELECT ama.id , ama.nickname , ama.mcc, ama.create_time, ama.account, ama.status FROM ads_manager AS ama  INNER JOIN  ads_mcc AS amc ON ama.mcc = amc.customerId";
	  
	  $sql = "SELECT ama.id , ama.nickname , ama.mcc, ama.create_time, ama.account, ama.status FROM ads_manager AS ama";
	  
	  
//INNER JOIN  ads_mcc AS amc ON ama.mcc = amc.customerId
	  $list = M()->query($sql);
	  
	  foreach($list as $k=>$v){
		  $list[$k]['name'] = M('ads_mcc',NULL,$this->connectReports)->where("customerId = '{$v['mcc']}'")->getField('name');
	  }
	  $this->assign('list',$list);
	  
	  $this->display();
		
		
   }
   
   public function add(){
	   if(IS_POST){
		   $data = $_POST;
	  		if(   strlen($data['account']) < 5  ||   strlen($data['account']) > 30  ){
				
				json_msg(false,'登录账号在5-30位之间');
			
			}elseif(    strlen($data['pwd']) < 6  ||       strlen($data['pwd']) > 10   ){
			   
			   json_msg(false,'密码长度在6-10位之间');
			   
			}else{
				 $c = M('ads_mcc',NULL,$this->connectReports)->where("customerId = '{$data[mcc]}' AND canManageClients = 1")->count();
				  if(!$c){
					  json_msg(false,'请填写合法的mccID');
				  }
				  
				//$d['pwd'] =$data['pwd'];
				$d['account'] = $data['account'];
				$d['password'] = md5($data['pwd']);
		  	   	$d['nickname'] = $data['nickname'];
			   	$d['status'] = intval($data['status']);
			   	$d['mcc'] = $data['mcc'];
				$d['email'] = $data['email'];
			   	$d['tel'] = $data['tel'];
				
				$d['create_time'] = $d['login_time'] = time();
				
				$rs = M('manager')->data($d)->add();
		  		$rs?json_msg(true,'添加成功'):json_msg(false,'添加失败')  ;
			}
	   }
   }
   
   public function pwd(){
		if(IS_POST){
			
			$data = $_POST;
			 if(!intval($data['id'])){
		    		
					json_msg(false,'非法参数');
			
			 }else if(strlen($data['pwd']) < 6  ||       strlen($data['pwd']) > 10){
					json_msg(false,'密码长度在6-10位之间');
			
			}else{
				
				$d['password'] = md5($data['pwd']);
				if(M('manager')->where('id ='.$data['id'])->count()){
					M('manager')->where('id ='.$data['id'])->data($d)->save();
		  			json_msg(true,'修改成功');
				}else{
					json_msg(false,'该账户不存在');
				}
				
				
			}
		}else{
			json_msg(false,'非法参数');	
		}
	}
	
	 public function pwd2(){
		if(IS_POST){
			
			$data = $_POST;
			// if(!intval($data['id'])){
		    		
					//json_msg(false,'非法参数');
			
			if(strlen($data['pwd']) < 6  ||       strlen($data['pwd']) > 10){
					json_msg(false,'密码长度在6-10位之间');
			
			}else{
				
				$d['password'] = md5($data['pwd']);
				//if(M('manager')->where('id ='.session('uid'))->count()){
				M('manager')->where('id ='.session('uid'))->data($d)->save();
		  		json_msg(true,'修改成功');
				//}else{
					//json_msg(false,'该账户不存在');
			//	}
				
				
			}
		}else{
			json_msg(false,'非法参数');	
		}
	}
	
	
	
   
   public function edit(){
	   if(IS_POST){
		   $data = $_POST;
		   
		   if(!intval($data['id'])){
		    	json_msg(false,'非法参数');
		   
		   //}elseif(    strlen($data['pwd']) < 6  ||       strlen($data['pwd']) > 10   ){
			   
			 //  json_msg(false,'密码长度在6-10位之间');

		   
		   }else{
			   $c = M('ads_mcc',NULL,$this->connectReports)->where("customerId = '{$data[mcc]}' AND canManageClients = 1")->count();
			   if(!$c){
				   json_msg(false,'请填写合法的mccID');
			   }
			   
			 //  $d['pwd'] =$data['pwd'];
			   //$d['password'] = md5($data['pwd']);
		  	   $d['nickname'] = $data['nickname'];
			   $d['status'] = intval($data['status']);
			   $d['mcc'] = $data['mcc'];
			   $d['email'] = $data['email'];
			   $d['tel'] = $data['tel'];
			   
			   	M('manager')->where('id ='.$data['id'])->data($d)->save();
		  		json_msg(true,'修改成功');
				//print_r($d);
		   }
		   
		  // 
		   
	   }else{
		   
		   $rs = M('manager')->where('id = '.$_GET['id'])->find();
		   
		   
		   if($rs['id']){
			   echo json_msg(true,$rs);
		   }else{
			   echo json_msg(false,'该记录不存在');
		   }
		   
		   
	   }
   }
   
   
   
   
   //更新mcc
   public function updateMcc(){
	  if(IS_POST){
			$url = C('DOMAIN_TASK').'/ads/accounts.php';
			//echo $url;
			//exit;
			$datetimes = date('Y-m-d H:i:s',time());
			$info = post_curl($url,$data);
			$list = $lt = json_decode($info,true);
			
			//pr(array($list));
			
			if(is_array($list) && !empty($list)){
				$i = 0;
				$arr = array();
				foreach($list['list'] as $k=>$v){
					$cid[0] = substr($v['customerId'],0,3);
						$cid[1] = substr($v['customerId'],3,3);
						$cid[2] = substr($v['customerId'],6,4);
						$v['customerId'] = $cid[0].'-'.$cid[1].'-'.$cid[2];
					if($i == 0){
						$this->index[$v['level']]['pid'] = 0; 
						$this->index[$v['level']]['customerId'] = $v['customerId']; 
						array_push($arr,$v);
					}else{
						$this->index[$v['level']]['pid'] = $this->index[$v['level']-1]['customerId'];
						$this->index[$v['level']]['customerId'] = $v['customerId'];
						$v['pid'] =	$this->index[$v['level']]['pid'];
						array_push($arr,$v);
					}
					$lt = $arr;
					$i++;
				}
			}
			$str='';
			$labelsAccount = array();
			$labelsAccounts = array();
			$labelsMcc = array(); //普通账户数组
			$aa = array();
			foreach($arr as $v){
				$cid =  str_replace('-','',$v['customerId']);
				$labels = 0; //默认不标记
				$status = 0; //默认不显示
				if($v['canManageClients'] == 1){
					$labels = 1; 
					$status = 1; 
					//array_push($aa,$cid);
				}else{
				//过去30天有消耗的 & 没有标记的  =  status  = 1
				
					//检测到有标记
					/*if(is_array($v['accountLabels']) && !empty($v['accountLabels'])){
						foreach($v['accountLabels'] as $v2){
							if($v2['name'] == 'active'){
								$labels = 1; 
								$status = 1; //不显示
								break;
							}
						}
					}*/
					if(in_array('active',$v['accountLabels'])){
						$labels = 1; 
						$status = 1; //不显示
					}
				}
				
				
				$v['canManageClients'] = intval($v['canManageClients']);
				//if($v['canManageClients'] == 1){
					//$status = 1;
				//}
				
				
				$str.='(';
				$str.="'{$v[customerId]}',";
				$str.="'{$cid}',";
				$str.="'{$v[name]}',";
				$str.="'{$v[companyName]}',";
				$str.="'{$v[pid]}',";
				$str.="'{$v[currencyCode]}',";
				$str.="'{$v[testAccount]}',";
				$str.="'{$labels}',";
				$str.="'{$v[canManageClients]}',";
				$str.="'{$v[level]}',";
				$str.="'{$status}',";
				$str.="0,";
				$str.="'{$datetimes}'";
				$str.='),';
			}
			//echo $str;
			//exit;
			$nsql = "CREATE TABLE IF NOT EXISTS `ads_mcc` (
						  `id` int(11) NOT NULL,
						  `customerId` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
						  `account_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
						  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
						  `companyName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `pid` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
						  `currencyCode` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `testAccount` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `accountLabels` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `canManageClients` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
						  `level` tinyint(2) DEFAULT NULL,
						  `status` tinyint(2) NOT NULL DEFAULT '0',
						  `listorder` int(11) NOT NULL DEFAULT '0',
						  `datetimes` datetime DEFAULT NULL
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			M('',NULL,$this->connectReports)->execute($nsql);
			
			
			$str =  rtrim($str,','); 
			
			
			//$sql = "INSERT INTO `ads_mcc`(`customerId`,`account_id`,`name`,`companyName`,`pid`, `currencyCode`, `testAccount`,`accountLabels`,
			//`canManageClients`,`level`,`status`,`listorder`) VALUES".$str;
			$sql = "INSERT INTO `ads_mcc` (`customerId`, `account_id`, `name`, `companyName`, `pid`, `currencyCode`, `testAccount`, `accountLabels`, `canManageClients`, `level`, `status`, `listorder`,`datetimes`) VALUES".$str;
			//echo $sql;
			//exit;
			M('',NULL,$this->connectReports)->execute('TRUNCATE ads_mcc');
			M('',NULL,$this->connectReports)->execute($sql);
			
			$urls = C('DOMAIN_TASK').'/ads/ads.php';
			post_curl($urls,array());
			
			/*$urls = 'http://ga.qdetong.net/ads.php';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urls);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30); //30秒超时
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$data = curl_exec($ch);
			curl_close($ch);*/
			
			
	}else{
		  $this->display();
	}
 }
   
   
   public function upload(){
	 //  echo $this->csvDir;
	   	
		$tempFile = $_FILES['Filedata']['tmp_name'];
	   	$fileTypes = array('csv'); // File extensions
		$fileParts = pathinfo($_FILES['Filedata']['name']);
		$targetFile = $this->csvDir . 'account' .'.'.$fileParts['extension'];
		if(file_exists($targetFile)){
					unlink($targetFile);	
				}
		//echo $targetFile;
		//exit;
		
		//echo $targetFile;
		//exit; 
		//move_uploaded_file($tempFile,$targetFile);
		
		
		
		if (in_array($fileParts['extension'],$fileTypes)) {
			
			if(move_uploaded_file($tempFile,$targetFile)){
				M('',NULL,$this->connectReports)->execute('TRUNCATE ads_mcc');
		
				
				$arr = csv_get_lines($targetFile ,20000,0);
			//	print_r($arr);
			//	exit;
				$list = array();
				foreach($arr as $v){
					if(is_mcc($v[0])){
						array_push($list,'"'.$v[0].'"');
					}
					//	unlink($targetFile);
					//json_msg(false,'数据格式出错');	
					//}
					//echo $v[0].'\n';
					//var_dump(is_mcc($v[0]));
				}
				
				
			}
			$ids = implode(',',$list);
			
			
			
			//print_r($list);
			//echo $ids;
				//exit;
			$accounts= $this->downloadMcc();
			
			$mccDB = M('ads_mcc',NULL,$this->connectReports);
		
		//$arrs[] = $arr[0];
		
		//print_r($accounts);
		//echo '<pre>';
		
		//print_r($this->index);
		
		$accounts = array_slice($accounts,0,20000);
		foreach($accounts as $v){
			$mccDB->data($v)->add();
			//print_r($v);
		}
		
		
		
		
		
		$sql = "UPDATE ads_mcc SET status = 1 WHERE customerId IN ({$ids}) AND canManageClients =0";
			
		//	echo $sql.'\n';
			
			
			M('',NULL,$this->connectReports)->execute($sql);
			
			M('ads_mcc',NULL,$this->connectReports)->where('canManageClients =1')->data(array('status'=>1))->save();
			
			
			//print_r($a);
			json_msg(true,'同步成功');	
			//echo 'alert(1)';
		}else{
			json_msg(false,'请选择 csv 格式的文件进行导入');	
		}
	   //echo $tempFile;
   }
   
   
   //下载所有mcc
    public function downloadMcc(){
        //$data = $_POST;
		$url = URL.'api/examples/AdWords/v201506/AccountManagement/index.php';
		
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
		
		return $arr;
	}
   
   public function getName(){
		$cid = $_POST['cid'];
		echo M('ads_mcc',NULL,$this->connectReports)->where("customerId = '{$cid}'")->getField('name');
   }
   public function iptcustomer(){
		if(IS_GET){
			$this->display();
		}else{
			$upload = new \Think\Upload();// 实例化上传类
			$upload->rootPath= $this->csvDir.'/';
			$upload->savePath=$_POST['cid'].'/';
			$upload->saveName = 'test'; 
			$info2   =   $upload->upload();
			//////////////////////////////////////////////////////////////////////////////
			$csv = $this->csvDir.$_POST['cid'].'/test.csv';	
			$handle = fopen($csv,'r'); 
			while ($data = fgetcsv($handle, 1000, ",")) {
				$data =  eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');
				$num = count($data);
			 	 $row++;
			  	for ($c=0; $c < $num; $c++) {
					$b[$row][] = $data[$c];
			  	}
				$b[$row][] = $_POST['cid'];
			 }
			fclose($handle);
			array_shift($b);
			unlink($csv);
			//pr(array($b));
			if(count($b)){
				$sql="INSERT INTO `n_accountinfo` (`id`, `customer_Id`, `loginName`, `loginPassword`, `tel`, `address`, `compaign`, `mcc`) VALUES ";
				foreach($b as $v){
					M('n_accountinfo',NULL)->where("customer_Id='{$v[4]}'")->delete();
					if($v['6'] == ''){
						$v[6]	 = '123456';
					}
					$pwd = md5($v[6]);
					$sql.="(NULL, '{$v[4]}', '{$v[5]}', '{$pwd}', '{$v[1]}', '{$v[3]}', '{$v[2]}', '{$v[7]}'),";
				}
				$sql =  rtrim($sql,',');
				M()->execute($sql);
			}
			//////////////////////////////////////////////////////////////////////////////
		}
	}
  
}