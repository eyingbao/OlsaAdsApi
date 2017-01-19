<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class UserController extends CommonController {
	
	protected $index;
	protected $csvDir = NULL ;
   
   public function _initialize(){
		//$this->csvDir = str_replace('Application\Home\Controller','',dirname(__FILE__)).'csv\\';
		parent::_initialize();
		
		
		
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
	
	
	
   
  
   
   
  
}