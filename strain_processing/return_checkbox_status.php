<?php

  include_once('../classes/session.php');
  ob_start(); // we need to run this to "clean up" the return of theCheckBoxState

  //header('Content-Type: text/plain');
  header('Content-Type: application/json');

  if (isset($_POST['strainID'])) {
    $strainID = $_POST['strainID'];
    $whichProcess = $_POST['whichProcess'];
    
    require_once("../classes/classes_load_elements.php");

    $geneElementObjectToEdit = new LoadParentStrains();
    $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($strainID);

    switch($whichProcess) {
      case "frozen":
        //$theCheckBoxState = ($geneElementArrayToEdit['dateFrozen_col'] == null) ? "false" : "true";
        $theCheckBoxState = $geneElementArrayToEdit['dateFrozen_col'];
        break;
      case "survival":
        //$theCheckBoxState = ($geneElementArrayToEdit['dateSurvived_col'] == null) ? "false" : "true";
        $theCheckBoxState = $geneElementArrayToEdit['dateSurvived_col'];
        break;
    }
    ob_end_clean();
    
    if ($theCheckBoxState == null) {
      $theCheckBoxState = "null";
    }
    echo json_encode($theCheckBoxState);
  } else {
    //error_log("strain id missing");
    echo json_encode(array('error' => "strain id missing"));
  }
