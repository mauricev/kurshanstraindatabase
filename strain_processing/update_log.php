<?php

  include_once('../classes/session.php');
  require_once('../classes/logger.php');
  $theLogObject = new Logger();
  $theLogString = $theLogObject->returnLog();
  echo $theLogString;
?>
