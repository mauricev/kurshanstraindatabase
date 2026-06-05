<?php

require_once(__DIR__ . '/classes_app_settings.php');

class OktaAuth {
  private array $settings_prop;
  private string $issuer_prop;

  public function __construct() {
    $this->settings_prop = AppSettings::oktaSettings();
    $this->issuer_prop = rtrim($this->settings_prop['issuer'], '/');
  }

  public function redirectToAuthorize() {
    $this->ensureSession();

    $_SESSION['okta_state'] = bin2hex(random_bytes(32));
    $_SESSION['okta_nonce'] = bin2hex(random_bytes(32));

    $query = http_build_query([
      'client_id' => $this->settings_prop['client_id'],
      'redirect_uri' => $this->settings_prop['redirect_uri'],
      'response_type' => 'code',
      'scope' => 'openid profile email groups',
      'state' => $_SESSION['okta_state'],
      'nonce' => $_SESSION['okta_nonce'],
    ]);

    header('location: ' . $this->issuer_prop . '/v1/authorize?' . $query);
    exit();
  }

  public function claimsFromCallback(array $queryParams): array {
    $this->ensureSession();

    if (isset($queryParams['error'])) {
      $description = $queryParams['error_description'] ?? $queryParams['error'];
      throw new RuntimeException('Okta login failed: ' . $description);
    }

    if (!(isset($queryParams['code'], $queryParams['state']))) {
      throw new RuntimeException('Okta callback did not include a code and state.');
    }

    if (!(isset($_SESSION['okta_state'])) || !(hash_equals($_SESSION['okta_state'], $queryParams['state']))) {
      throw new RuntimeException('Okta callback state did not match.');
    }

    $tokens = $this->exchangeCodeForTokens($queryParams['code']);
    if (!(isset($tokens['id_token']))) {
      throw new RuntimeException('Okta token response did not include an ID token.');
    }

    $claims = $this->verifyIDToken($tokens['id_token']);

    if (isset($tokens['access_token'])) {
      $userInfoClaims = $this->fetchUserInfo($tokens['access_token']);
      if (($userInfoClaims['sub'] ?? '') !== $claims['sub']) {
        throw new RuntimeException('Okta UserInfo subject did not match the ID token subject.');
      }

      $claims = array_merge($claims, $userInfoClaims);
    }

    unset($_SESSION['okta_state'], $_SESSION['okta_nonce']);

    return $claims;
  }

  public function isAuthorizedUser(array $claims): bool {
    $groups = $this->groupsFromClaims($claims);
    return in_array('frankeldatabase_users', $groups, true) || in_array('frankeldatabase_admin_users', $groups, true);
  }

  public function isAdmin(array $claims): bool {
    return in_array('frankeldatabase_admin_users', $this->groupsFromClaims($claims), true);
  }

  public function authorizationDebug(array $claims): array {
    $groups = $this->groupsFromClaims($claims);
    return [
      'required_groups' => [
        'frankeldatabase_users',
        'frankeldatabase_admin_users',
      ],
      'groups_claim_exists' => array_key_exists('groups', $claims),
      'groups_claim_type' => array_key_exists('groups', $claims) ? gettype($claims['groups']) : NULL,
      'groups_returned' => $groups,
      'authorized' => $this->isAuthorizedUser($claims),
      'admin' => $this->isAdmin($claims),
      'claim_keys_returned' => array_keys($claims),
      'subject_present' => isset($claims['sub']) && is_string($claims['sub']) && $claims['sub'] !== '',
      'email_present' => isset($claims['email']) && is_string($claims['email']) && $claims['email'] !== '',
    ];
  }

  public function displayName(array $claims): string {
    foreach (['name', 'preferred_username', 'email'] as $key) {
      if (isset($claims[$key]) && is_string($claims[$key]) && trim($claims[$key]) !== '') {
        return trim($claims[$key]);
      }
    }

    return $claims['sub'];
  }

  public function email(array $claims): string {
    if (isset($claims['email']) && is_string($claims['email']) && trim($claims['email']) !== '') {
      return trim($claims['email']);
    }

    if (isset($claims['preferred_username']) && is_string($claims['preferred_username']) && str_contains($claims['preferred_username'], '@')) {
      return trim($claims['preferred_username']);
    }

    return $claims['sub'] . '@okta.invalid';
  }

