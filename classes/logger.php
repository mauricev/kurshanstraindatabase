<?php
require_once(__DIR__ . '/classes_app_settings.php');

class Logger {
    private function returnLogPath($fileName_param) {
      return AppSettings::loggingFilesDirectory() . "/$fileName_param";
    }

    private function rememberLoggingError($message_param) {
      $_SESSION['loggerLastError'] = $message_param;
    }

    private function openLogFile($logPath_param, $mode_param) {
      $theErrorMessage = "";
      set_error_handler(function($errno, $errstr) use (&$theErrorMessage) {
        $theErrorMessage = $errstr;
        return true;
      });

      $theFileReference = fopen($logPath_param, $mode_param);
      restore_error_handler();

      if ($theFileReference === false) {
        $this->rememberLoggingError("fopen($logPath_param, $mode_param) failed: " . ($theErrorMessage !== "" ? $theErrorMessage : "unknown error"));
      }

      return $theFileReference;
    }

    private function createUniqueLogFile() {
      $theErrorMessage = "";
      $theLogDirectory = AppSettings::loggingFilesDirectory();
      set_error_handler(function($errno, $errstr) use (&$theErrorMessage) {
        $theErrorMessage = $errstr;
        return true;
      });

      $theLogPath = tempnam($theLogDirectory, 'peri-log-');
      restore_error_handler();

      if ($theLogPath === false) {
        $this->rememberLoggingError("tempnam($theLogDirectory, peri-log-) failed: " . ($theErrorMessage !== "" ? $theErrorMessage : "unknown error"));
        return false;
      }

      return $theLogPath;
    }

    public function isLogFileSet() {
      return isset($_SESSION['loggerFileName']) && ($_SESSION['loggerFileName'] !== "");
    }

    public function createLogFile() {
      unset($_SESSION['loggerLastError']);

      $theLogPath = $this->createUniqueLogFile();
      if ($theLogPath !== false) {
        $_SESSION['loggerFileName'] = basename($theLogPath);
        return true;
      }

      unset($_SESSION['loggerFileName']);
      return false;
    }

    public function returnLogFileName() {
      if (!($this->isLogFileSet()) || !(is_file($this->returnLogPath($_SESSION['loggerFileName'])))) {
        if (!($this->createLogFile())) {
          return false;
        }
      }
      return $_SESSION['loggerFileName'];
    }

    public function __construct() {
      $theFileName = $this->returnLogFileName();
    }

  public function appendToLog ($stringToAppend_param) {
    $theFileName = $this->returnLogFileName();
    if ($theFileName === false) {
      return false;
    }

    $theFileReference = $this->openLogFile($this->returnLogPath($theFileName), 'a+');
    if ($theFileReference !== false) {
      $theWriteResult = fwrite($theFileReference, $stringToAppend_param . "\n");
      fclose($theFileReference);
      return ($theWriteResult !== false);
    }

    return false;
  }

  public function returnDiagnostics() {
    $theLogDirectory = AppSettings::loggingFilesDirectory();
    $theSessionLogFile = $this->isLogFileSet() ? $_SESSION['loggerFileName'] : "";
    $theSessionLogPath = $theSessionLogFile !== "" ? $this->returnLogPath($theSessionLogFile) : "";
    $thePhpUser = "unknown";
    $thePhpEffectiveUID = "unknown";
    $thePosixFunctionsAvailable = "no";
    $theUIDLookupStatus = "POSIX functions unavailable";

    if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
      $thePosixFunctionsAvailable = "yes";
      $thePhpEffectiveUID = posix_geteuid();
      $theUserInfo = posix_getpwuid($thePhpEffectiveUID);
      if ($theUserInfo !== false && isset($theUserInfo['name'])) {
        $thePhpUser = $theUserInfo['name'];
        $theUIDLookupStatus = "resolved";
      } else {
        $theUIDLookupStatus = "no user name found for UID";
      }
    }

    return [
      'log directory' => $theLogDirectory,
      'directory exists' => file_exists($theLogDirectory) ? 'yes' : 'no',
      'is directory' => is_dir($theLogDirectory) ? 'yes' : 'no',
      'directory readable' => is_readable($theLogDirectory) ? 'yes' : 'no',
      'directory writable' => is_writable($theLogDirectory) ? 'yes' : 'no',
      'POSIX functions available' => $thePosixFunctionsAvailable,
      'PHP effective uid' => $thePhpEffectiveUID,
      'PHP effective user' => $thePhpUser,
      'UID lookup status' => $theUIDLookupStatus,
      'session log file' => $theSessionLogFile !== "" ? $theSessionLogFile : 'not set',
      'session log path' => $theSessionLogPath !== "" ? $theSessionLogPath : 'not set',
      'session log exists' => $theSessionLogPath !== "" && is_file($theSessionLogPath) ? 'yes' : 'no',
      'last logging error' => $_SESSION['loggerLastError'] ?? 'none recorded',
    ];
  }

  public function returnLog () {
    $theFileName = $this->returnLogFileName();
    if ($theFileName === false) {
      return "";
    }

    $theLogPath = $this->returnLogPath($theFileName);
    if (!(is_file($theLogPath) && is_readable($theLogPath))) {
      return "";
    }

    $theFileArray = file($theLogPath, FILE_IGNORE_NEW_LINES);
    if ($theFileArray !== false) {
      // the last entries are the first ones done, so we need to reverse the array
      $theFileArrayReversed = array_reverse($theFileArray);
      $theLogString = "";
      $theCounter = 1;
      // the counter contains the count for each line and we prepend spaces to accommodate for up to 3 digits worth of space
      // to line up the entries
      // that is, up to 999 entries per session
      foreach ($theFileArrayReversed as $theLine) {
        if ($theCounter < 100) {
          $theCounter = "  " . $theCounter;
          if ($theCounter < 10) {
            $theCounter = "  " . $theCounter;
          }
        }
        $theLogString = $theLogString . $theCounter . " " . $theLine . "\n";
        $theCounter++;
      }
      return $theLogString;
    }
  }
}
?>
