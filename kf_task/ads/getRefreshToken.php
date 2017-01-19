<?php
require '../vendor/autoload.php';
require '../config.php';
use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
class GetRefreshToken {
	const ADWORDS_API_SCOPE = 'https://www.googleapis.com/auth/adwords';
	const DFP_API_SCOPE = 'https://www.googleapis.com/auth/dfp';
	const AUTHORIZATION_URI = 'https://accounts.google.com/o/oauth2/v2/auth';
	const REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';
	public static function main() {
		$PRODUCTS = [
			['AdWords', self::ADWORDS_API_SCOPE],
			['DFP', self::DFP_API_SCOPE],
			['AdWords and DFP', self::ADWORDS_API_SCOPE . ' ' . self::DFP_API_SCOPE]
	];
    $stdin = fopen('php://stdin', 'r');
    $api = trim(fgets($stdin));
    $api = intval($api);
	$scopes = $PRODUCTS[$api][1] . ' ' . trim(fgets($stdin));
	$scopea = array(
		'https://www.googleapis.com/auth/adwords',
		'https://www.googleapis.com/auth/analytics',
		'https://www.googleapis.com/auth/analytics.edit'
	);
	$scopea = implode(' ',$scopea);
	$oauth2 = new OAuth2([
        'authorizationUri' => self::AUTHORIZATION_URI,
        'redirectUri' => self::REDIRECT_URI,
        'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
       // 'clientId' => $clientId,
       // 'clientSecret' => $clientSecret,
		'clientId' => phpAdsGa_CLIENT_ID,
        'clientSecret' => phpAdsGa_CLIENT_SECRET,
        'scope' => $scopea
    ]);
	if(isset($_GET['code']) && $_GET['code']!=''){
		$oauth2->setCode($_GET['code']);
   		$authToken = $oauth2->fetchAuthToken();
		echo $authToken['refresh_token'];
	}else{
		echo  $oauth2->buildFullAuthorizationUri();
	}
  }
}
GetRefreshToken::main();