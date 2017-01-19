<?php
//namespace ads\api;
class Reports{
	protected $reportDownloader;
	protected $DownloadFormat;
	protected $cid;
	protected $fPath;
	public function __construct($reportDownloader,$DownloadFormat,$cid,$fPath){
		$this->reportDownloader =$reportDownloader;
		$this->DownloadFormat = $DownloadFormat;
		$this->cid = $cid;
		$this->fPath = $fPath;
	}
	
	public function getAllCost(){
		$customerId = $this->cid;
		$fPath = $this->fPath;
		$d1 = date("Ymd",strtotime("-7 day"));
		$d2 = date("Ymd",strtotime("-1 day"));
		$filePath1 =$fPath.$customerId.'-all_cost.csv';
		$reportQuery = "SELECT  Cost FROM CAMPAIGN_PERFORMANCE_REPORT WHERE   CampaignStatus = ENABLED  AND Cost > 0   DURING {$d1},{$d2}";
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($reportQuery, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath1);
		
		$arr1 = csv_get_lines($filePath1,2000,0);
		unlink($filePath1);
		$f1 = array();
		array_shift($arr1);
		array_pop($arr1);
		array_pop($arr1);
		if(is_array($arr1) && !empty($arr1)){
			foreach($arr1 as $v1){
				array_push($f1,$v1[0]/1000000);
			}
			$f1 = array_sum($f1);
		}else{
			$f1 = 0;	
		}
		return $f1;
	}
	
	
	
	
	public function getBudget(){
		$customerId = $this->cid;
		$fPath = $this->fPath;
		$d1 = date("Ymd",strtotime("-7 day"));
		$d2 = date("Ymd",strtotime("-1 day"));
		$filePath1 =$fPath.$customerId.'-budgetErr1.csv';
		$reportQuery = "SELECT  Cost,BudgetId FROM CAMPAIGN_PERFORMANCE_REPORT WHERE   CampaignStatus = ENABLED  DURING {$d1},{$d2}";
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($reportQuery, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath1);
		$arr1 = csv_get_lines($filePath1,2000,0);
		unlink($filePath1);
		$f1 = array();
		array_shift($arr1);
		array_pop($arr1);
		array_pop($arr1);
		$amount =  array();
		if(is_array($arr1) && !empty($arr1)){
			foreach($arr1 as $v){
				//$at = bdd($user,$customerId,$fPath,$v[1]);
				array_push($amount,$v[1]);
			}
			$all_cost = $this->getAllCost();
			return array(implode(',',$amount),$all_cost);	
		}else{
			return 0;	
		}
	}
	
	
	
	public function getAccountCost(){
		$customerId = $this->cid;
		$fPath = $this->fPath;
		$d1 = date("Ymd",strtotime("-7 day"));
		$d2 = date("Ymd",strtotime("-1 day"));
		$filePath1 =$fPath.$customerId.'-account_cost_err1.csv';
		$q1 = "SELECT  Cost FROM CAMPAIGN_PERFORMANCE_REPORT WHERE   CampaignStatus = ENABLED  AND AdNetworkType1 = SEARCH  AND Cost > 0   DURING {$d1},{$d2}";
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q1, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath1);
		
		
		$arr1 = csv_get_lines($filePath1,2000,0);
		unlink($filePath1);
		$f1 = array();
		array_shift($arr1);
		array_pop($arr1);
		array_pop($arr1);
		if(is_array($arr1) && !empty($arr1)){
			foreach($arr1 as $v1){
				array_push($f1,$v1[0]/1000000);
			}
			
			$f1 = array_sum($f1);
			$f1  =floor($f1 / 7); 
		}else{
			$f1 = 0;	
		}
		$d3 = date("Ymd",strtotime("-14 day"));
		$d4 = date("Ymd",strtotime("-8 day"));
		
