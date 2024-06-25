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

    $highValueStrainReasonNotUsed[0] = null;

    // if we are setting these, they get their existing values unless isNitrogenFreezeDateSet is not set
    $isNitrogenFreezeDateSet = $geneElementArrayToEdit['initial_nitrogenFrozen'];
    $nitrogenFreezeDate = $geneElementArrayToEdit['date_nitrogenFrozen'];

    // we need to only set the strain name and the date
    
    $theDate = date('Y-m-d');

    switch($whichProcess) {
      case "handoff":
        $handOffDate = $theDate;
        break;
      case "sendback":
        $handOffDate = null;
        $movedDate = null;
        break;
      case "frozen":
        $frozenDate = $theDate;
        if ($checkBoxState == "true") {
          $frozenDate = null; // we are unsetting the frozen date; we may not be able to test whether this works until another day has passed
        // we need to check if nitrogen is marked; if it is not, then we set it; otherwise, we give the original date it had been set to    
        } else {                        
          if (!$isNitrogenFreezeDateSet) {
            $isNitrogenFreezeDateSet = 1;
            $nitrogenFreezeDate = $theDate;
          }
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

    $strainObject = new Strain($geneElementArrayToEdit['strainName_col'], $isolationNameNotUsed, $frozenDate, $thawedDateNotUsed, $commentNotUsed,$parentStrainsNotUsed,$allelesNotUsed,$transGenesNotUsed,$balancersNotUsed,$contributorNotUsed[0],$frozenLocationNotUsed,$nitrogenNotUsed,$lastVialStateNotUsed,$lastVialer[0],$handOffDate, $survivalDate, $movedDate, $isNitrogenFreezeDateSet, $nitrogenFreezeDate,$highValueStrainReasonNotUsed[0]);

    $strainObject->processStrainStatus($strainID,$whichProcess); 

  } else {
    error_log("strain id missing");
  }

?>
