<?php

require_once(__DIR__ . '/../classes/classes_database.php');
require_once(__DIR__ . '/../classes/classes_okta.php');

if (AppSettings::instanceKey() !== 'elisa') {
  header("location: ../start/login_landing.php");
  exit();
}

try {
  $oktaAuth = new OktaAuth();
  $claims = $oktaAuth->claimsFromCallback($_GET);

  if (!($oktaAuth->isAuthorizedUser($claims))) {
    throw new RuntimeException('Your Okta account is not assigned to the Frankel database user or admin group.');
  }

  $userObject = new User("", "", "", "okta");
  $authorID = $userObject->syncOktaUserAndReturnID(
    $claims['sub'],
    $oktaAuth->displayName($claims),
    $oktaAuth->email($claims),
    $oktaAuth->isAdmin($claims)
  );

  $userObject->setupSessionForUserID($authorID);
}
catch(Exception $e) {
  error_log('Okta callback failed: ' . $e->getMessage());
  http_response_code(401);
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo AppSettings::labName(); ?> Strain Database</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
</head>
<body class="bg-light">
  <div class="container">
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
      <h2><?php echo AppSettings::labName(); ?> Strain Database</h2>
      <p class="lead">Login failed</p>
      <p><?php echo htmlspecialchars($e->getMessage(), ENT_QUOTES); ?></p>
      <a class="btn btn-primary" href="../start/login_landing.php">Try again</a>
    </div>
  </div>
</body>
</html>
<?php
}
?>
