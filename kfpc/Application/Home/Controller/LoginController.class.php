<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
	public function logout(){
		unset($_SESSION['uid']);
    	unset($_SESSION['mcc']);
		
		cookie('uuid',NULL);
		cookie('uname',NULL);
		cookie('upwd',NULL);
		
		Headers(C('DOMAIN_PC').'/Login');
	}
	protected function updateLogin($user){
		$d['mcc'] = $user;
		$d['date'] =date('Y-m-d H:i:s',time());
		M('ads_log',NULL,$this->connectReports)->data($d)->add();		
	}
	public function index(){
	  
		if(IS_GET){
			$this->assign('DOMAIN_PC',C('DOMAIN_PC'));
			$this->display();
	  
	  	}else{
			 $data = $_POST;
			 
			  if(strlen($data['email']) < 5 || strlen($data['email']) > 30){
				  
				  json_msg(false,'用户名在5-30位之间');
				  
			  }else{
				  
				  if(strlen($data['pwd']) < 6 || strlen($data['pwd']) > 10 ){
					  
					  json_msg(false,'密码在6-10位之间');
				  }else{
					$pwd = md5($data['pwd']);
					$email = strtolower($data['email']);
					$rs = M('manager')->where("account = '{$data[email]}' AND password = '{$pwd}'")->find();  
					//pr(array("account = '{$data[email]}' AND password = '{$pwd}'"));
					
					//pr(array($rs));
					
					if($rs['id']){
						
						if($rs['status'] == 1){
							session('uid',$rs['id']);
							if($rs['id'] ==1 && ($rs['mcc'] =='' || $rs['mcc'] ==NULL)){
								$rs['mcc'] = C('ROOT_MCC');	
								M('manager')->where('id=1')->data(array('mcc'=>$rs['mcc']))->save();
							}
							
							$_SESSION['mcc']['id'] = $rs['mcc'];
							$_SESSION['mcc']['nickname'] = $rs['nickname'];
							
							
							if($data['remember']  == 1){
								cookie('uuid',$rs['id'],3600 * 24 * 30 * 6);
								cookie('upwd',$pwd,3600 * 24 * 30 * 6);
								cookie('uname',$rs['account'],3600 * 24 * 30 * 6);
							}
							
							//$this->updateLogin($rs['account']);
						}else{
							//alert('该账号已禁用');	
							json_msg(false,'该账号已禁用');
						}
						
						//Headers($this->url);
						//session('mcc',$rs['']);
						if(OS == 'LIN'){
							$dir = str_replace('/Application/Home','/csv/test/',dirname(dirname(__FILE__)));
						}else{
							$dir = str_replace('\Application\Home','\csv\test\\',dirname(dirname(__FILE__)));
						}
						
					//	$dir = str_replace('\Application\Home','\csv\test\\',dirname(dirname(__FILE__)));
						
						$arr = scandir($dir);
						array_shift($arr);
						array_shift($arr);
						foreach($arr as $v){
							if(  ((time() - filemtime($dir.$v) ) / 60 ) > 5  ){
								unlink($dir.$v);	
							}
						}
						
						
						
						
						
						
						
						 json_msg(true,'登录成功');
					}else{
						 json_msg(false,'用户名或密码错误');
					}
				}
			}
		}
	}
}