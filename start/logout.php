<!DOCTYPE html>
<?php

	unset($_SESSION['loggerFileName']);
	unset($_SESSION['user']);
	unset($_SESSION['loggedin']);
	unset($_SESSION['loggerFileName']);

	$result = header("location: ../start/login_landing.php");
?>
