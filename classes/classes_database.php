<?php

class Peri_Database {
  static protected $pdo_prop;
  protected $tableName_prop;
  protected $columnWithElementName_prop;

  public function __construct() {
    // this account has full permissions for the database and nothing else
    // The password is located outside the Apache environment for security
    //this path is created on the server to match my test environment
    include("/users/maurice/peri-password/db_settings.php");
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
  protected $actualPassword_prop;
  protected $actualHashedPassword_prop;
  protected $actualVerified_prop = 1;
  protected $actualActiveStatus_prop = 1;
  protected $actualToken;

  public function __construct($name_param, $email_param, $password_param) {

    $this->tableName_prop = 'author_table';
    $this->actualName_prop = $name_param;
    $this->actualEmail_prop = $email_param;
    $this->actualPassword_prop = $password_param;
    if ($password_param != ""){
      $this->actualHashedPassword_prop = password_hash($password_param, PASSWORD_DEFAULT);
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
      $preparedSQLInsert = Peri_Database::$pdo_prop->prepare("INSERT INTO $this->tableName_prop (authorName_col,email_col,hashedPassword_col,isActive_col, verified_col) VALUES (?,?,?,?,?)");

      $itemstoInsert = array($this->actualName_prop,$this->actualEmail_prop,$this->actualHashedPassword_prop, $this->actualActiveStatus_prop, $this->actualVerified_prop);
      $userAdded = $preparedSQLInsert->execute($itemstoInsert);
      return ($preparedSQLInsert->rowCount() == 1);
    }
    catch(Exception $e) {
      echo $e->getMessage();
      return false;
    }
  }

  public function IsValidUser() {
    $theUserIsValid = false;

    try {
      $preparedSQLQuery = $this->sqlPrepare("SELECT hashedPassword_col,isActive_col FROM $this->tableName_prop WHERE authorName_col = ?");
      $itemsToCheck = array($this->actualName_prop);
      $preparedSQLQuery->execute($itemsToCheck);
      $existingElement = $preparedSQLQuery->fetch();

      if ($existingElement['isActive_col']) {
        $theUserIsValid = password_verify($this->actualPassword_prop, $existingElement['hashedPassword_col']);
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

    $userObject = new User("","","");
    $author = $userObject->fetchUserIdentify($_SESSION['user']);
  */

  protected function fetchUserID () {
    $preparedSQLQuery = $this->sqlPrepare("SELECT author_id FROM $this->tableName_prop WHERE authorName_col = ?");

    $itemstoCheck = array($this->actualName_prop);
    $preparedSQLQuery->execute($itemstoCheck);

    $existingElement = $preparedSQLQuery->fetch();

    return ($existingElement['author_id']);
  }

  public function setupSession() {
    session_start();
    $_SESSION['loggedin'] = true;
    $user = $this->fetchUserID();
    $_SESSION['user'] = $user;
    error_log("in setupSession, user is $user");
    $result = header("location: ../start/start.php");
    exit();
  }
}
?>
