<?php

	session_start();
	unset($_SESSION['loggerFileName']);
	unset($_SESSION['user']);
	unset($_SESSION['loggedin']);
	unset($_SESSION['loggerFileName']);
	unset($_SESSION['okta_state']);
	unset($_SESSION['okta_nonce']);

	$result = header("location: ../start/login_landing.php");
?>
