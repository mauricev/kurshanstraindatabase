<?php

require_once(__DIR__ . '/classes_app_settings.php');

class Peri_Database {
  static protected $pdo_prop;
  protected $tableName_prop;
  protected $columnWithElementName_prop;

  public function __construct() {
    // The settings file is located outside the Apache environment for security.
    $db_name = AppSettings::databaseName();
    $db_user = AppSettings::databaseUser();
    $db_pass = AppSettings::databasePassword();
    $dsn = "mysql:host=localhost;dbname=$db_name;charset=utf8mb4";
    $options =
    [
      PDO::ATTR_EMULATE_PREPARES   => true, //apparently with false under php 8 and current mysql results in packets out of order error
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      // we make this connection persistent since it's called from everywhere and we don't want to make unnecessary connections to the database
      PDO::ATTR_PERSISTENT => true,
    ];

    if (!(isset(Peri_Database::$pdo_prop))) {
      Peri_Database::$pdo_prop = new PDO($dsn, $db_user, $db_pass, $options);
    }
    // unset these so the creds don't hang around in memory
    unset($db_name,$db_user,$db_pass);
  }

  public function sqlPrepare ($prepareString_param) {
    return (Peri_Database::$pdo_prop->prepare($prepareString_param));
  }

  public function beginTransaction() {
    Peri_Database::$pdo_prop->beginTransaction();
  }

  public function lastInsertId() {
    return(Peri_Database::$pdo_prop->lastInsertId());
  }

  public function rollback() {
    Peri_Database::$pdo_prop->rollback();
  }

  public function commit() {
    Peri_Database::$pdo_prop->commit();
  }
}

class User extends Peri_Database {
  protected $actualName_prop;
  protected $actualEmail_prop;
  protected $actualInternalCode_prop;
  protected $actualHashedPassword_prop;
  protected $actualAuthProvider_prop;
  protected $actualVerified_prop = 1;
  protected $actualActiveStatus_prop = 1;
  protected $actualToken;

  public function __construct($name_param, $email_param, $internalCode_param, $authProvider_param) {

    $this->tableName_prop = 'author_table';
    $this->actualName_prop = $name_param;
    $this->actualEmail_prop = $email_param;
    $this->actualInternalCode_prop = $internalCode_param;
    $this->actualAuthProvider_prop = $authProvider_param;
    if (($this->actualAuthProvider_prop === "local") && ($internalCode_param != "")){
      $this->actualHashedPassword_prop = password_hash($internalCode_param, PASSWORD_DEFAULT);
    } else {
      $this->actualHashedPassword_prop = NULL;
    }
    // token is not currently used; I think the plan was to send the token in email
    // and then when going to to token page, it would compare with the saved token and that’s how it
    // would determine if the user was real
    $this->actualToken = bin2hex(random_bytes(50)); // generate unique token

    // must be last because it determines which user logs into the database
    parent::__construct();
  }

  public function alreadyExists () {
    $preparedSQLQuery = Peri_Database::$pdo_prop->prepare("SELECT COUNT(*) FROM $this->tableName_prop WHERE authorName_col = ?");

    $itemstoCheck = array($this->actualName_prop);
    $preparedSQLQuery->execute($itemstoCheck);

    $existingElement = $preparedSQLQuery->fetch();

    return ($existingElement["COUNT(*)"] == 1);
  }

  public function submitUser() {
    try {
      require_once(__DIR__ . '/classes_single_element.php');

      if (!(in_array($this->actualAuthProvider_prop, ["local","okta"], true))) {
        throw new Exception("invalid auth provider");
      }

      $theOIDCSub = NULL;
      if ($this->actualAuthProvider_prop === "okta") {
        $theOIDCSub = $this->actualInternalCode_prop;
      }

      $this->beginTransaction();

      $newContributorObject = new NewContributor($this->actualName_prop);
      $newContributorID = $newContributorObject->insertOurEntryAndReturnID();

      $preparedSQLInsert = Peri_Database::$pdo_prop->prepare("INSERT INTO $this->tableName_prop (authorName_col,email_col,hashedPassword_col,isActive_col, verified_col, contributor_fk, authProvider_col, oidcSub_col) VALUES (?,?,?,?,?,?,?,?)");

      $itemstoInsert = array($this->actualName_prop,$this->actualEmail_prop,$this->actualHashedPassword_prop, $this->actualActiveStatus_prop, $this->actualVerified_prop, $newContributorID, $this->actualAuthProvider_prop, $theOIDCSub);
      $userAdded = $preparedSQLInsert->execute($itemstoInsert);
      $this->commit();
      return ($preparedSQLInsert->rowCount() == 1);
    }
    catch(Exception $e) {
      if (Peri_Database::$pdo_prop->inTransaction()) {
        $this->rollback();
      }
      echo $e->getMessage();
      return false;
    }
  }

  public function IsValidUser() {
    $theUserIsValid = false;

    try {
      $preparedSQLQuery = $this->sqlPrepare("SELECT hashedPassword_col,isActive_col,authProvider_col FROM $this->tableName_prop WHERE authorName_col = ?");
      $itemsToCheck = array($this->actualName_prop);
      $preparedSQLQuery->execute($itemsToCheck);
      $existingElement = $preparedSQLQuery->fetch();

      if (($existingElement !== false) && $existingElement['isActive_col'] && ($existingElement['authProvider_col'] === "local") && ($existingElement['hashedPassword_col'] !== NULL)) {
        $theUserIsValid = password_verify($this->actualInternalCode_prop, $existingElement['hashedPassword_col']);
      }
      return $theUserIsValid;
    }
    catch(Exception $e) {
      echo $e->getMessage();
      return false;
    }
  }

