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
use Google\AdsApi\AdWords\v201609\mcm\ManagedCustomerService;
use Google\AdsApi\Common\OAuth2TokenBuilder;
$GLOBALS['arr'] = array('level'=>0,'list'=>array());
$PAGE_LIMIT = 500;
$file = '../config.ini'; 
$oAuth2Credential = (new OAuth2TokenBuilder())->fromFile($file)->build();


$session = (new AdWordsSessionBuilder())->fromFile($file)->withOAuth2Credential($oAuth2Credential)->build();


$adWordsServices = new AdWordsServices();

$managedCustomerService = $adWordsServices->get($session, ManagedCustomerService::class);

$selector = new Selector();
$selector->setFields(['CustomerId', 'Name','DateTimeZone','CurrencyCode','TestAccount','AccountLabels','CanManageClients']);
$selector->setPaging(new Paging(0, $PAGE_LIMIT));



$customerIdsToAccounts = [];
$customerIdsToChildLinks = [];
$customerIdsToParentLinks = [];
$totalNumEntries = 0;
    do {
      // Make the get request.
      $page = $managedCustomerService->get($selector);
	  
	 // print_r($page);
	//exit;
	  
      // Create links between manager and clients.
      if ($page->getEntries() !== null) {
        $totalNumEntries = $page->getTotalNumEntries();
        if ($page->getLinks() !== null) {
          foreach ($page->getLinks() as $link) {
            $customerIdsToChildLinks[intval($link->getManagerCustomerId())][] = $link;
            $customerIdsToParentLinks[intval($link->getClientCustomerId())] = $link;
          }
        }
        foreach ($page->getEntries() as $account) {
          $customerIdsToAccounts[intval($account->getCustomerId())] = $account;
        }
      }

      // Advance the paging index.
      $selector->getPaging()->setStartIndex(
          $selector->getPaging()->getStartIndex() + $PAGE_LIMIT);
    } while ($selector->getPaging()->getStartIndex() < $totalNumEntries);

    // Find the root account.
    $rootAccount = null;
	foreach ($customerIdsToAccounts as $account) {
  		$c =intval($account->getCustomerId());
	  	if(!array_key_exists($c,$customerIdsToParentLinks)) {
        	$rootAccount = $account;
		 	break;
      	}
	}
	
	
	
	
	DisplayAccountTree($rootAccount, NULL, $customerIdsToAccounts, $customerIdsToChildLinks, 0,0);
	/*echo '<pre>';
	print_r($GLOBALS['arr']);
	echo '</pre>';
	exit;*/
	
	echo json_encode($GLOBALS['arr']);
	function DisplayAccountTree($account, $link, $accounts, $links,$depth,$i){
	 	if($depth > $GLOBALS['arr']['level']){
			$GLOBALS['arr']['level'] = $depth;
		}
		$arr['customerId'] = $account->getCustomerId();
		$arr['name'] = $account->getName();
		$arr['currencyCode'] = $account->getCurrencyCode();
		$arr['testAccount'] = $account->getTestAccount();
		$labels = $account->getAccountLabels();
		$arr['accountLabels'] = array();
		if(is_array($labels) && !empty($labels)){
			foreach($labels as $v){
				array_push($arr['accountLabels'],$v->getName());
			}
		}
		//$arr['aa'] =$labels;
		//$arr['accountLabels'] = $account->getAccountLabels();
		
		$arr['canManageClients'] = $account->getCanManageClients();
		$arr['level'] = $depth;
		$arr['pid'] = ($depth - $i);
		array_push($GLOBALS['arr']['list'],$arr);
		if (array_key_exists(intval($account->getCustomerId()), $links)) {
			foreach ($links[$account->getCustomerId()] as $childLink) {
				$childAccount = $accounts[$childLink->getClientCustomerId()];
				DisplayAccountTree($childAccount, $childLink, $accounts, $links,$depth +1,$depth);
			}
		}
	}
?>