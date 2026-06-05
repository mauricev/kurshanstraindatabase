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
    throw new RuntimeException('Your Okta account is not assigned to the Frankel database user or admin group. Debug: ' . json_encode($oktaAuth->authorizationDebug($claims), JSON_UNESCAPED_SLASHES));
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
  $oktaDebugMessage = $e->getMessage();
  http_response_code(401);
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo AppSettings::labName(); ?> Strain Database</title>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
  <script>
    console.error('Okta login failed', <?php echo json_encode($oktaDebugMessage, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);
  </script>
</head>
<body class="bg-light">
  <div class="container">
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
      <h2><?php echo AppSettings::labName(); ?> Strain Database</h2>
      <p class="lead">Login failed</p>
      <p><?php echo htmlspecialchars($e->getMessage(), ENT_QUOTES); ?></p>
      <div style="margin: 1rem auto; max-width: 960px; text-align: left;">
        <h3>Okta debug details</h3>
        <pre style="white-space: pre-wrap; overflow-wrap: anywhere; padding: 1rem; background: #f8f9fa; border: 1px solid #ced4da;"><?php echo htmlspecialchars($oktaDebugMessage, ENT_QUOTES); ?></pre>
      </div>
      <a class="btn btn-primary" href="../start/login_landing.php">Try again</a>
    </div>
  </div>
</body>
</html>
<?php
}
?>
