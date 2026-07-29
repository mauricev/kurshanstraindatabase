<?php

		require_once(__DIR__ . '/../classes/csrf.php');
		csrfValidateRequest();
		$_SESSION = [];

		expireSessionCookie();

		session_destroy();

		$result = header("location: ../start/login_landing.php");
	?>
