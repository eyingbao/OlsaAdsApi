<?php
namespace Mobile\Controller;
use Think\Controller;
//报表控制器
class LogoutController extends Controller {
	protected $d1;
	protected $d2;
	protected $cid;
	protected $connectCrm;
	
	//public function _initialize(){
		//parent::_initialize();
	//}
	
	public function login(){
		if(IS_GET){
			$this->assign('DOMAIN_PC',C('DOMAIN_MOBILE'));
			$this->display();
		}else{
			$data = $_POST;
				
			if($data['user'] != '' && $data['pwd'] != ''){
				$pwd = md5($data['pwd']);
				$rs = M('n_accountinfo',NULL)->where("loginName = '{$data[user]}' AND loginPassword = '{$pwd}'")->find();
				if($rs['id']){
					session('uuid',$rs['id']);
					session('uname',$rs['loginname']);
					if($data['remember']  == 1){
						cookie('uuid',$rs['id'],3600 * 24 * 30 * 6);
						cookie('upwd',$pwd,3600 * 24 * 30 * 6);
						cookie('uname',$rs['loginname'],3600 * 24 * 30 * 6);
					}
				} 
				$rs['id']?json_msg(true,$rs['id']):json_msg(false,'用户名或密码错误');
			
			}else{
				json_msg(false,'请填写用户名或者密码');
			}
			
		}
	}
	
	public function index(){
		unset($_SESSION['uuid']);
    	unset($_SESSION['uname']);
		unset($_SESSION['upwd']);
		unset($_SESSION['openid']);
		unset($_SESSION['is_openid'.$_SESSION['source']]);
		cookie('uuid',NULL);
		cookie('uname',NULL);
		cookie('upwd',NULL);
		Headers(C('DOMAIN_MOBILE').'/Logout/login');
	}
		
}