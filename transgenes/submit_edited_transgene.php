<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>KurshanLab Strain Database</title>
  </head>
  <body>
    <?php
    
      require_once('../classes/classes_gene_elements.php');

      $theNewComment = "";
    		if(isset($_POST['comments_fieldName'])) {
    			$theNewComment = $_POST['comments_fieldName'];
    		}

      $theNewChromosome = "";
      if(isset($_POST['chromosome_htmlName'])){
				$theNewChromosome = $_POST['chromosome_htmlName'];
			}
      // this array contains the selected gene in the first element

      // because there is a single entry for contributors_htmlName, it doesn't get assigned NULL like the others
      // so if the value is 0, we hand set it to null.
      if(isset($_POST['contributorArray_htmlName'])){
        $selectedContributor = $_POST['contributorArray_htmlName'];
        if ($selectedContributor[0] == "") { // BUG here, requires "", not 0
          $selectedContributor[0] = NULL;
        }
      } else $selectedContributor[0] = NULL;

      if(isset($_POST['coinjectionMarkerArray_htmlName'])){
        $selectedCoInjectionMarker = $_POST['coinjectionMarkerArray_htmlName'];
        if ($selectedCoInjectionMarker[0] == "") { // BUG here?
          $selectedCoInjectionMarker[0] = NULL;

        }
      } else $selectedCoInjectionMarker[0] = NULL;

      // need to be prepared to accept null here
      if(isset($_POST['transgeneArray_htmlName'])){
        $theParentTransGene = $_POST['transgeneArray_htmlName'];
        if ($theParentTransGene[0] == "") { // BUG here?
          $theParentTransGene[0] = NULL;
        }
      } else {
          $theParentTransGene[0] = NULL;
      }

      $selectedPlasmids = NULL;
      if(isset($_POST['plasmidArray_htmlName'])){
         $selectedPlasmids = $_POST['plasmidArray_htmlName'];
       }

      $theIntegratedExtraChromosomalState = "Ex";

      if ($_POST['locationInCell_htmlName'] == "integrated") {
      	$theIntegratedExtraChromosomalState = "Is";
      }
      if ($_POST['locationInCell_htmlName'] == "single insertion") {
      	$theIntegratedExtraChromosomalState = "Si";
      }

      $theNewExternallySourcedTransGeneName = "";


      // this name reflects the lab-produced name with its current location, which may not be the originally passed name
      // due to a change in chromosome status
      $isTransGeneBeingEdited = $_POST['originalTransGeneEdited'];
      if ($isTransGeneBeingEdited) {

        $theOriginalLocation = $_POST['original_transgeneLocation_postVar'];

        $theOriginalTransGeneName = $_POST['originalGeneLetters_postVar'] . $theOriginalLocation . $_POST['originalGeneNumbers_postVar'];

        $theTransGeneID = $_POST['originalgeneElementID_postVar'];

        $theOldSelectedGene = $_POST['originalgeneElementID_postVar'];

        // says new because integrated state may be different?
        $theNewLabProducedTransGeneName = $_POST['originalGeneLetters_postVar'] . $theIntegratedExtraChromosomalState . $_POST['originalGeneNumbers_postVar'];


        $transGeneHasBeenChanged = false;
        //letters and numbers of externally-sourced in case they changed
        if (isset($_POST['transgene_letters_name'])) {
          if ($_POST['originalGeneLetters_postVar'] != $_POST['transgene_letters_name']) {
            $transGeneHasBeenChanged = true;
          }
        }

        if (isset($_POST['transgene_numbers_name'])) {
          if ($_POST['originalGeneNumbers_postVar'] != $_POST['transgene_numbers_name']) {
            $transGeneHasBeenChanged = true;
          }
        }

        //chromosome
        if ($_POST['orginalChromosome_postVar'] != $theNewChromosome) {
          $transGeneHasBeenChanged = true;
        }

        // contributor
        if ($_POST['originalContributorID_postvar'] != $selectedContributor[0]) {
          echo "contributor marked as changed<br>";
          $transGeneHasBeenChanged = true;
        }
        // comments
        if ($_POST['originalComment_postVar'] != $theNewComment) {
          $transGeneHasBeenChanged = true;
        }

        // parent transgene
        if ($_POST['originalParentExTransGene_postvar'] != $theParentTransGene[0]) {
          $transGeneHasBeenChanged = true;
        }

        //coinjection marker
        if ($_POST['originalcoInjectionMarker_postvar'] != $selectedCoInjectionMarker[0]) {
          $transGeneHasBeenChanged = true;
        }

        // plasmids array
        // have a quesion, what if there was a plasmid and it's removed?
        // should be fine because it would exist as an empty array

        if (TestArrays('originalPlasmidsArray_postVar', $selectedPlasmids)) {
          $transGeneHasBeenChanged = true;
        }

        $switchedState ="";
        // original transgene letters and numbers are populated regardless of the transgene's state, lab-produced or externally-sourced
        if ($_POST['originalGeneLetters_postVar'] == 'kur') {
          if ($_POST['manufacturedWhere_htmlName'] == "externally-sourced") {
            $switchedState = "toExternallySourced";
           } else {
            $switchedState = "stayingOnLabProduced";
          }
        } else {
          if ($_POST['manufacturedWhere_htmlName'] == "lab-produced") {
            $switchedState = "toLabProduced";
          } else {
            $switchedState = "stayingOnExternallySourced";
          }
        }

        switch ($switchedState) {
        	case "toExternallySourced":
            // changing from lab-produced to externally-sourced; state of $transGeneHasBeenChanged is irrelevant
            // externally sourced doesn't refer to the ex/is status; that's the integrated versus extra-chromosomal state
            // we know the name is external because theinternal name is internally generated
            $theNewExternallySourcedTransGeneName = $_POST['transgene_letters_name'] . $theIntegratedExtraChromosomalState . $_POST['transgene_numbers_name'];

            // externally sourced gets the new name as programmed above
            // but first we ensure that the new name doesn't conflict with an existing record
            //$name_param, $theNewChromosome_param, $comments_param,$location_param,$parentTransGene_param

        		$checkThisTransGene = new TransGene($theNewExternallySourcedTransGeneName, $theNewChromosome, $theNewComment,$theIntegratedExtraChromosomalState, $theParentTransGene[0],$selectedCoInjectionMarker[0],$selectedPlasmids,$selectedContributor[0]);
            if(!$checkThisTransGene->doesItAlreadyExist()){

              $checkThisTransGene->updateOurEntry($theTransGeneID);
              header("location: ../start/start.php");
            } else echo "<br>the name ALREADY exists";
        		break;

          case "toLabProduced":
            // changing from externally-sourced to lab-produced; state of $transGeneHasBeenChanged is irrelevant
            // transgene name is auto-generated by insertOurEntryWithCounterTableUpdate
            // new method updateOurEntryWithCounterTableUpdate creates a new counter record but puts the results into the existing transgene record
            $dummyName="";
        		$newTransGeneObject = new TransGene($dummyName, $theNewChromosome, $theNewComment,$theIntegratedExtraChromosomalState, $theParentTransGene[0],$selectedCoInjectionMarker[0],$selectedPlasmids,$selectedContributor[0]);
            $newTransGeneObject->updateOurEntryWithCounterTableUpdate($theTransGeneID);
            header("location: ../start/start.php");
        		break;

          case "stayingOnLabProduced":

            // we pass it the original name and just update
            // problem $theOriginalTransGeneName may not be the correct name if the chromosome has changed
            // we need to split the name into components and then add in the chromosome state, ex or is.
            $newTransGeneObject = new TransGene($theNewLabProducedTransGeneName, $theNewChromosome, $theNewComment,$theIntegratedExtraChromosomalState, $theParentTransGene[0],$selectedCoInjectionMarker[0],$selectedPlasmids,$selectedContributor[0]);
            // if we come here, we need to check to see if the chromsome status has changed the name
            // if it hasn't then, just update; otherwise, we need to ensure the new name doesn't conflict with another transgene

            // problem if the chromosome state has changed, the counter must be updated!
            // count should change only if we switch chromosome states
            // if we are on the same
            // if these values below differ, state of $transGeneHasBeenChanged is irrelevant
            if ($theOriginalLocation != $theIntegratedExtraChromosomalState) {
              $newTransGeneObject->updateOurEntryWithCounterTableUpdate($theTransGeneID);
              // this means we changed chromosome status, ex versus is
            	// here, we want the class to create an entirely new name from scratch using
            	// updateOurEntryWithCounterTableUpdate, but using the existing id
            	// this function will get the next entry in for the correct transgene table and update the table
            	// and update the record in the database for transgenes
            	// here we don't pass it anything to the name argument
            } else {
              if ($transGeneHasBeenChanged) {
                $newTransGeneObject->updateOurEntry($theTransGeneID);
              }
            }
            header("location: ../start/start.php");
            break;

          case "stayingOnExternallySourced":
            if ($transGeneHasBeenChanged) {
              // we may or may not have edited the name;
              // if we edited it, then check that the new name is not a duplicate
              // but if we didn't edit it, don't check because that original name will be flagged inadvertently as a duplicate
              $checkForNewNameForDuplicate = false;

              // the new name consists of three fields, the two text fields and the current chromosome state
              // the chromosome state may have changed

              $theNewExternallySourcedTransGeneName = $_POST['transgene_letters_name'] . $theIntegratedExtraChromosomalState . $_POST['transgene_numbers_name'];
              $checkThisTransGene = new TransGene($theNewExternallySourcedTransGeneName, $theNewChromosome, $theNewComment,$theIntegratedExtraChromosomalState, $theParentTransGene[0],$selectedCoInjectionMarker[0],$selectedPlasmids,$selectedContributor[0]);
              $checkForNewNameForDuplicate = false;
              if ($theOriginalTransGeneName != $theNewExternallySourcedTransGeneName) {
                $checkForNewNameForDuplicate = true;
              }
              if ($checkForNewNameForDuplicate) {
                if(!$checkThisTransGene->doesItAlreadyExist()){
                  $checkThisTransGene->updateOurEntry($theTransGeneID);
                  header("location: ../start/start.php");
                }
              } else {
                $checkThisTransGene->updateOurEntry($theTransGeneID);
                header("location: ../start/start.php");
              }
            }

            break;
        }
      } else {
        if ($_POST["manufacturedWhere_htmlName"] == "externally-sourced") {

          $theNewExternallySourcedTransGeneName = $_POST['transgene_letters_name'] . $theIntegratedExtraChromosomalState . $_POST['transgene_numbers_name'];

          $newTransGeneObject = new TransGene($theNewExternallySourcedTransGeneName,$theNewChromosome, $theNewComment,$theIntegratedExtraChromosomalState,$theParentTransGene[0],$selectedCoInjectionMarker[0],$selectedPlasmids,$selectedContributor[0]);
          if (!($newTransGeneObject->doesItAlreadyExist())) {
							$newTransGeneObject->insertOurEntry();
              header("location: ../start/start.php");
					}
  			}
  			else {

  				// computes the name for the right transgene column counter, that is Is or Ex.
  				$transgeneColumnCounterName = 'transgene'. $theIntegratedExtraChromosomalState . "Counter";
          //need to confirm this is correct; I am passing nothing to the name for a new netry that lab-produced
          $dummyName="";
  				$newTransGeneObject = new TransGene($dummyName,$theNewChromosome, $theNewComment,$theIntegratedExtraChromosomalState,$theParentTransGene[0],$selectedCoInjectionMarker[0],$selectedPlasmids,$selectedContributor[0]);
  				$newTransGeneObject->insertOurEntryWithCounterTableUpdate();
          header("location: ../start/start.php");
  			}
      }
     ?>
  </body>
</html>
