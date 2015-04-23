 <?php
	session_start ();
	include_once '../controllers/Controller.php';
	include_once '../dto/SocialUser.php';
	
	$controller = new Controller ();
	$socialNetwork = $_SESSION ["sn"];
	$currentUser = $controller->getLoggedUser ( $socialNetwork );
	
	$_SESSION ["id"] = $currentUser->socialId;
	$_SESSION ["name"] = $currentUser->name;
	$_SESSION ["mail"] = $currentUser->email;
	$_SESSION ["avatarUrl"] = $currentUser->avatarUrl;
	$_SESSION ["socialPageUrl"] = $currentUser->socialPageUrl;
	?>

<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Login with your Favourite Social Network</title>
<link href="../../public/css/bootstrap-combined.min.css"
	rel="stylesheet">
<link href="../../public/css/omsc.css" rel="stylesheet">
<script type="text/javascript" src="../../public/js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="../../public/js/config.js"></script>
<script type="text/javascript"
	src="http://maps.google.com/maps/api/js?sensor=false&libraries=places"></script>
<script type="text/javascript"
	src="../../public/js/locationpicker.jquery.min.js"></script>
<script type="text/javascript" src="../../public/js/location_choose.js"></script>
</head>
<body>
	Location:
	<input type="text" id="address" style="width: 200px" />
	<div id="map" style="width: 500px; height: 400px;"></div>
	<form name="coordinateForm" action="register.php" method="post">
		Lat.: <input type="text" name="latitude" id="latitude" readonly/>
		Long.: <input type="text" name="longitude" id="longitude" readonly/>
		<input type="submit" name="submit_button" value="Register" />
	</form>
	<div>
		<ul>
			<li>Image</li>
			<li><img
				src="https://graph.facebook.com/<?php echo $currentUser->socialId; ?>/picture"></li>
			<li>ID</li>
			<li><?php echo $currentUser->socialId; ?></li>
			<li>Fullname</li>
			<li><?php echo $currentUser->name; ?></li>
			<li>Email</li>
			<li><?php echo $currentUser->email; ?></li>
		</ul>
	</div>
	<div id="location"></div>
	<a href="register.php">Register</a>
</body>
</html>