		$filePath2 =$fPath.$customerId.'-account_cost_err2.csv';
		$q2 = "SELECT  Cost FROM CAMPAIGN_PERFORMANCE_REPORT  WHERE    CampaignStatus = ENABLED  AND AdNetworkType1 = SEARCH    AND Cost > 0 DURING {$d3},{$d4}";
		
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q2, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath2);
		
		$arr2 = csv_get_lines($filePath2,2000,0);
		unlink($filePath2);
		$f2 = array();
		array_shift($arr2);
		array_pop($arr2);
		array_pop($arr2);
		if(is_array($arr2) && !empty($arr2)){
			foreach($arr2 as $v2){
				array_push($f2,$v2[0]/1000000);
			}
			
			$f2 = array_sum($f2);
			$f2  =floor($f2 / 7); 
		}else{
			$f2 = 0;	
		}
		return array($f1-$f2,$f1,$f2);
	}
	
	
	
	public function getCampaign(){
		$customerId = $this->cid;
		$fPath = $this->fPath;
		$d2 = date("Ymd",strtotime("-1 day"));
		$d1 = date("Ymd",strtotime("-7 day"));
		$filePath1 =$fPath.$customerId.'-campaign_ctr.csv';
		$q1 = "SELECT  Clicks,Impressions FROM CAMPAIGN_PERFORMANCE_REPORT  WHERE    CampaignStatus = ENABLED  AND AdNetworkType1 = SEARCH AND  Impressions > 0   DURING {$d1},{$d2}";
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q1, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath1);
		
		$arr1 = csv_get_lines($filePath1,2000,0);
		unlink($filePath1);
		$f1 = array();
		array_shift($arr1);
		array_pop($arr1);
		array_pop($arr1);
		$f1 = array();
		$f2 = array();
		//return $arr1;
		if(is_array($arr1) && !empty($arr1)){
			foreach($arr1 as $v1){
				array_push($f1,$v1[0]);
				array_push($f2,$v1[1]);
			}
			
			$f1 = array_sum($f1);
			
			$f2 = array_sum($f2);
			
			return sprintf("%.4f",($f1 / $f2)) * 100;
		}
	}
	
	public function getConvers(){
		$customerId = $this->cid;
		$fPath = $this->fPath;
		$d30a1 = date("Ymd",strtotime("-30 day"));
		$d30a2 = date("Ymd",strtotime("-1 day"));
		$filePath1 =$fPath.$customerId.'-convers1.csv';
		$q1 = "SELECT  AllConversions FROM CAMPAIGN_PERFORMANCE_REPORT  WHERE    CampaignStatus = ENABLED     DURING {$d30a1},{$d30a2}";
		
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q1, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath1);

		$arr1 = csv_get_lines($filePath1,2000,0);
		unlink($filePath1);
		$f1 = array();
		array_shift($arr1);
		array_pop($arr1);
		array_pop($arr1);
		if(is_array($arr1) && !empty($arr1)){
			foreach($arr1 as $v1){
				array_push($f1,$v1[0]);
			}
		}
		$f1 = array_sum($f1);
		
		//上上30天
		$d30b1 = date("Ymd",strtotime("-60 day"));
		$d30b2 = date("Ymd",strtotime("-31 day")); 
		$filePath2 =$fPath.$customerId.'-convers2.csv';
		$q2 = "SELECT  AllConversions FROM CAMPAIGN_PERFORMANCE_REPORT  WHERE   CampaignStatus = ENABLED     DURING {$d30b1},{$d30b2}";
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q2, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath2);
	
		$arr2 = csv_get_lines($filePath2,2000,0);
		unlink($filePath2);
		$f2 = array();
		array_shift($arr2);
		array_pop($arr2);
		array_pop($arr2);
		if(is_array($arr2) && !empty($arr2)){
			foreach($arr2 as $v2){
				array_push($f2,$v2[0]);
			}
		}
		$f2 = array_sum($f2);
		
		return intval($f1) - intval($f2);
		//return array($f1,$f2);
	}
	
	
	public function getKwPoint(){
		$customerId = $this->cid;
		$fPath = $this->fPath;
		
		$d1 = date("Ymd",strtotime("-1 day"));

		$filePath1 =$fPath.$customerId.'-kw_point1.csv';
		$filePath2 =$fPath.$customerId.'-kw_point2.csv';
		$q1 = "SELECT  Criteria,CampaignId , AdGroupId ,QualityScore  FROM KEYWORDS_PERFORMANCE_REPORT  WHERE    CampaignStatus = ENABLED  AND AdGroupStatus = ENABLED  AND Status = ENABLED   DURING {$d1},{$d1}";
		$q2 = "SELECT  CampaignId , CampaignName FROM  CAMPAIGN_PERFORMANCE_REPORT  WHERE  AdNetworkType1 = CONTENT   AND  CampaignStatus = ENABLED    DURING {$d1},{$d1}";
		
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q2, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath2);
			
			
		$arr2 = csv_get_lines($filePath2,2000,0);
		unlink($filePath2);
		array_shift($arr2);
		array_pop($arr2);
		array_pop($arr2);
			
			
		$reportDownloadResult = $this->reportDownloader->downloadReportWithAwql($q1, $this->DownloadFormat);
   		$reportDownloadResult->saveToFile($filePath1);
			
		$arr1 = csv_get_lines($filePath1,2000,0);
		unlink($filePath1);
		array_shift($arr1);
		array_pop($arr1);
		array_pop($arr1);
		$f2 = array();
		if(is_array($arr2) && !empty($arr2)){
			foreach($arr2 as $v2){
				array_push($f2,$v2[0]);
			}
		}
		$f1 = array();
		$f4 = array();
		$fall =array();
		if(is_array($arr1) && !empty($arr1)){
			foreach($arr1 as $v1){
				if(!in_array($v1[1],$f2)){
					if(intval($v1[3]) < 4 && is_numeric($v1[3])){
						array_push($f4,$v1);
					}
					array_push($f1,$v1);
				}
			}
			$aa = count($f1);
			$bb = count($f4);
			
			if($aa != 0){
				$bb = sprintf("%.2f",$bb/$aa)*100;
			}else{
				$aa = 0;	
			}
			return array($bb,$aa);
		}
		return array(0,0);
	}
}
?>