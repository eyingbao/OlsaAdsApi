<?php
	require_once  '../config.php';
	require_once '../vendor/autoload.php';
	$client_id = phpAdsGa_CLIENT_ID;
	$client_secret = phpAdsGa_CLIENT_SECRET;
	$redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->addScope(array("https://www.googleapis.com/auth/analytics.readonly"));
	$readToken = phpAdsGa_REFRESH_TOKEN;
	$client->refreshToken($readToken);
	$analytics = new Google_Service_Analytics($client);
	return $analytics;
?>