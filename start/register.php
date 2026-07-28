<?php
	require_once('../classes/csrf.php');
	csrfValidateRequest();

	$userName = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	require_once('../classes/classes_database.php');

		$userObject = new User($userName, $email, $password, "local");
		if (!($userObject->alreadyExists())) {
				if ($userObject->submituser()) {
					header("location: ../start/login_landing.php");
					exit();
				}

				http_response_code(500);
				echo "Registration failed.";
				exit();
		}

		http_response_code(409);
		echo "The username is already registered.";
	 ?>
