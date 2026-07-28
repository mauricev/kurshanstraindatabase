<?php

  include_once('../classes/session.php');
  require_once('../classes/logger.php');
  header('Content-Type: text/plain; charset=utf-8');
  $theLogObject = new Logger();
  $theLogString = $theLogObject->returnLog();
  echo $theLogString;
?>
