<?php

  include_once('../classes/session.php');

  //header('Content-Type: application/json');

  if (isset($_POST['strainID'])) {
    $strainID = $_POST['strainID'];
    $whichProcess = $_POST['whichProcess'];
    $checkBoxState = $_POST['checkBoxState'];
  
    require_once("../classes/classes_load_elements.php");

    $geneElementObjectToEdit = new LoadParentStrains();
    $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($strainID);

    $isolationNameNotUsed = "";
    $thawedDateNotUsed = "";
    $commentNotUsed = "";
    $parentStrainsNotUsed = "";
    $allelesNotUsed = "";
    $transGenesNotUsed = "";
    $balancersNotUsed = "";
    $contributorNotUsed[0] = "";
    $frozenLocationNotUsed = "";
    $nitrogenNotUsed = "";
    $lastVialStateNotUsed = "";
    $lastVialer[0] = "";

    $handOffDate = "";
    $frozenDate = "";
    $survivalDate = "";
    $movedDate = "";

    // we need to only set the strain name and the date
    $theDate = date('Y-m-d');

    switch($whichProcess) {
      case "handoff":
        $handOffDate = $theDate;
        break;
      case "frozen":
        $frozenDate = $theDate;
        if ($checkBoxState == "true") {
          $frozenDate = null;
        }
        break;
      case "survival":
        $survivalDate = $theDate;
        if ($checkBoxState == "true") {
          $survivalDate = null;
        }
        break;
      case "finaldestination":
        $movedDate = $theDate;
        break;
    }

    $strainObject = new Strain($geneElementArrayToEdit['strainName_col'], $isolationNameNotUsed, $frozenDate, $thawedDateNotUsed, $commentNotUsed,$parentStrainsNotUsed,$allelesNotUsed,$transGenesNotUsed,$balancersNotUsed,$contributorNotUsed[0],$frozenLocationNotUsed,$nitrogenNotUsed,$lastVialStateNotUsed,$lastVialer[0],$handOffDate, $survivalDate, $movedDate);

    $strainObject->processStrainStatus($strainID,$whichProcess); 

  } else {
    error_log("strain id missing");
  }

?>
