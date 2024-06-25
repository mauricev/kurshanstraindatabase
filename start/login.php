
<!DOCTYPE html>";
<?php
	//include_once('../classes/session.php');
	$userName = $_POST['username'];
	$password = $_POST['password'];

	require_once('../classes/classes_database.php');
	error_log("in login.php, user is $userName");

	$userObject = new User($userName, "", $password);
	if ($userObject->IsValidUser()) {
		$userObject->setupSession();
	}
	else {
		$result = header("location: ../start/login_landing.php");
		exit();
	}
?>
