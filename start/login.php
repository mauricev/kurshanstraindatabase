<!DOCTYPE html>
<?php

	$userName = $_POST['username'];
	$password = $_POST['password'];

	require_once('../classes/classes_database.php');

	$userObject = new User($userName, "", $password);
	if ($userObject->IsValidUser()) {
		$userObject->setupSession();
	}
	else $result = header("location: ../start/login_landing.php");
?>
