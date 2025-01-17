<?php
#require_once '../../library/facebook-php-sdk/autoload.php';
#require_once '../configuration/FBconfig.php';
#require_once '../models/AbstractSocialModel.php';
#require_once '../dto/SocialUser.php';
#require_once '../controllers/SocialException.php';

require_once $_SERVER["DOCUMENT_ROOT"] . '/application/configuration/FBConfig.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/models/AbstractSocialModel.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/dto/SocialUser.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/application/controllers/SocialException.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/library/facebook-php-sdk/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

class FBModel extends AbstractSocialModel {
	
	function getUser() {
		FacebookSession::setDefaultApplication (FB_APP_ID, FB_APP_SECRET);
		$helper = new FacebookRedirectLoginHelper (FB_REDIRECT_URL);
		try {
			$session = $helper->getSessionFromRedirect();
		} catch ( FacebookRequestException $ex ) {
			echo $ex;
		} catch (Exception $ex) {
			echo $ex;
		}
		
		if (isset ( $session )) {
		$request = new FacebookRequest ($session, 'GET', '/me');
		$response = $request->execute ();
		// get response
		$graphObject = $response->getGraphObject ();
		$fbid = $graphObject->getProperty('id'); // To Get Facebook ID
		$fbfullname = $graphObject->getProperty('name'); // To Get Facebook full name
		$femail = $graphObject->getProperty ('email'); // To Get Facebook email ID
		$socialPageUrl = FB_ROOT_URL . $fbid;
		$avatarUrl = FB_GRAPH_URL . $fbid . "/picture";
		$user = new SocialUser($fbid, $fbfullname, $femail, $socialPageUrl, $avatarUrl, FB_ID);
		return $user;
		} else {
			$loginUrl = $helper->getLoginUrl ( array (
					'scope' => FB_REQUIRED_SCOPE
			) );
			$se = new SocialException ( "Login needed: " . $loginUrl . "<br>");
			$se->loginUrl = $loginUrl;
			throw $se;
			//echo '<a href="' . $loginUrl . '">Login</a>';
			//window.alert('Go to Facebook Login: <a href="' . $loginUrl . '">Login</a>');
			//echo("<script>window.location.href = 'http://www.mrwebmaster.it';</script>");
			//header ("Location: " . $loginUrl);
			exit;
		}
		
	}
}
?>
