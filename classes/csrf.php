<?php

function csrfEnsureSession() {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
}

function csrfToken(): string {
  csrfEnsureSession();

  if (!(isset($_SESSION['csrf_token'])) || !(is_string($_SESSION['csrf_token']))) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  return $_SESSION['csrf_token'];
}

function csrfInput(): string {
  return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES) . '">';
}

function csrfSubmittedToken(): ?string {
  if (isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])) {
    return $_POST['csrf_token'];
  }

  if (isset($_SERVER['HTTP_X_CSRF_TOKEN']) && is_string($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    return $_SERVER['HTTP_X_CSRF_TOKEN'];
  }

  return NULL;
}

function csrfValidateRequest() {
  csrfEnsureSession();

  $submittedToken = csrfSubmittedToken();
  if ($submittedToken === NULL || !(isset($_SESSION['csrf_token'])) || !(hash_equals($_SESSION['csrf_token'], $submittedToken))) {
    http_response_code(403);
    exit('Invalid request token.');
  }
}
?>
