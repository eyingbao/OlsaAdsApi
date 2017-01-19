<?php
if(isset($_GET['uaid']) && $_GET['uaid']!=''){
	$uaid = $_GET['uaid'];
	$ua = explode('-',$uaid);
	$analytics = require_once 'gaconfig.php';
	c($analytics,$ua[1],$uaid); 
//149-262-0763
}else{
	echo 0;	
}
//c($analytics,'66015945', 'UA-66015945-1');


function c($analytics,$id,$uaid){
	try {
		
		
		$adWordsLinks = $analytics->management_webPropertyAdWordsLinks->
		listManagementwebPropertyAdWordsLinks($id, $uaid);
		
		
		// $adWordsLink = $analytics->management_webPropertyAdWordsLinks->get(
      //'66015945', 'UA-66015945-1', 'vzPvsGJVRROsq8LgRycPtw');
		
		//vzPvsGJVRROsq8LgRycPtw
		//$adWordsLinks = $analytics->management_webPropertyAdWordsLinks->listManagementwebPropertyAdWordsLinks('68633168', 'UA-68633168-1');

		// echo '<pre>';
	// print_r($adWordsLinks);
		// header("Content-type: text/html; charset=utf-8"); 
		 
		// foreach ($adWordsLinks->getItems() as $link) {
			 
		// }
		
		if($adWordsLinks->totalResults){
			$rs = $adWordsLinks->getItems();
			$link = $rs[0]->getAdWordsAccounts();
			echo $uaid.','.$link[0]->getCustomerId();
		}else{
			echo 0;	
		}
		} catch (apiServiceException $e) {
		 // print 'There was an Analytics API service error '
			 // . $e->getCode() . ':' . $e->getMessage();
		
		} catch (apiException $e) {
		 // print 'There was a general API error '
			//  . $e->getCode() . ':' . $e->getMessage();
		}
		
		
	
}
?>

