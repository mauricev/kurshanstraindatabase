<?php
	//include_once('../classes/session.php');
	require_once(__DIR__ . '/../classes/classes_database.php');

	if (AppSettings::instanceKey() === 'elisa') {
		require_once(__DIR__ . '/../classes/classes_okta.php');

		$oktaAuth = new OktaAuth();
		$oktaAuth->redirectToAuthorize();
	}

	if (AppSettings::instanceKey() !== 'peri') {
		http_response_code(500);
		echo "Login is not configured for this database instance.";
		exit();
	}

	$userName = $_POST['username'] ?? '';
	$password = $_POST['password'] ?? '';

	error_log("in login.php, user is $userName");

	$userObject = new User($userName, "", $password, "local");
	if ($userObject->IsValidUser()) {
		$userObject->setupSession();
	}
	else {
		$result = header("location: ../start/login_landing.php");
		exit();
	}
?>
