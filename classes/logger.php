<?php

class Logger {

    public function isLogFileSet() {
      return isset($_SESSION['loggerFileName']);
    }

    public function createLogFile() {
      // make up a random name
      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
      $_SESSION['loggerFileName'] = str_shuffle($permitted_chars);
      // create the file initially
      $theFileName = $_SESSION['loggerFileName'];
      // possible BUG, was using a relative reference, now uses document root
      $theFileReference = fopen($_SERVER['DOCUMENT_ROOT'] . "/logging_files/$theFileName", 'a+');
      if ($theFileReference) {
        fclose($theFileReference);
      }
    }

    public function returnLogFileName() {
      if (!($this->isLogFileSet())) {
        $this->createLogFile();
      }
      return $_SESSION['loggerFileName'];
    }

    public function __construct() {
      $theFileName = $this->returnLogFileName();
    }

  public function appendToLog ($stringToAppend_param) {
    $theFileName = $this->returnLogFileName();
    $theFileReference = fopen($_SERVER['DOCUMENT_ROOT'] . "/logging_files/$theFileName", 'a+');
    if ($theFileReference !== false) {
      $theWriteResult = fwrite($theFileReference, $stringToAppend_param . "\n");
      fclose($theFileReference);
    }
  }

  public function returnLog () {
    $theFileName = $this->returnLogFileName();
    $theFileArray = file($_SERVER['DOCUMENT_ROOT'] . "/logging_files/$theFileName", FILE_IGNORE_NEW_LINES);
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