  private function ensureSession() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  private function groupsFromClaims(array $claims): array {
    if (!(isset($claims['groups']))) {
      return [];
    }

    if (is_array($claims['groups'])) {
      return $claims['groups'];
    }

    if (is_string($claims['groups'])) {
      return [$claims['groups']];
    }

    return [];
  }

  private function exchangeCodeForTokens(string $code): array {
    $headers = [
      'Authorization: Basic ' . base64_encode($this->settings_prop['client_id'] . ':' . $this->settings_prop['client_secret']),
      'Accept: application/json',
    ];

    return $this->postForm($this->issuer_prop . '/v1/token', [
      'grant_type' => 'authorization_code',
      'code' => $code,
      'redirect_uri' => $this->settings_prop['redirect_uri'],
    ], $headers);
  }

  private function fetchUserInfo(string $accessToken): array {
    return $this->fetchJson($this->issuer_prop . '/v1/userinfo', [
      'Authorization: Bearer ' . $accessToken,
      'Accept: application/json',
    ]);
  }

  private function verifyIDToken(string $idToken): array {
    $parts = explode('.', $idToken);
    if (count($parts) !== 3) {
      throw new RuntimeException('Okta ID token is not a JWT.');
    }

    $header = json_decode($this->base64UrlDecode($parts[0]), true);
    $claims = json_decode($this->base64UrlDecode($parts[1]), true);

    if (!(is_array($header)) || !(is_array($claims))) {
      throw new RuntimeException('Okta ID token could not be decoded.');
    }

    if (($header['alg'] ?? '') !== 'RS256' || !(isset($header['kid']))) {
      throw new RuntimeException('Okta ID token uses an unexpected signing algorithm.');
    }

    $key = $this->jwkForKeyID($header['kid']);
    $signatureIsValid = openssl_verify($parts[0] . '.' . $parts[1], $this->base64UrlDecode($parts[2]), $this->jwkToPem($key), OPENSSL_ALGO_SHA256);

    if ($signatureIsValid !== 1) {
      throw new RuntimeException('Okta ID token signature is invalid.');
    }

    $this->validateClaims($claims);

    return $claims;
  }

  private function validateClaims(array $claims) {
    $now = time();
    $leeway = 120;

    if (($claims['iss'] ?? '') !== $this->issuer_prop) {
      throw new RuntimeException('Okta ID token issuer is invalid.');
    }

    $audience = $claims['aud'] ?? NULL;
    $audienceMatches = is_array($audience)
      ? in_array($this->settings_prop['client_id'], $audience, true)
      : $audience === $this->settings_prop['client_id'];

    if (!($audienceMatches)) {
      throw new RuntimeException('Okta ID token audience is invalid.');
    }

    if (is_array($audience) && count($audience) > 1 && (($claims['azp'] ?? '') !== $this->settings_prop['client_id'])) {
      throw new RuntimeException('Okta ID token authorized party is invalid.');
    }

    if (!(isset($claims['sub'])) || !(is_string($claims['sub'])) || $claims['sub'] === '') {
      throw new RuntimeException('Okta ID token subject is missing.');
    }

    if (!(isset($claims['exp'])) || $claims['exp'] < ($now - $leeway)) {
      throw new RuntimeException('Okta ID token is expired.');
    }

    if (isset($claims['nbf']) && $claims['nbf'] > ($now + $leeway)) {
      throw new RuntimeException('Okta ID token is not valid yet.');
    }

    if (!(isset($_SESSION['okta_nonce'])) || !(isset($claims['nonce'])) || !(hash_equals($_SESSION['okta_nonce'], $claims['nonce']))) {
      throw new RuntimeException('Okta ID token nonce did not match.');
    }
  }

  private function jwkForKeyID(string $keyID): array {
    $jwks = $this->fetchJson($this->issuer_prop . '/v1/keys');
    foreach ($jwks['keys'] ?? [] as $key) {
      if (($key['kid'] ?? '') === $keyID) {
        return $key;
      }
    }

    throw new RuntimeException('No matching Okta signing key was found.');
  }

