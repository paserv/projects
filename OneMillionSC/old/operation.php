<?php session_start(); ?>
<?php
	require_once 'autoload.php';
	autoload();
	
	$excep = new CustomException();
	
	$controller = new Controller();
	$user = null;
	if (isset($_SESSION["latitude"])) {
		$user = $controller->getUserFromSession();
	}
	
	/**
	 * If it comes from paypal store page
	 */
	if (isset ( $_REQUEST ['success'] ) && $_REQUEST ['success'] == 'true') {
		try {
			$controller->register($user, $_REQUEST ['paymentId'], $_REQUEST ['PayerID'], false);
		} catch (Exception $ex) {
			$excep->setError($ex->getCode(), $ex->getMessage());
		}
	} elseif (isset ( $_REQUEST ['success'] ) && $_REQUEST ['success'] == 'false') {
		$_SESSION["latitude"] = null;
		$_SESSION["longitude"] = null;
		$_SESSION["aboutme"] = null;
		$excep->setError(400, "User Cancelled the Approval");
	} else {
		/**
		 * If it comes from account.php
		 */
		# If "Register" button OR "Modify" button -> save latitude, longitude and about me in SESSION #
		if(isset($_REQUEST['modify_button']) || isset($_REQUEST['register_button'])) {
			$_SESSION["latitude"] = $_REQUEST['latitude'];
			$_SESSION["longitude"] = $_REQUEST['longitude'];
			if (isset($_REQUEST['aboutme'])) {
				$_SESSION["aboutme"] = $_REQUEST['aboutme'];
			}
		}
		
	if (isset($_SESSION["latitude"])) {
		$user = $controller->getUserFromSession();
	}
		if ($user !== null) {
			try {
				if (isset($_REQUEST['delete_button'])) {
					$controller->delete($user);
					$user->latitude = "";
					$user->longitude = "";
				} else if (isset($_REQUEST['modify_button'])) {
					$controller->update($user);
				} else if (isset($_REQUEST['register_button'])){
					if (!IS_PAYPAL_ENABLED) {
						$controller->registerFree($user);
					} elseif (isset($_SESSION["okquiz"]) && $_SESSION["okquiz"] === true) {
						$_SESSION["okquiz"] = false;
						$controller->registerFree($user);
					} else {
						$controller->redirectToPaypal();
					}
				}
			} catch (Exception $e) {
				$excep->setError($e->getCode(), $e->getMessage());
			}
		} elseif (isset($_REQUEST['logout_button'])){
			$controller->logout();
			if (isset($_SESSION["latitude"])) {
				$user->latitude = "";
				$user->longitude = "";
			}
		}
	}
?>
<!doctype html>
<head>
<title>One Million Social Club - Registration</title>
<script type="text/javascript" src="public/js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="public/js/jquery-ui-1.11.4.js"></script>
<script type="text/javascript" src="public/js/config.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=places"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="public/css/bootstrap-combined.min.css" rel="stylesheet">
<link href="public/css/omsc.css" rel="stylesheet">
</head>
<body>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
<div id="fb-root"></div>
<script>
  (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.3&appId=1421469004782445";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<?php include 'header.php'; ?>

	<div id="corpo">
	<br>
	<br>
	<br>
	<br>
	<?php
		if ($excep->existProblem) {
			include 'error.php';
		} else { ?>
			<div>Operation Success!</div>
			<div>
			<?php if ($user !== null) {?>
				<a href="index.php<?php echo "?latitude=" . $user->latitude . "&longitude=" . $user->longitude ?>">Come Back Home</a>
			<?php } else { ?>
				<a href="index.php">Come Back Home</a>
			<?php } ?>
			</div>
			<?php 
			if (!isset($_REQUEST['logout_button']) && !isset($_REQUEST['delete_button']) && isset($_SESSION['sn'])) {
				if ($_SESSION['sn'] === 'FB') { ?>
				<div class="fb-share-button" data-href="https://aoapoa.com/" data-layout="button"></div>
				<?php } elseif ($_SESSION['sn'] === 'TW') {?>
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://aoapoa.com" data-count="none" data-hashtags="omsc">Tweet</a>
				<?php } elseif ($_SESSION['sn'] === 'PL') {?>
				<a href="https://plus.google.com/share?url=http://www.aoapao.com" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><img src="public/img/plus_share2.png" alt="Share on Google+"/></a>
				<?php } 
				} ?>
		<?php } ?>
	</div>
	<br>
	<br>
	<br>
	<br>
	<br>
	
<?php include 'footer.php'; ?>
</body>
</html>