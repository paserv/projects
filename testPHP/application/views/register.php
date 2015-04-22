 <?php
	session_start ();
	include_once '../controllers/Controller.php';
	include_once '../dto/DBUserData.php';
	
	$socialId = $_SESSION["id"];
	$name = $_SESSION["name"];
	$mail = $_SESSION["mail"];
	$socialNetwork = $_SESSION["sn"];
	
	$controller = new Controller();
	$user = new DBUserData($socialId, $name, $mail, 42.10, 12.12, "Description", "URL", "Avatar");
	$controller->registerUserIntoDB($user);
	$controller->registerFakeUserIntoFusionTable($user);

	?>

<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Registration</title>
<link href="../../public/css/bootstrap-combined.min.css" rel="stylesheet">
</head>
<body onload="getLocation()">
	<div>
		Registration OK
	</div>
</body>
</html>