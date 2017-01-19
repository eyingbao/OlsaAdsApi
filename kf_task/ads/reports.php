<?php
require '../vendor/autoload.php';
require 'fun.php';
use Google\AdsApi\AdWords\AdWordsSession;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\Reporting\v201609\ReportDownloader;
use Google\AdsApi\AdWords\Reporting\v201609\DownloadFormat;
use Google\AdsApi\AdWords\ReportSettingsBuilder;
use Google\AdsApi\Common\OAuth2TokenBuilder;
$file = '../config.ini'; 
$cid =$_GET['cid'];
$oAuth2Credential = (new OAuth2TokenBuilder())
								->fromFile($file)
								->build();
$reportSettings = (new ReportSettingsBuilder())
							->fromFile($file)
							->includeZeroImpressions(false)
							->build();
$session = (new AdWordsSessionBuilder())
				->fromFile($file)
				->withClientCustomerId($cid)
				->withOAuth2Credential($oAuth2Credential)
				->withReportSettings($reportSettings)
				->build();
require 'reports.class.php';
$reportDownloader = new ReportDownloader($session);
$Reports = new Reports($reportDownloader,DownloadFormat::CSV,$cid,'csv/');
$kw= $Reports->getKwPoint();
$arr["kw_point"] =  $kw[0];
$arr["kw_search_all"] = $kw[1];
$arr["convers"] = $Reports->getConvers();
$arr["campaign_ctr"] = $Reports->getCampaign();
$account_cost_err = $Reports->getAccountCost();
$arr["account_cost_err"] =$account_cost_err[0]; 
$arr["account_cost_d7"] = $account_cost_err[1];
$arr["account_cost_14"] = $account_cost_err[2];
$arr["mcc"] =  $cid;
$arr["account_id"] =  str_replace('-','',$cid);
echo json_encode($arr);
?>