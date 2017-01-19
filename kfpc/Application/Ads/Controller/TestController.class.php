<?php
namespace Udb\Controller;
use Think\Controller;
class TestController extends Controller {
	
	
	
	public function __construct(){
		
		
	}
	
	
	
	
    public function index(){
		$commonEvent = A('Common','Event');
		$list = $commonEvent->getSiteidCount();
		
		print_r($list);
		
		
	}
	
	public function update(){
		$commonEvent = A('Common','Event');
		$list = $commonEvent->getSiteidCount();
		
		
		if(is_array($list) && !empty($list)){
			foreach($list as $v){
				$commonEvent->updateSiteidCount($v);
			}
		}
	}
	
}