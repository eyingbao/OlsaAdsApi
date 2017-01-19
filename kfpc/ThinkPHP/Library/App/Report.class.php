<?php
namespace App;
class Report{
	
	protected 
	
	//广告系列每日预算异常预警
	public function CampaignBudgetErr($customerId,$date){
		
		/*$customerId = $_GET['customerId'];
		$filePath = 'D:\wamp\www\adwords\csv\test\\'.time().$customerId.'.csv';
		$dateRange = sprintf('%d,%d',date('Ymd', strtotime('-7 day')), date('Ymd', strtotime('-1 day')));
		$reportQuery= 'SELECT  Headline  FROM AD_PERFORMANCE_REPORT   DURING '.$dateRange; */
		
		return array(
			'path' => 'D:\wamp\www\adwords\csv\test\\'.time().$customerId.'csv',
			'query' => "SELECT  Headline  FROM AD_PERFORMANCE_REPORT  DURING {$date[0]},$date[1]}"
		);
		
	}

}



?>