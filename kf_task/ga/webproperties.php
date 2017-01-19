<?php
$analytics = require_once 'gaconfig.php';

$id = $_GET['id'];
b($analytics,$id);
$f = array();
function b($analytics,$id){
	try {
	  $properties = $analytics->management_webproperties
		  ->listManagementWebproperties($id);
		 // return $properties;
		
		
		
		 $arr = array();
		 try {
			 $list = $properties->getItems();
			 
			 if(is_array($list) && !empty($list)){
				foreach($list as $v){
					//array_push($arr,$v['id']);
					
					
					$properties = $profiles = $analytics->management_profiles->listManagementProfiles($id, $v['id']);
					
					
					$list = $properties->getItems();
					
					
					$pp = $list[count($list)-1]->getId();
					
					
				
					
					
					$adWordsLinks = $analytics->management_webPropertyAdWordsLinks->listManagementwebPropertyAdWordsLinks($id, $v['id']);
					
					
					
					
					if($adWordsLinks->totalResults){
						$rs = $adWordsLinks->getItems();
						$link = $rs[0]->getAdWordsAccounts();
						$f[$v['id']] = array($pp,$link[0]->getCustomerId(),$id);
						if(is_array($rs) && !empty($rs)){
							foreach($rs as $v2){
								$link = $v2->getAdWordsAccounts();
							}
						}
					}
					
				}
			 }
		 }catch (apiServiceException $e) {
			 echo 'e';
		 }
		 
		 echo json_encode($f);
		 //echo implode(',',$arr);
	} catch (apiServiceException $e) {
		echo 'e';
	} catch (apiException $e) {
		echo 'e';
	}
			
			
}
?>

