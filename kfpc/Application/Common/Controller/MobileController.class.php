<?php
namespace Common\Controller;
use Think\Controller;
class MobileController extends Controller {
	protected $domain;
	protected $connectReports;
	public function _initialize(){
		$this->connectReports = 'mysql://'.C('DB_USER_JAVA').':'.C('DB_PWD_JAVA').'@'.C('DB_HOST_JAVA').'/'.C('DB_NAME_JAVA').'#utf8';
		$cUuid = cookie('uuid');
		$cUname = cookie('uname');
		$cPwd = cookie('upwd');
		$sUuid = session('uuid');
		$sUuname = session('uname'); 
		//pr(array($_COOKIE,$_SESSION));
		$this->domain = C('DOMAIN_PC').'/';
		$this->assign('DOMAIN_PC',C('DOMAIN_PC'));
		//echo $this->domain;
		//exit;
		if(	($cUuid && $cUname)   &&  ($sUuid && $sUuname) ){ //同时检测到cookie 和 session

		}else if( ($cUuid && $cUname)   &&  !($sUuid && $sUuname)){//只检测到cookie,第一次进来的时候

		
		}else if( !($cUuid && $cUname)   &&  ($sUuid && $sUuname) ){

		}else{

			Headers("{$this->domain}Mobile/Logout");
		}
		
		
	}
}