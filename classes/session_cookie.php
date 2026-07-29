<?php

function configureSessionCookie() {
  if (session_status() !== PHP_SESSION_NONE) {
    return;
  }

  $params = session_get_cookie_params();
  session_set_cookie_params([
    'lifetime' => $params['lifetime'],
    'path' => $params['path'] ?: '/',
    'domain' => $params['domain'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
}

function startSecureSession() {
  if (session_status() === PHP_SESSION_ACTIVE) {
    return;
  }

  configureSessionCookie();
  session_start();
}

function expireSessionCookie() {
  if (!(ini_get("session.use_cookies"))) {
    return;
  }

  $params = session_get_cookie_params();
  setcookie(session_name(), '', [
    'expires' => time() - 42000,
    'path' => $params['path'],
    'domain' => $params['domain'],
    'secure' => $params['secure'],
    'httponly' => $params['httponly'],
    'samesite' => $params['samesite'] ?? 'Lax',
  ]);
}

?>
