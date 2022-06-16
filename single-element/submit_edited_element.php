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

        if ($singleElementBeingEdited) {
          $theElementID = $_POST['original_elementID_postvar'];
        }
      	switch($_POST['original_element_postvar']) {
      		case 'newContributor_htmlName';
      			$theElementObject = new NewContributor($theElementString);
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
