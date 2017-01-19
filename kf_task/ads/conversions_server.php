<?php
require '../vendor/autoload.php';
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201609\cm\OrderBy;
use Google\AdsApi\AdWords\v201609\cm\Paging;
use Google\AdsApi\AdWords\v201609\cm\Selector;
use Google\AdsApi\AdWords\v201609\cm\SortOrder;
use Google\AdsApi\AdWords\v201609\cm\ConversionTrackerService;
use Google\AdsApi\Common\OAuth2TokenBuilder;
require_once  '../config.php';
require_once  'public.php';
$cid =$_GET['cid'];
$file = '../config.ini'; 



$oAuth2Credential = (new OAuth2TokenBuilder())->fromFile($file)->build();
$session = (new AdWordsSessionBuilder())->fromFile($file)->withClientCustomerId($cid)->withOAuth2Credential($oAuth2Credential)->build();
$adWordsServices = new AdWordsServices();
$conversionTrackerService = $adWordsServices->get($session, ConversionTrackerService::class);




$selector = new Selector();
$selector->setFields(['Id','Name','ExcludeFromBidding','MostRecentConversionDate','LastReceivedRequestTime','OriginalConversionTypeId','ConversionTypeOwnerCustomerId']);
//$selector->fields = array('Id','Name','ExcludeFromBidding','MostRecentConversionDate','LastReceivedRequestTime','OriginalConversionTypeId','ConversionTypeOwnerCustomerId');




$graph = $conversionTrackerService->get($selector);
/*echo '<pre>';
print_r($graph->getEntries());
echo '<pre>';
exit;*/

$b = false;
if($graph->getTotalNumEntries() ==0){ //未申请
	echo "[\"{$cid}\",\"\",0]";
}else{
	foreach($graph->getEntries() as $v){
		if($v->getStatus() == 'ENABLED'){
			$b = true;
			$obj = $v;
			break;
		}
	}
	if(!$b){
		echo "[\"{$cid}\",\"\",0]";
	}else{
		$graph = $obj;
		$name = $graph->getName();
		$mostRecentConversionDate = $graph->getMostRecentConversionDate();
		$lastReceivedRequestTime = $graph->getLastReceivedRequestTime();
		if(isset($mostRecentConversionDate) || isset($lastReceivedRequestTime)){ //已安装
			echo "[\"{$cid}\",\"{$name}\",2]";
		}else if(!isset($mostRecentConversionDate) && !isset($lastReceivedRequestTime)){  //未验证
			echo "[\"{$cid}\",\"{$name}\",1]";
		}
	}
}
?>

