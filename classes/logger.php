<?php

class Logger {
    private function returnLogPath($fileName_param) {
      return $_SERVER['DOCUMENT_ROOT'] . "/logging_files/$fileName_param";
    }

    public function isLogFileSet() {
      return isset($_SESSION['loggerFileName']) && ($_SESSION['loggerFileName'] !== "");
    }

    public function createLogFile() {
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

      for ($attemptNumber = 0; $attemptNumber < 5; $attemptNumber++) {
        $theFileName = str_shuffle($permitted_chars);
        $theFileReference = @fopen($this->returnLogPath($theFileName), 'x');
        if ($theFileReference !== false) {
          fclose($theFileReference);
          $_SESSION['loggerFileName'] = $theFileName;
          return true;
        }
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

    $theFileReference = @fopen($this->returnLogPath($theFileName), 'a+');
    if ($theFileReference !== false) {
      $theWriteResult = fwrite($theFileReference, $stringToAppend_param . "\n");
      fclose($theFileReference);
      return ($theWriteResult !== false);
    }

    return false;
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
      return htmlspecialchars($theLogString,ENT_QUOTES);
    }
  }
}
?>
