<?php
session_start();
if (!(isset($_SESSION['loggedin']))) {
  // if the session loggedin is not set, we are not logged in, redirect to the login page
  header("location: ../start/login_landing.php");
}

function ourheader($page) {
  header($page);
}
?>
