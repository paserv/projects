<?php session_start(); ?>
<!doctype html>
<head>
<title>One Million Social Club - Title Here</title>
<script type="text/javascript" src="public/js/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="public/js/jquery-ui-1.11.4.js"></script>
<script type="text/javascript" src="public/js/config.js"></script>
<script type="text/javascript" src="public/js/search.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=places&language=en"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="public/css/bootstrap-combined.min.css" rel="stylesheet">
<link href="public/css/omsc.css" rel="stylesheet">
</head>
<?php
require_once 'autoload.php';
autoload();
$controller = new Controller ();
$excep = new CustomException();
?>

<body>
	<?php include 'header.php'; ?>
	<div id="corpo">
		<?php
		if ($excep->existProblem) {
			include 'error.php';
		} elseif (isset ( $_REQUEST ['solution'] ) && $_REQUEST ['solution'] == QUIZ_SOLUTION) {
			$_SESSION["okquiz"] = QUIZ_SOLUTION;
		?>
		<div>Get Your Placemark for Free</div>
		<div>Sign in with: </div>
		<a href="account.php?sn=FB"><img src="public/img/facebook.png"></a>
		<a href="account.php?sn=TW"><img src="public/img/twitter.png"></a>
		<a href="account.php?sn=PL"><img src="public/img/gplus.png"></a>
		<?php } else { ?>
			<form name="coordinateForm" action="quiz.php" method="post">
				<input type="text" name="solution" id="solution"/>
				<input type="submit" name="solution_button" value="solution"/>
			</form>
	</div>
<?php } include 'footer.php'; ?>
</body>
</html>