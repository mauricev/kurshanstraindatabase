<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php
      if($_POST['newElement_fieldName']) {

        $theElementString = $_POST['newElement_fieldName'];

        require_once('../classes/classes_single_element.php');

        $singleElementBeingEdited = $_POST['original_isElementBeingEdited_postvar'];

        error_log("in submit, ".  $singleElementBeingEdited);

        if ($singleElementBeingEdited) {
          $theElementID = $_POST['original_elementID_postvar'];
        }
      	switch($_POST['original_element_postvar']) {
      		case 'newContributor_htmlName';
      			$theElementObject = new NewContributor($theElementString);
            $contributorState = 0;
            if (isset($_POST['outsideLab_fieldID'])) {
              $contributorState = 1;
            }
            error_log("in submit, value of contributorState ".$contributorState);
            $theElementObject->setOutsideContributorState($contributorState);
      			break;

      		case 'newCoInjection_htmlName';
            $theElementObject = new NewCoInjectionMarker($theElementString);
      			break;

      		case 'newAntibioticResistance_htmlName';
      			$theElementObject = new NewAntibiotic($theElementString);
      			break;

      		case 'newFluoro_htmlName';
      			$theElementObject = new NewFluoro($theElementString);
      			break;
      	}
      	if($theElementObject) {
          if ($singleElementBeingEdited) {
            // this code doesn't take into account unsetting the outside lab setting for a contributor
            // to do this requires saving the orignal value and if it’s changed, updateOurEntry
            if (!($theElementObject->doesItAlreadyExist())) {
              $theElementObject->updateOurEntry($theElementID);
            }
          } else {
            if (!($theElementObject->doesItAlreadyExist())) {
      	      $theElementObject->insertOurEntry();
      	    }
          }
          header("location: ../start/start.php");
      	}
      }
    ?>
  </body>
</html>
