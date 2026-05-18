<?php

class AppSettings {
  private const SETTINGS_FILE = '/users/maurice/peri-password/app_settings.php';
  private const OKTA_SETTINGS_FILE = '/users/maurice/peri-password/okta_settings.php';

  private static ?array $settings_prop = NULL;

  private static function settings(): array {
    if (self::$settings_prop === NULL) {
      if (!is_readable(self::SETTINGS_FILE)) {
        throw new RuntimeException('App settings file not found: ' . self::SETTINGS_FILE);
      }
      $settings = require(self::SETTINGS_FILE);
      if (!is_array($settings)) {
        throw new RuntimeException('App settings file must return an array.');
      }
      self::$settings_prop = $settings;
    }
    return self::$settings_prop;
  }

  public static function get(string $key): mixed {
    $settings = self::settings();
    if (!array_key_exists($key, $settings)) {
      throw new RuntimeException("Missing app setting: $key");
    }
    return $settings[$key];
  }

  public static function oktaSettings(): array {
    if (!is_readable(self::OKTA_SETTINGS_FILE)) {
      throw new RuntimeException('Okta settings file not found: ' . self::OKTA_SETTINGS_FILE);
    }

    $settings = require(self::OKTA_SETTINGS_FILE);
    if (!is_array($settings)) {
      throw new RuntimeException('Okta settings file must return an array.');
    }

    foreach (['issuer', 'client_id', 'client_secret', 'redirect_uri'] as $key) {
      if (!array_key_exists($key, $settings) || $settings[$key] === '') {
        throw new RuntimeException("Missing Okta setting: $key");
      }
    }

    return $settings;
  }

  public static function databaseName(): string {
    return self::get('db_name');
  }

  public static function databaseUser(): string {
    return self::get('db_user');
  }

  public static function databasePassword(): string {
    return self::get('db_pass');
  }

  public static function instanceKey(): string {
    return self::get('instance_key');
  }

  public static function labName(): string {
    $instanceKey = self::instanceKey();

    return match ($instanceKey) {
      'peri' => 'KurshanLab',
      'elisa' => 'FrankelLab',
      default => throw new RuntimeException("Unsupported instance key: $instanceKey"),
    };
  }

  public static function labElementPrefix(): string {
    return self::instanceKey() === 'elisa' ? 'grz' : 'kur';
  }

  public static function strainPrefix(): string {
    return self::instanceKey() === 'elisa' ? 'UPS' : 'PTK';
  }

  public static function hasLabElementPrefix(string $name): bool {
    return str_starts_with($name, self::labElementPrefix());
  }

  public static function hasStrainPrefix(string $name): bool {
    return str_starts_with($name, self::strainPrefix());
  }
}