  // we are inheriting from peri_database
  // when we call loadplasmid we are instantiating another class
  // it inherits from the database class. reference to database is static not the whole class
  // we use the session superglobal to determine who the current user is
  // this contains the user id, not the username
  public function IsCurrentUserAnEditor() {
    try {
      // we check if there’s a current user
      if (isset($_SESSION['user'])) {
        $preparedSQLQuery = $this->sqlPrepare("SELECT adminUser_col FROM $this->tableName_prop WHERE author_id = ?");
        $itemsToCheck = array($_SESSION['user']);
        $preparedSQLQuery->execute($itemsToCheck);
        $existingElement = $preparedSQLQuery->fetch();

        if ($existingElement['adminUser_col'] == 1) {
          return true;
        }
        return false;
      }
    }
    catch(Exception $e) {
      echo $e->getMessage();
      return false;
    }
  }

  public function fetchUserIdentify ($authorID) {
    $preparedSQLQuery = $this->sqlPrepare("SELECT authorName_col FROM $this->tableName_prop WHERE author_id = ?");

    $itemstoCheck = array($authorID);
    $preparedSQLQuery->execute($itemstoCheck);

    $existingElement = $preparedSQLQuery->fetch();

    return ($existingElement['authorName_col']);
  }

  /*

    $userObject = new User("","","","local");
    $author = $userObject->fetchUserIdentify($_SESSION['user']);
  */

  protected function fetchUserID () {
    $preparedSQLQuery = $this->sqlPrepare("SELECT author_id FROM $this->tableName_prop WHERE authorName_col = ?");

    $itemstoCheck = array($this->actualName_prop);
    $preparedSQLQuery->execute($itemstoCheck);

    $existingElement = $preparedSQLQuery->fetch();

    return ($existingElement['author_id']);
  }

  private function cleanOktaAuthorName(string $name, string $email): string {
    $baseName = trim($name);

    if ($baseName === "" && $email !== "") {
      $baseName = explode("@", $email)[0];
    }

    $baseName = preg_replace('/\s+/', ' ', $baseName);
    $baseName = preg_replace('/[^A-Za-z0-9 ._@-]/', '', $baseName);
    $baseName = trim($baseName);

    if ($baseName === "") {
      $baseName = "okta-user";
    }

    return substr($baseName, 0, 30);
  }

  private function uniqueAuthorName(string $baseName): string {
    $candidate = substr($baseName, 0, 30);
    $suffix = 1;

    while (true) {
      $preparedSQLQuery = $this->sqlPrepare("SELECT COUNT(*) FROM $this->tableName_prop WHERE authorName_col = ?");
      $preparedSQLQuery->execute([$candidate]);
      $existingElement = $preparedSQLQuery->fetch();

      if ($existingElement["COUNT(*)"] == 0) {
        return $candidate;
      }

      $suffixText = "-" . $suffix;
      $candidate = substr($baseName, 0, 30 - strlen($suffixText)) . $suffixText;
      $suffix++;
    }
  }

  private function contributorIDForName(string $name): int {
    $preparedSQLQuery = $this->sqlPrepare("SELECT contributor_id FROM contributor_table WHERE contributorName_col = ?");
    $preparedSQLQuery->execute([$name]);
    $existingContributor = $preparedSQLQuery->fetch();

    if ($existingContributor !== false) {
      return (int)$existingContributor['contributor_id'];
    }

    $preparedSQLInsert = $this->sqlPrepare("INSERT INTO contributor_table (contributorName_col, outside_contributor_col) VALUES (?, ?)");
    $preparedSQLInsert->execute([$name, 0]);

    return (int)$this->lastInsertId();
  }

  public function syncOktaUserAndReturnID(string $oidcSub, string $displayName, string $email, bool $isAdmin): int {
    $preparedSQLQuery = $this->sqlPrepare("SELECT author_id FROM $this->tableName_prop WHERE oidcSub_col = ?");
    $preparedSQLQuery->execute([$oidcSub]);
    $existingUser = $preparedSQLQuery->fetch();

    if ($existingUser !== false) {
      $preparedSQLUpdate = $this->sqlPrepare("UPDATE $this->tableName_prop SET email_col = ?, isActive_col = ?, verified_col = ?, adminUser_col = ?, authProvider_col = ? WHERE author_id = ?");
      $preparedSQLUpdate->execute([$email, 1, 1, $isAdmin ? 1 : 0, "okta", $existingUser['author_id']]);

      return (int)$existingUser['author_id'];
    }

    try {
      $this->beginTransaction();

      $baseAuthorName = $this->cleanOktaAuthorName($displayName, $email);
      $authorName = $this->uniqueAuthorName($baseAuthorName);
      $contributorID = $this->contributorIDForName($authorName);

      $preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop (authorName_col,email_col,hashedPassword_col,isActive_col, verified_col, adminUser_col, contributor_fk, authProvider_col, oidcSub_col) VALUES (?,?,?,?,?,?,?,?,?)");
      $preparedSQLInsert->execute([$authorName, $email, NULL, 1, 1, $isAdmin ? 1 : 0, $contributorID, "okta", $oidcSub]);
      $authorID = (int)$this->lastInsertId();

      $this->commit();

      return $authorID;
    }
    catch(Exception $e) {
      if (Peri_Database::$pdo_prop->inTransaction()) {
        $this->rollback();
      }

      throw $e;
    }
  }

  public function setupSessionForUserID(int $authorID) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
    $_SESSION['loggedin'] = true;
    $_SESSION['user'] = $authorID;
    error_log("in setupSessionForUserID, user is $authorID");
    $result = header("location: ../start/start.php");
    exit();
  }

  public function setupSession() {
    $user = $this->fetchUserID();
    $this->setupSessionForUserID($user);
  }
}
?>