  private function fetchJson(string $url, array $headers = []): array {
    $context = NULL;
    if (count($headers) > 0) {
      $context = stream_context_create([
        'http' => [
          'method' => 'GET',
          'header' => implode("\r\n", $headers),
          'ignore_errors' => true,
        ],
      ]);
    }

    $response = file_get_contents($url, false, $context);
    if ($response === false) {
      throw new RuntimeException('Unable to fetch Okta metadata.');
    }

    $decoded = json_decode($response, true);
    if (!(is_array($decoded))) {
      throw new RuntimeException('Okta returned invalid JSON.');
    }

    return $decoded;
  }

  private function postForm(string $url, array $fields, array $headers): array {
    $body = http_build_query($fields);
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $requestDebug = [
      'url' => $url,
      'grant_type' => $fields['grant_type'] ?? NULL,
      'redirect_uri' => $fields['redirect_uri'] ?? NULL,
    ];

    $context = stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $body,
        'ignore_errors' => true,
      ],
    ]);

    $response = file_get_contents($url, false, $context);
    $responseHeaders = $http_response_header ?? [];
    if ($response === false) {
      $lastError = error_get_last();
      throw new RuntimeException('Unable to exchange Okta authorization code. Debug: ' . json_encode([
        'request' => $requestDebug,
        'response_headers' => $responseHeaders,
        'php_error' => $lastError['message'] ?? NULL,
      ], JSON_UNESCAPED_SLASHES));
    }

    $decoded = json_decode($response, true);
    if (!(is_array($decoded))) {
      throw new RuntimeException('Okta token endpoint returned invalid JSON. Debug: ' . json_encode([
        'request' => $requestDebug,
        'response_headers' => $responseHeaders,
        'response_body' => $response,
      ], JSON_UNESCAPED_SLASHES));
    }

    if (isset($decoded['error'])) {
      $description = $decoded['error_description'] ?? $decoded['error'];
      throw new RuntimeException('Okta token exchange failed: ' . $description . ' Debug: ' . json_encode([
        'request' => $requestDebug,
        'response_headers' => $responseHeaders,
        'response_body' => $decoded,
      ], JSON_UNESCAPED_SLASHES));
    }

    return $decoded;
  }

  private function base64UrlDecode(string $value): string {
    $padding = strlen($value) % 4;
    if ($padding > 0) {
      $value .= str_repeat('=', 4 - $padding);
    }

    return base64_decode(strtr($value, '-_', '+/'));
  }

  private function jwkToPem(array $jwk): string {
    if (!(isset($jwk['n'], $jwk['e']))) {
      throw new RuntimeException('Okta signing key is missing RSA parameters.');
    }

    $modulus = $this->asn1Integer($this->base64UrlDecode($jwk['n']));
    $publicExponent = $this->asn1Integer($this->base64UrlDecode($jwk['e']));
    $rsaPublicKey = $this->asn1Sequence($modulus . $publicExponent);

    $rsaEncryptionOID = "\x06\x09\x2A\x86\x48\x86\xF7\x0D\x01\x01\x01";
    $algorithmIdentifier = $this->asn1Sequence($rsaEncryptionOID . "\x05\x00");
    $subjectPublicKey = "\x03" . $this->asn1Length(strlen($rsaPublicKey) + 1) . "\x00" . $rsaPublicKey;
    $subjectPublicKeyInfo = $this->asn1Sequence($algorithmIdentifier . $subjectPublicKey);

    return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($subjectPublicKeyInfo), 64, "\n") . "-----END PUBLIC KEY-----\n";
  }

  private function asn1Integer(string $value): string {
    $value = ltrim($value, "\x00");
    if ($value === '') {
      $value = "\x00";
    }

    if ((ord($value[0]) & 0x80) !== 0) {
      $value = "\x00" . $value;
    }

    return "\x02" . $this->asn1Length(strlen($value)) . $value;
  }

  private function asn1Sequence(string $value): string {
    return "\x30" . $this->asn1Length(strlen($value)) . $value;
  }

  private function asn1Length(int $length): string {
    if ($length < 128) {
      return chr($length);
    }

    $bytes = '';
    while ($length > 0) {
      $bytes = chr($length & 0xff) . $bytes;
      $length >>= 8;
    }

    return chr(0x80 | strlen($bytes)) . $bytes;
  }
}
?>
