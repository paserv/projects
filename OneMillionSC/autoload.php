<?php
function getDirs() {
	$dirs = array(
			"root" => $_SERVER["DOCUMENT_ROOT"],
			"models" =>  __DIR__ . '/models/',
			"dto" => __DIR__ . '/dto/',
			"configuration" => __DIR__ . '/configuration/',
			"controllers" =>  __DIR__ . '/controllers/',
			"library" =>  __DIR__ . '/library/',
			"tcp" => '_c',
	);
	return $dirs;
}

function autoload() {
	$dirs = getDirs();
	$file = $dirs['controllers'] . 'Controller.php';
	require_once($file);
}

function controller_autoload() {
	$dirs = getDirs();
	$tcp = $dirs['tcp'];
	
	$file0 = $dirs['models'] . 'AbstractSocialModel.php';
	$file1 = $dirs['models'] . 'FBModel.php';
	$file2 = $dirs['models'] . 'DBModel.php';
	$file3 = $dirs['models'] . 'PLModel.php';
	$file4 = $dirs['models'] . 'FusionModel.php';
	$file5 = $dirs['models'] . 'DummyModel.php';
	$file6 = $dirs['dto'] . 'DBUser.php';
	$file7 = $dirs['configuration'] . 'Config' . $tcp . '.php';
	$file8 = $dirs['models'] . 'TWModel.php';
	$file9 = $dirs['models'] . 'PayPalModel.php';
	$file10 = $dirs['dto'] . 'CustomException.php';
	$dep_array = array($file0, $file1, $file2, $file3, $file4, $file5, $file6, $file7, $file8, $file9, $file10);
	require_array($dep_array);
}

function ReCaptcha_autoload() {
	$dirs = getDirs();
	$file = $dirs['library'] . '/recaptcha/autoload.php';
	require_once($file);
}

function FB_API_autoload() {
	$dirs = getDirs();
	$file = $dirs['library'] . '/facebook-php-sdk/autoload.php';
	require_once($file);
}

function Google_API_autoload() {
	$dirs = getDirs();
	$file = $dirs['library'] . '/google-php-api/autoload.php';
	require_once($file);
}

function Twitter_API_autoload() {
	$dirs = getDirs();
	$file = $dirs['library'] . '/twitter-php-api/autoload.php';
	require_once($file);
}

function PayPal_API_autoload() {
	$dirs = getDirs();
	$file = $dirs['library'] . '/paypal-php-sdk/autoload.php';
	require_once($file);
}

function GeoLocation_autoload() {
	$dirs = getDirs();
	$file = $dirs['library'] . '/geolocation/GeoLocation.php';
	require_once ($file);
}

function DummyModel_autoload() {
	$dirs = getDirs();
	$file1 = $dirs['models'] . 'AbstractSocialModel.php';
	$file2 = $dirs['dto'] . 'SocialUser.php';
	$dep_array = array($file1, $file2);
	require_array($dep_array);
}

function DBUser_autoload() {
	$dirs = getDirs();
	$file1 = $dirs['dto'] . 'SocialUser.php';
	$file2 = $dirs['dto'] . 'QuizDTO.php';
	$dep_array = array($file1, $file2);
	require_array($dep_array);
}

function require_array($dep_array) {
	foreach ($dep_array as $file) {
		require_once($file);
	}
}