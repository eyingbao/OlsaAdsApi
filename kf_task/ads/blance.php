<?php
//namespace Google\AdsApi\Examples\AdWords\v201609\AccountManagement;
require '../vendor/autoload.php';
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201609\cm\OrderBy;
use Google\AdsApi\AdWords\v201609\cm\Paging;
use Google\AdsApi\AdWords\v201609\cm\Selector;
use Google\AdsApi\AdWords\v201609\cm\SortOrder;
use Google\AdsApi\AdWords\v201609\billing\BudgetOrderService;
use Google\AdsApi\Common\OAuth2TokenBuilder;
$cid =$_GET['cid'];
$file = '../config.ini'; 
require_once  '../config.php';	
require_once  'public.php';

$con = mysqli_connect($db['51']['host'],$db['51']['user'],$db['51']['pwd'],$db['51']['db']);
$date[0] = date('Ymd',strtotime('-7 day'));
$date[1] = date('Ymd',time());

getBlance($cid,$date,$con,$file);


function getBlance($customerId,$date,&$con,$file){
	$yday = date('Y-m-d',strtotime('-1 day'));
	$cid  = str_replace('-','',$customerId);
	try {
		$oAuth2Credential = (new OAuth2TokenBuilder())->fromFile($file)->build();
		$session = (new AdWordsSessionBuilder())->fromFile($file)->withClientCustomerId($cid)->withOAuth2Credential($oAuth2Credential)->build();
		$adWordsServices = new AdWordsServices();
		
		$budgetOrderService = $adWordsServices->get($session, BudgetOrderService::class);
		
		$selector = new Selector();
		$selector->setFields(['CustomerId']);
		$o = $budgetOrderService->get($selector);
	
	
		$rs['budget'] = 0;
			$ids = array();
			$prs = $o->getEntries();
			if(isset($prs)){
				
				
				
				if(is_array($o->getEntries())){
					foreach($o->getEntries() as $v){
						$getLastRequest = $v->getLastRequest();
						if(isset($getLastRequest)){
							if($v->getLastRequest()->getStatus() == 'APPROVED'){
								//array_push($ids,intval($v->id));
								$ids[intval($v->getId())] = str_replace('Asia/Shanghai','',str_replace(' ','',$v->getStartDateTime()));
							}
						}
					}
				}
				/*echo '<pre>';
		print_r($ids);
		echo '</pre>';
			exit;*/
				
				if(count($ids)){
					arsort($ids);
					$ida =  array_keys($ids);
					$id = $ida[0];
					foreach($o->getEntries() as $v){
						if($v->getId() == $id){
							$rs['bdate'] = substr($v->getStartDateTime(),0,8);
							$rs['budget'] += $v->getSpendingLimit()->getMicroAmount() / 1000000;
						}	
					}
					$budget =  sprintf('%01.2f',$rs['budget']);
				}else{
					$budget = 0;
				}
			}else{
				$budget = 0;	
			}
			
			
			$bdate = substr($rs['bdate'],0,4).'-'.substr($rs['bdate'],4,2).'-'.substr($rs['bdate'],6,2);
			$cost = get_cost($cid,$bdate,$yday,$con);
			$bce  = $budget - $cost;
			echo "[{$cid},{$budget},\"{$bdate}\",\"{$cost}\",\"{$bce}\"]";
		}catch (Exception $e) {
			echo "[{$cid},\"e\"]";
		}	
	}


function get_cost($cid,$day1,$day2,&$con){
		$sql = "SELECT SUM(COST) AS cost FROM `aw_reportcampaign`  WHERE ACCOUNT_ID = '{$cid}' AND DAY >= '{$day1}'";
		$row = mysqli_query($con,$sql);
		$rs = mysqli_fetch_array($row,MYSQLI_ASSOC);
		return isset($rs['cost'])?$rs['cost']:0;
	}

?>