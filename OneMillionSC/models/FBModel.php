<?php
require_once 'autoload.php';
FBModel_autoload();

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