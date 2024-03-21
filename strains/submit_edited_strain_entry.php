<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>KurshanLab Strain Database</title>
  </head>
  <body>
    <?php
      $isStrainBeingEdited = $_POST['originalStrainEdited'];

      $theNewIsolationName = $_POST['isolation_htmlName'];

      $theNewComment = $_POST['strainSpecificComments_htmlName'];

      $theNewDateFrozen = $_POST['originalDateFrozen_postvar'];

      $theNewHandedOffDate = $_POST['originalHandOffDate_postvar'];
      $theNewSurvivalDate = $_POST['originalSurvivalDate_postvar'];
      $theNewMovedDate = $_POST['originalMovedDate_postvar'];

      $theNewDateThawed = NULL; // this is the default value MySQL accepts

      if (isset($_POST['dateThawed_htmlName']) && $_POST['dateThawed_htmlName'] != NULL) { // BUG here; needs to check for NULL not 0
        $theNewDateThawed = $_POST['dateThawed_htmlName'];
      }

      $selectedParentStrains = NULL;
      if (isset($_POST['parentStrainsArray_htmlName'])) {
          $selectedParentStrains = $_POST['parentStrainsArray_htmlName'];
      }

      // load existing set of alleles of course but also compare them to what was there before to
      // determine if we need to truly update the database
      $selectedAlleles = NULL;
      if (isset($_POST['allelesArray_htmlName'])) {
          $selectedAlleles = $_POST['allelesArray_htmlName'];
      }

      $selectedTransGenes = NULL;
      if (isset($_POST['transgeneArray_htmlName'])) {
          $selectedTransGenes = $_POST['transgeneArray_htmlName'];
      }

      $selectedBalancers = NULL;
      if (isset($_POST['balancersArray_htmlName'])) {
          $selectedBalancers = $_POST['balancersArray_htmlName'];
      }

      $theNewIsLastVialState = 0;
      if (isset($_POST['lastvialcheckbox_htmlName'])) {
          $theNewIsLastVialState = 1;
      }

      // only option; assigned differently
      if(isset($_POST['lastVialerArray_htmlName'])){
        $selectedLastVialer = $_POST['lastVialerArray_htmlName'];
        if ($selectedLastVialer[0] == "") { // BUG here, requires "", not 0
          $selectedLastVialer[0] = NULL;
        }
      } else $selectedLastVialer[0] = NULL;

      require_once('../classes/classes_gene_elements.php');

      // because there is a single entry for contributors_htmlName, it doesn't get assigned NULL like the others
      // so if the value is 0, we hand set it to null.
      if(isset($_POST['contributorArray_htmlName'])){
        $selectedContributor = $_POST['contributorArray_htmlName'];
        if ($selectedContributor[0] == "") { // BUG here, requires "", not 0
          $selectedContributor[0] = NULL;
        }
      } else $selectedContributor[0] = NULL;

      if ($theNewHandedOffDate == "") {
        $theNewHandedOffDate = null;
      }
      if ($theNewDateFrozen == "") {
        $theNewDateFrozen = null;
      }
      if ($theNewSurvivalDate == "") {
        $theNewSurvivalDate = null;
      }
      if ($theNewMovedDate == "") {
        $theNewMovedDate = null;
      }

      if ($isStrainBeingEdited) {

        $strainHasBeenChanged = false;

        $theOriginalStrainName = $_POST['originalGeneLetters_postVar'] .$_POST['originalGeneNumbers_postVar'];
        $theStrainID = $_POST['originalgeneElementID_postVar'];

        $theFrozenLocation = $_POST['fullFreezer_postvar'];
        $theNitrogenLocation = $_POST['fullNitrogen_postvar'];

        // comment
        if ($_POST['comment_postvar'] != $theNewComment) {
          $strainHasBeenChanged = true;
        }

        // alleles
        if (TestArrays('originalAlleleArray_postVar', $selectedAlleles)) {
          $strainHasBeenChanged = true;
        }
        // transgenes
        if (TestArrays('originalTransGenesArray_postVar', $selectedTransGenes)) {
          $strainHasBeenChanged = true;
        }

        // balancers
        if (TestArrays('originalBalancerArray_postVar', $selectedBalancers)) {
          $strainHasBeenChanged = true;
        }

        // parent strains
        if (TestArrays('originalParentStrainArray_postVar', $selectedParentStrains)) {
          $strainHasBeenChanged = true;
        }

        // contributor
        if ($_POST['originalContributorID_postvar'] != $selectedContributor[0]) {
          $strainHasBeenChanged = true;
        }

        // date frozen
        if ($_POST['originalDateFrozen_postvar'] != $theNewDateFrozen) {
          $strainHasBeenChanged = true;
        }

        // date thawed
        if ($_POST['originalDateThawed_postvar'] != $theNewDateThawed) {
          $strainHasBeenChanged = true;
        }

        // isolation name
        if ($_POST['originalIsolationName_postvar'] != $theNewIsolationName) {
          $strainHasBeenChanged = true;
        }

        // lastvialer
        $theCurrentLastVialState = 0;
        if (isset($_POST['lastvialcheckbox_htmlName'])) {
          $theCurrentLastVialState = 1;
        }
        if ($theCurrentLastVialState != $theNewIsLastVialState) {
          $strainHasBeenChanged = true;
        }
        if ($_POST['originalLastVialer_postvar'] != $selectedLastVialer[0]) {
          $strainHasBeenChanged = true;
        }

        $switchedState ="";
        if ($_POST['originalGeneLetters_postVar'] == 'PTK') {
          if ($_POST['manufacturedWhere_htmlName'] == "externally-sourced_value") {
            $switchedState = "toExternallySourced";
           } else {
            $switchedState = "stayingOnLabProduced";
          }
        } else {
          if ($_POST['manufacturedWhere_htmlName'] == "lab-produced_value") {
            $switchedState = "toLabProduced";
          } else {
            $switchedState = "stayingOnExternallySourced";
          }
        }

        switch ($switchedState) {

          case "toExternallySourced":
            // $strainHasBeenChanged by default;
            $theNewExternallySourcedStrainName = $_POST['manufacturedWhereLetters_htmlName'] . $_POST['manufacturedWhereNumbers_htmlName'];
            $checkThisStrain = new Strain($theNewExternallySourcedStrainName, $theNewIsolationName, $theNewDateFrozen, $theNewDateThawed, $theNewComment,$selectedParentStrains,$selectedAlleles,$selectedTransGenes,$selectedBalancers,$selectedContributor[0],$theFrozenLocation,$theNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer[0],$theNewHandedOffDate,$theNewSurvivalDate,$theNewMovedDate);
        		if(!$checkThisStrain->doesItAlreadyExist()){
              $checkThisStrain->updateOurEntry($theStrainID);
              ourheader("location: ../start/start.php");
            }
        		break;

          case "toLabProduced":
            // $strainHasBeenChanged by default;
            // strain name is auto-generated by insertOurEntryWithCounterTableUpdate
            // new method updateOurEntryWithCounterTableUpdate creates a new counter record but puts the results into the existing transgene record
            $dummyName="";
            $newStrainObject = new Strain($dummyName, $theNewIsolationName, $theNewDateFrozen, $theNewDateThawed, $theNewComment,$selectedParentStrains,$selectedAlleles,$selectedTransGenes,$selectedBalancers,$selectedContributor[0],$theFrozenLocation,$theNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer[0],$theNewHandedOffDate,$theNewSurvivalDate,$theNewMovedDate);
        		$newStrainObject->updateOurEntryWithCounterTableUpdate($theStrainID);
            ourheader("location: ../start/start.php");
        		break;

          case "stayingOnLabProduced":
            if ($strainHasBeenChanged) {
              // we pass it the original name and just update
              $newStrainObject = new Strain($theOriginalStrainName, $theNewIsolationName, $theNewDateFrozen, $theNewDateThawed, $theNewComment,$selectedParentStrains,$selectedAlleles,$selectedTransGenes,$selectedBalancers,$selectedContributor[0],$theFrozenLocation,$theNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer[0],$theNewHandedOffDate,$theNewSurvivalDate,$theNewMovedDate);
              $newStrainObject->updateOurEntry($theStrainID);
            }
            ourheader("location: ../start/start.php");
            break;

          case "stayingOnExternallySourced":
            // we may or may not have edited the name;
            // if we edited it, then check that the new name is not a duplicate
            // but if we didn't edit it, don't check because that original name will be flagged inadvertently as a duplicate
            $checkForNewNameForDuplicate = false;

            // the new name consists of three fields, the two text fields and the current chromosome state
            // the chromosome state may have changed
            $theNewExternallySourcedStrainName = $_POST['manufacturedWhereLetters_htmlName'] . $_POST['manufacturedWhereNumbers_htmlName'];
            $checkThisStrain = new Strain($theNewExternallySourcedStrainName, $theNewIsolationName, $theNewDateFrozen, $theNewDateThawed, $theNewComment,$selectedParentStrains,$selectedAlleles,$selectedTransGenes,$selectedBalancers,$selectedContributor[0],$theFrozenLocation,$theNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer[0],$theNewHandedOffDate,$theNewSurvivalDate,$theNewMovedDate);

            // if names have changed, then $strainHasBeenChanged is true by default
            if ($theOriginalStrainName != $theNewExternallySourcedStrainName) {
              $checkForNewNameForDuplicate = true;
            }
            if ($checkForNewNameForDuplicate) {
              if(!$checkThisStrain->doesItAlreadyExist()){
                $checkThisStrain->updateOurEntry($theStrainID);
                ourheader("location: ../start/start.php");
              }
            } else {
              if ($strainHasBeenChanged) {
                $checkThisStrain->updateOurEntry($theStrainID);
              }
              ourheader("location: ../start/start.php");
            }
            break;
        }
      } else {
          // we are entering a new strain for the first time
          $dummyThawed=NULL;
          $unsavedFreezerLocation="";
          $unsavedNitrogenLocation="";
          switch ($_POST['manufacturedWhere_htmlName']) {
            case 'externally-sourced_value':
              $theNewExternallySourcedStrainName = $_POST['manufacturedWhereLetters_htmlName'] . $_POST['manufacturedWhereNumbers_htmlName'];
              $newStrainObject = new Strain($theNewExternallySourcedStrainName, $theNewIsolationName, $theNewDateFrozen, $dummyThawed, $theNewComment,$selectedParentStrains,$selectedAlleles,$selectedTransGenes,$selectedBalancers,$selectedContributor[0],$unsavedFreezerLocation,$unsavedNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer[0],$theNewHandedOffDate,$theNewSurvivalDate,$theNewMovedDate);
              // first, start transaction
              // second, create strain: this is insertOurEntry.
              // third, call pdo->lastinsertid to get the id
              // fourth, now loop:
              // fifth, in the loop for each allele name, search to get its id
              // sixth, create a record into the strain-allele table inserting both the saved id and allele id
              // pass the method two arrays, one for alleles and one for transgenes

              if (!($newStrainObject->doesItAlreadyExist())) {
                $newStrainObject->insertOurEntry();
                ourheader("location: ../start/start.php");
   						}
              break;
            case 'lab-produced_value':
              // strain name is internally generated for lab-created strains
              $dummyName="";
              $newStrainObject = new Strain($dummyName, $theNewIsolationName, $theNewDateFrozen, $dummyThawed, $theNewComment,$selectedParentStrains,$selectedAlleles,$selectedTransGenes,$selectedBalancers,$selectedContributor[0],$unsavedFreezerLocation,$unsavedNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer[0],$theNewHandedOffDate,$theNewSurvivalDate,$theNewMovedDate);
             // this method will do almost everything: it creates the name, updates the counter table, then does the insert for strain
   					  $newStrainObject->insertOurEntryWithCounterTableUpdate();
              ourheader("location: ../start/start.php");
              break;
          }
      }
     ?>
  </body>
</html>
