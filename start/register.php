<!DOCTYPE html>

<?php
	$userName = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	require_once('../classes/classes_database.php');

	$userObject = new User($userName, $email, $password);
	if (!($userObject->alreadyExists())) {
			if ($userObject->submituser()) {
				$result = header("location: ../start/login_landing.php");
			} else "registration failed for some reason";
	} else echo "The username is already registered."
 ?>
