<div id="error">
	<?php
	$codes = array(
			#Search Error#
			100 => array('Sorry no results found', 'public/img/login_ico.png'),
			101 => array('Please Register for unlimited search', 'public/img/login_ico.png'),
			102 => array('No query', 'public/img/login_ico.png'),
			
			#DB Error#
			200 => array('Connection to database unaivailable', 'public/img/login_ico.png'),
			201 => array('Impossible to delete User', 'public/img/login_ico.png'),
			202 => array('Impossible check User is registered', 'public/img/login_ico.png'),
			203 => array('Impossible search User', 'public/img/login_ico.png'),
			204 => array('Impossible search by ID', 'public/img/login_ico.png'),
			205 => array('User Already Registered', 'public/img/login_ico.png'),
			206 => array('One Million Users already registered', 'public/img/login_ico.png'),
			207 => array('Error insert user', 'public/img/login_ico.png'),
			208 => array('Error User Not Registered', 'public/img/login_ico.png'),
			209 => array('Error update User', 'public/img/login_ico.png'),
			210 => array('Select count error', 'public/img/login_ico.png'),
			211 => array('Impossible search by Coords', 'public/img/login_ico.png'),
			212 => array('No Result found in search by Coords', 'public/img/login_ico.png'),
			212 => array('No Result found in search by Name And Coords', 'public/img/login_ico.png'),
			213 => array('Select count Home error', 'public/img/login_ico.png'),
			
			#Fusion Error#
			300 => array('Impossible Register User Into Fusion Table', 'public/img/login_ico.png'),
			301 => array('Impossible Delete User From Fusion Table', 'public/img/login_ico.png'),
			302 => array('Impossible Update User From Fusion Table', 'public/img/login_ico.png'),
			
			400 => array('Fusion Tables are full', 'public/img/login_ico.png'),
			
	);
	
	if(isset($_SESSION ["error_code"]) && $_SESSION ["error_code"] !== false){
		$error_code = $_SESSION ['error_code'];
		$message = $codes[$error_code][0];
		$private_err_msg = "";
		if (isset ($_SESSION ["error_private_msg"]) ) {
			$private_err_msg = $_SESSION ["error_private_msg"];
		}
		$icon = $codes[$error_code][1];
		if ($message != false) {
			echo '<br><br><br><br><div style="margin-left:30px; align=center"><div><img src="' . $icon . '">' . $message . '</div><div>' . $private_err_msg . '</div></div>';
			//TODO log into DB
			//TODO send mail
		} else {
			echo '<br><br><br><br><div style="margin-left:30px; align=center"><img src="public/img/login_ico.png">Error Code not found</div>';
		}
		
	} else {
		echo '<br><br><br><br><div style="margin-left:30px; align=center"><img src="public/img/login_ico.png">No Error to display</div>';
	}
	?>	
</div>