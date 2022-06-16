<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php
        $isEntityBeingEdited = $_POST['originalPlasmidEdited'];

        $thePlasmidName = "";
        if(isset($_POST['plasmidName_htmlName'])) {
       		$thePlasmidName = $_POST['plasmidName_htmlName'];
       	}

        $theComment = "";
       	if(isset($_POST['comment_htmlName'])) {
       		$theComment = $_POST['comment_htmlName'];
       	}

        $theOthercDNA = "";
       	if(isset($_POST['other_cDNA_htmlName'])) {
       		$theOthercDNA = $_POST['other_cDNA_htmlName'];
       	}

        $thePlasmidLocation = "";
       	if(isset($_POST['plasmidLocation_htmlName'])) {
       		$thePlasmidLocation = $_POST['plasmidLocation_htmlName'];
       	}

        require_once('../classes/classes_gene_elements.php');

        $selectedAntibiotics = NULL;
        if(isset($_POST['antibioticArray_htmlName'])){
   				$selectedAntibiotics = $_POST['antibioticArray_htmlName'];

 			  }

        $selectedNTags = NULL;
   			if(isset($_POST['fluoroNtagArray_htmlName'])){
   				$selectedNTags = $_POST['fluoroNtagArray_htmlName'];

   			}

        $selectedCTags = NULL;
   			if(isset($_POST['fluoroCtagArray_htmlName'])){
   				$selectedCTags = $_POST['fluoroCtagArray_htmlName'];

   			}

        $selectedITags = NULL;
   			if(isset($_POST['fluoroInternaltagArray_htmlName'])) {
   				$selectedITags = $_POST['fluoroInternaltagArray_htmlName'];

   			}

        // because there is a single entry for contributors_htmlName, it doesn't get assigned NULL like the others
        // so if the value is 0, we hand set it to null.
        if(isset($_POST['contributorArray_htmlName'])){
          $selectedContributor = $_POST['contributorArray_htmlName'];
          if ($selectedContributor[0] == "") { // BUG here, requires "", not 0
            $selectedContributor[0] = NULL;
          }
   			} else $selectedContributor[0] = NULL;

        if(isset($_POST['promoterArray_htmlName'])) {
 				  $selectedPromoter = $_POST['promoterArray_htmlName'];
          if ($selectedPromoter[0] == "") {
            $selectedPromoter[0] = NULL;
          }
   			} else $selectedPromoter[0] = NULL;

        if(isset($_POST['genesArray_htmlName'])) {
 				  $selectedGene = $_POST['genesArray_htmlName'];
          if ($selectedGene[0] == "") {
            $selectedGene[0] = NULL;
          }
   			} else $selectedGene[0] = NULL;

        // common sequence code for plasmids and alleles
        require_once("../sequence/submit_sequence.php");

        echo "contributor being passed to new plasmid is " . $selectedContributor[0] . "<br>";
        $newPlasmidObject = new Plasmid($thePlasmidName, $theOthercDNA, $thePlasmidLocation, $theComment,$selectedAntibiotics,$selectedNTags,$selectedCTags,$selectedITags,$selectedPromoter[0], $selectedGene[0],$selectedContributor[0],$theSequenceFileName,$theSequenceFileData);
        if ($isEntityBeingEdited) {

          $plasmidHasBeenChanged = false;

          $thePlasmidID = $_POST['originalPlasmidID_postVar'];
          $theOriginalPlasmidName = $_POST['originalPlasmidName_postVar'];
          $theOriginalcDNA = $_POST['originalOthercDNA_postVar'];
          $theOriginalPlasmidLocation = $_POST['originalPlasmidLocation_postVar'];
          $theOriginalComment = $_POST['originalComment_postvar'];
          $theOriginalContributor = $_POST['originalContributor_postvar'];
          $theOriginalPromoter = $_POST['originalPromotor_postvar'];
          $theOriginalGene = $_POST['originalGene_postvar'];

          if (TestArrays('originalNFluoroTags_postVar', $selectedNTags)) {
            $plasmidHasBeenChanged = true;
          }
          if (TestArrays('originalCFluoroTags_postVar', $selectedCTags)) {
            $plasmidHasBeenChanged = true;
          }
          if (TestArrays('originalIFluoroTags_postVar', $selectedITags)) {
            $plasmidHasBeenChanged = true;
          }

          // 1) new entry, has file name and data or nothing
          // we save sequence data of nothing ($theSequenceFileData = "")
          // or we save sequence data of somthing $theSequenceFileData = $_POST['sequenceFileData_htmlName'];
          // now we edit possibilities
          // 1) we leave everything alone
          // 2) we upload the same file with a different name
          // 3) we upload a different file with the original name
          // 4) we upload a different file with a different name

          // first we check to see if we left everything alone

          // first check
          // $theOriginalSequenceData != $theSequenceFileData
          // we record the new data and the new name, no need for a check ; here we assume data had to have changed and filename was updated accordingly
          // if $theOriginalSequenceData == $theSequenceFileData, did the name change
          // if filename is not null and doesn't equal old name, update name to new name
          // otherwise, pass back old name


          if ($theOriginalSequenceData != $theSequenceFileData) {
            $plasmidHasBeenChanged = true;
          }

          //contributor
          if ($theOriginalContributor != $selectedContributor[0]) {
            $plasmidHasBeenChanged = true;
          }

          //comment
          if ($theOriginalComment != $theComment) {
            $plasmidHasBeenChanged = true;
          }

          //othercdna
          if ($theOriginalcDNA != $theOthercDNA) {
            $plasmidHasBeenChanged = true;
          }

          //plasmid location
          if ($theOriginalPlasmidLocation != $thePlasmidLocation) {
            $plasmidHasBeenChanged = true;
          }

          // promoter
          //bug fixed here
          if ($theOriginalPromoter != $selectedPromoter[0]) {
            $plasmidHasBeenChanged = true;
          }

          // gene
          //bug fixed here
          if ($theOriginalGene != $selectedGene[0]) {
            $plasmidHasBeenChanged = true;
          }

          $checkForNewNameForDuplicate = false;

          if ($theOriginalPlasmidName != $thePlasmidName) {
            $checkForNewNameForDuplicate = true;
          }
          if ($checkForNewNameForDuplicate) {
            // if we are here, the names don't match, so update/edit
            if(!$newPlasmidObject->doesItAlreadyExist()){
              $newPlasmidObject->updateOurEntry($thePlasmidID);
              ourheader("location: ../start/start.php");
            }
          } else {
            if ($plasmidHasBeenChanged) {
              $newPlasmidObject->updateOurEntry($thePlasmidID);
            }
            ourheader("location: ../start/start.php");
          }
        } else {
          if (!($newPlasmidObject->doesItAlreadyExist())) {
            $newPlasmidObject->insertOurEntry();
            ourheader("location: ../start/start.php");
			    }
        }

     ?>
  </body>
</html>
