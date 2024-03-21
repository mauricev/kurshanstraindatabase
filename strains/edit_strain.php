<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <title>KurshanLab Strain Database</title>
   	<meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/selectize.js"></script>
    <script src="../js/common-functions.js"></script>
    <script src="/js/strain-javascript.js"></script>

    <script>
      $( document ).ready(function()
      {
        $('input[name=manufacturedWhereLetters_htmlName]').prop('disabled', true);
        $('input[name=manufacturedWhereNumbers_htmlName]').prop('disabled', true);

        $('#select-transgene').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-allele').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-parentStrains').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-contributors').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-lastvialers').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-balancers').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        // this is a radio button and we’re checking the checked one against its value
        if($('input[name=manufacturedWhere_htmlName]:checked').val() == 'externally-sourced_value') {
          $('input[name=manufacturedWhereLetters_htmlName]').prop('disabled', false);
          $('input[name=manufacturedWhereNumbers_htmlName]').prop('disabled', false);
        }
        else {
          $('input[name=manufacturedWhereLetters_htmlName]').prop('disabled', true);
          $('input[name=manufacturedWhereNumbers_htmlName]').prop('disabled', true);
        }
        lastTubeCheckBoxAddListener();
        edit_strain_source_buttons();
        edit_strain_lastvial_button();
        edit_strain_lastvial_thawed_required_button();
        cancelButton();

      });
    </script>
  </head>
  <body class="bg-light">
  	<div class="container">
  		<div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
        <?php
          $isStrainBeingEdited = false;
          if (isset($_POST['parentStrainsArray_htmlName'])) {
            $isStrainBeingEdited = true;
            $selectedElement = $_POST['parentStrainsArray_htmlName'];

            $geneElementObjectToEdit = new LoadParentStrains();
            $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($selectedElement[0]);

            $theStrainName = htmlspecialchars($geneElementArrayToEdit['strainName_col'],ENT_QUOTES);

            echo "<p class='lead'>Edit Strain, $theStrainName</p>";
          } else {
            echo "<p class='lead'>Add New Strain</p>";
          }
        ?>
      </div>
      <?php
          if ($isStrainBeingEdited) {
            // $selectedElement[0] this is the strain_id
            // strain name is composed of letters and numbers; there is no hyphen!
            // solution is to create an array, one for the letters and one for the numbers
            // $selectedElement[0] and $geneElementArrayToEdit['strainName_col'] should be identical
            // htmlspecialchars was applied to the $theStrainName
            $strainNameArray = preg_split('/([a-zA-Z]+)/', $theStrainName, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

            // $selectedElement[0] is identical to $geneElementArrayToEdit['strain_id']
            $theOriginalStrainID = $geneElementArrayToEdit['strain_id'];
            // htmlspecialchars was applied to the $theStrainName
            $theOriginalStrainName = $theStrainName;
            $theOriginalIsolationName = htmlspecialchars($geneElementArrayToEdit['isolationName_col'],ENT_QUOTES);

            if ($geneElementArrayToEdit['dateFrozen_col'] != null) {
              $theOriginalDateFrozen = htmlspecialchars($geneElementArrayToEdit['dateFrozen_col'],ENT_QUOTES);
            } else {
              $theOriginalDateFrozen = "";
            }

            // BUG if thawed date is not set, we can’t run htmlspecialcharacters on it
            if (isset($geneElementArrayToEdit['dateThawed_col'])) {
              $theOriginalDateThawed = htmlspecialchars($geneElementArrayToEdit['dateThawed_col'],ENT_QUOTES);
            } else {
              $theOriginalDateThawed = NULL;
            }

            $theOriginalComment = htmlspecialchars($geneElementArrayToEdit['comments_col'],ENT_QUOTES);

            $theOriginalFullFreezer = $geneElementArrayToEdit['fullFreezer_col'];
            $theOriginalNitrogen = $geneElementArrayToEdit['fullNitrogen_col'];

            $theOriginalContributor = $geneElementArrayToEdit['contributor_fk'];

            $theOriginalIsLastVial= $geneElementArrayToEdit['isLastVial_col'];

            $theOriginalLastVialer = $geneElementArrayToEdit['lastVialContributor_fk'];

            if ($geneElementArrayToEdit['dateHandedOff_col'] != null) {
              $theOriginalHandOffDate = htmlspecialchars($geneElementArrayToEdit['dateHandedOff_col'],ENT_QUOTES);
            } else {
              $theOriginalHandOffDate = "";
            }

            if ($geneElementArrayToEdit['dateSurvived_col'] != null) {
              $theOriginalSurvivalDate = htmlspecialchars($geneElementArrayToEdit['dateSurvived_col'],ENT_QUOTES);
            } else {
              $theOriginalSurvivalDate = "";
            }

            if ($geneElementArrayToEdit['dateMoved_col'] != null) {
              $theOriginalMovedDate = htmlspecialchars($geneElementArrayToEdit['dateMoved_col'],ENT_QUOTES);
            } else {
              $theOriginalMovedDate = "";
            }

            $labProduced = false;
            if ($strainNameArray[0] == 'PTK') {
              $labProduced = true;
            }
          } else {
            $theOriginalDateFrozen = "";
            $theOriginalHandOffDate = "";
            $theOriginalSurvivalDate = "";
            $theOriginalMovedDate = "";
          }

          $theHandOffState = ($theOriginalHandOffDate != "");
          $theFrozenState = ($theOriginalDateFrozen != "");
          $theSurvivalState = ($theOriginalSurvivalDate != "");
          $theMovedState = ($theOriginalMovedDate != "");


          // we’ve decided to make these buttons display only
          $theHandOffButtonState = "disabled";
          $theFrozenButtonState = "disabled";
          $theSurvivalButtonEnabledState = "disabled";
          $theMoveButtonEnabledState = "disabled";
      ?>
      <form class="needs-validation" novalidate action="../strains/submit_edited_strain_entry.php"  method="post">

        <div class="row">

          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_gene_elements.php");
              $theAlleleListing = new LoadAllele();
              if ($isStrainBeingEdited) {
                $theMarkedAlleles = new LoadAllelesToStrain($theOriginalStrainID);
                $theMarkedAllelesArray = $theMarkedAlleles->ReturnMarkedGeneElements();
                $theMarkedAlleles->PopulateHiddenArray();
                $theAlleleListing->buildSelectedTablesWithMultipleEntries($theMarkedAllelesArray);
              } else {
                $multiple = true;
                $theAlleleListing->buildSelectTable($multiple);
              }
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <?php
              $theTransGeneListing = new LoadTransGene();
              if ($isStrainBeingEdited) {
                $theMarkedTransGenes = new LoadTransGenesToStrain($theOriginalStrainID);
                $theMarkedTransGenesArray = $theMarkedTransGenes->ReturnMarkedGeneElements();
                $theMarkedTransGenes->PopulateHiddenArray();
                $theTransGeneListing->buildSelectedTablesWithMultipleEntries($theMarkedTransGenesArray);
              } else {
                $multiple = true;
                $theTransGeneListing->buildSelectTable($multiple);
              }
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_gene_elements.php");
              $theBalancerListing = new LoadBalancer();
              if ($isStrainBeingEdited) {
                $theMarkedBalancers = new LoadBalancersToStrain($theOriginalStrainID);
                $theMarkedBalancersArray = $theMarkedBalancers->ReturnMarkedGeneElements();
                $theMarkedBalancers->PopulateHiddenArray();
                $theBalancerListing->buildSelectedTablesWithMultipleEntries($theMarkedBalancersArray);
              } else {
                $multiple = true;
                $theBalancerListing->buildSelectTable($multiple);
              }
            ?>
          </div>
        </div>

        <div class="row">
  				<div class="col-md-3 mb-3">
            <?php
              if ($isStrainBeingEdited) {
                echo "<input type='text' id='isolationName_InputID' class='form-control' value=\"$theOriginalIsolationName\" name='isolation_htmlName' placeholder='isolationName'>";
              } else {
                echo "<input type='text' id='isolationName_InputID' class='form-control' name='isolation_htmlName' placeholder='isolationName'>";
              }
            ?>
  				</div>

          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theParentStrainListing = new LoadParentStrains();
              if ($isStrainBeingEdited) {
                $theMarkedParentStrains = new LoadParentStrainsToStrain($theOriginalStrainID);
                $theMarkedParentStrainsArray = $theMarkedParentStrains->ReturnMarkedGeneElements();
                $theParentStrainListing->buildSelectedTablesWithMultipleEntries($theMarkedParentStrainsArray);
                $theMarkedParentStrains->PopulateHiddenArray();
              } else {
                $multiple = true;
                $theParentStrainListing->buildSelectTable($multiple);
              }
            ?>
          </div>
        </div>

        <div class="row">
				  <div class="col-md-3 mb-3">
  					<div class="form-check">
              <?php
                // if we are editing a lab-produced strain, the name displays at the top
                $labProducedRadioBtnText = "<input type = 'radio' id='lab-produced' name ='manufacturedWhere_htmlName' value= 'lab-produced_value' class='form-check-input' required>";
                if ($isStrainBeingEdited) {
                  if ($labProduced) {
                    echo "<input type = 'radio' id='lab-produced' name ='manufacturedWhere_htmlName' value= 'lab-produced_value' checked class='form-check-input' required>";
                  } else {
                    echo $labProducedRadioBtnText;
                  }
                } else {
                  echo $labProducedRadioBtnText;
                }
                require_once("../classes/classes_gene_elements.php");
                // we just need an empty strain object here
                $theTempStrain = createStrainPlaceHolder();
                $theTempStrain->getNextName($theGeneCurrentCount,$theNextStrainName);

                echo "<input type='hidden' id='strainBeingEditedHiddenField' name='hidden-label' value=$isStrainBeingEdited>";
                echo "<input type='hidden' id='PTKNumberHiddenField' name='hidden-label' value=$theNextStrainName>";
                if ($isStrainBeingEdited) {
                  echo "<input type='hidden' id='PTKLabProducedStateHiddenField' name='hidden-label' value=$labProduced>";
                }
  						?>
              <label id='lab-produced-label' class="form-check-label" for="lab-produced"></label>
  					</div>
  				</div>
          <div class="col-md-1">
            <label id='letters-label' class="tinylabel" style="padding-left:10px" for="manufacturedWhereLetters_htmlName">strain letters</label>
          </div>
          <div class="col-md-2">
            <label id='numbers-label' class="tinylabel" style="padding-left:10px" for="manufacturedWhereNumbers_htmlName">strain numbers</label>
          </div>
			  </div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
            <?php
              $externallySourceRadioBtnText = "<input type = 'radio' id='externally-sourced'  name ='manufacturedWhere_htmlName' value= 'externally-sourced_value' class='custom-control-input' required>";
              if ($isStrainBeingEdited) {
                if ($labProduced) {
                  echo $externallySourceRadioBtnText;
                } else {
                  echo "<input type = 'radio' id='externally-sourced'  name ='manufacturedWhere_htmlName' checked value= 'externally-sourced_value' class='custom-control-input' required>";
                }
              } else {
                echo $externallySourceRadioBtnText;
              }
						?>
						<label class="custom-control-label" for="externally-sourced">externally-sourced</label>
					</div>
				</div>
				<div class="col-lg-1 mb-3">
					<?php
            if ($isStrainBeingEdited) {
              if (!$labProduced) {
                echo "<input type='text' name='manufacturedWhereLetters_htmlName' required pattern='[A-Z]{1,3}' class='form-control' value=$strainNameArray[0] title='external_strain_name_letters'/>";
              } else {
                echo "<input type='text' name='manufacturedWhereLetters_htmlName' required pattern='[A-Z]{1,3}' class='form-control' disabled title='external_strain_name_letters'/>";
              }
            } else {
              echo "<input type='text' name='manufacturedWhereLetters_htmlName' required pattern='[A-Z]{1,3}' class='form-control' title='external_strain_name_letters'/>";
            }
          ?>
          <div class="invalid-feedback">
            Enter the uppercase letters of the strain name.
          </div>
				</div>
        <div class="col-lg-2 mb-3">
          <?php
            $strainNumbersText = "<input type='text' name='manufacturedWhereNumbers_htmlName' required class='form-control' pattern='[0-9]{1,6}' title='external_strain_name_numbers'/>";
            if ($isStrainBeingEdited) {
              if (!$labProduced) {
                echo "<input type='text' name='manufacturedWhereNumbers_htmlName' required class='form-control' pattern='[0-9]{1,6}' value=$strainNameArray[1] title='external_strain_name_numbers'/>";
              } else {
                echo $strainNumbersText;
              }
            } else {
              echo $strainNumbersText;
            }
          ?>
          <div class="invalid-feedback">
            Enter the numbers of the strain name.
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3">
          <label for="SourceStrainSelector_ID" style="padding-top:4px">source</label>
          <?php
            require_once("../classes/classes_load_elements.php");
            $theContributorListing = new LoadContributor();
            if ($isStrainBeingEdited) {
              $theContributorListing->buildSelectedTablesWithSingleEntry($theOriginalContributor);
            } else {
              $theContributorListing->buildSelectTable(false);
            }
          ?>
          <div style="padding-bottom:16px">
          </div>
        </div>

        <!-- <div class="col-md-3 mb-3" style="padding-top:12px"> -->
          <?php
            if ($isStrainBeingEdited) {
              echo "<div class='col-md-3 mb-3'style='padding-top:12px'>";
                echo "<div class='form-check'>";
                  if ($theOriginalIsLastVial == 1) {
                    echo "<input class='form-check-input' type='checkbox' value='' name='lastvialcheckbox_htmlName' checked id='lastTubeCheckBoxID'>";
                  } else {
                    echo "<input class='form-check-input' type='checkbox' value='' name='lastvialcheckbox_htmlName' id='lastTubeCheckBoxID'>";
                  }
                  // stores the value of the checkbox so javascript can access it
                  echo "<input type='hidden' id='IsLastVialHiddenField' name='hidden-label' value=$theOriginalIsLastVial>";
                  echo "<label class='form-check-label' for='lastTubeCheckBoxID'>last tube?</label>";
                echo "</div>";

                echo "<div>";
                require_once("../classes/classes_load_elements.php");
                $theLastVialerListing = new LoadLastVialers();
                $theLastVialerListing->buildSelectedTablesWithSingleEntry($theOriginalLastVialer);
                echo "</div>";
              echo "</div>";
            }
          ?>

      </div>

      <div class="row">
        <div class="form-group col-md-9 mb-3">
            <?php
              if ($isStrainBeingEdited) {
                echo "<textarea id='strainSpecificComments_ID' class='form-control rounded-0' rows='2' name='strainSpecificComments_htmlName' title='strainSpecificComments' placeholder='comments' style='width:100%'>$theOriginalComment</textarea>";
              } else {
                echo "<textarea id='strainSpecificComments_ID' class='form-control rounded-0' rows='2' name='strainSpecificComments_htmlName' title='strainSpecificComments' placeholder='comments' style='width:100%'></textarea>";
              }
            ?>
        </div>
      </div>

      <div class="row">
         <?php 
          echo "<span class='mb-1'>set strain states on the main page</span>";
          ?>
      </div>

      <div class="row">

        <!-- <div class="col-md-3 mb-3"> -->
        <!-- <div class="col-md-12"> -->
          <div class="container">
          <!-- <div class="checkbox-group d-flex justify-content-start"> -->
          <div class="row justify-content-between">
          <?php
            $userObject = new User("","",""); // we don’t need to assign any variables here; we just need it to query the database author table
            if ($userObject->IsCurrentUserAnEditor()) {

              // for editor, we will have a frozen checkbox
              // with date dislayed beneath

              // a survival checkbox
              // with date dislayed beneath

              // a move to checkbox (submit is the actual button)
              // with date dislayed beneath
              // if this has a date, all these buttons are disabled and can't enabled


               echo "<div class='col-md-3'>";
                echo "<div class='custom-control custom-checkbox'>";
                  if ($theHandOffState) {
                    echo "<input type='checkbox' class='form-check-input handoff ' checked $theHandOffButtonState id='handoffButtonID'>";
                  } else {
                    echo "<input type='checkbox' class='form-check-input handoff ' $theHandOffButtonState id='handoffButtonID'>";
                  }
                  echo "<label class='form-check-label' for='frozenButtonID'>Handed Off?</label>";
                echo "</div>";
              // forces some space
              echo "<span>" . ($theOriginalHandOffDate ?: '&nbsp;') . "</span>";
              echo "</div>";

              echo "<div class='col-md-3'>";
                echo "<div class='custom-control custom-checkbox'>";
                  if ($theFrozenState) {
                    echo "<input type='checkbox' class='form-check-input frozen ' checked $theFrozenButtonState id='frozenButtonID'>";
                  } else {
                    echo "<input type='checkbox' class='form-check-input frozen ' $theFrozenButtonState id='frozenButtonID'>";
                  }
                  echo "<label class='form-check-label' for='frozenButtonID'>Frozen?</label>";
                echo "</div>";
              echo "<span'>$theOriginalDateFrozen</span>";
              echo "</div>";
           
              echo "<div class='col-md-3'>";
                echo "<div class='custom-control custom-checkbox'>";
                  if ($theSurvivalState) {
                    echo "<input type='checkbox' class='form-check-input survival ' checked $theSurvivalButtonEnabledState id='survivalButtonID'>";
                  } else {
                    echo "<input type='checkbox' class='form-check-input survival ' $theSurvivalButtonEnabledState id='survivalButtonID'>";
                  }
                  echo "<label class='form-check-label' for='survivalButtonID'>Survived?</label>";
                echo "</div>";
              echo "<span>$theOriginalSurvivalDate</span>"; 
              echo "</div>";

              echo "<div class='col-md-3'>";
                echo "<div class='custom-control custom-checkbox'>";
                  if ($theMovedState) {
                    echo "<input type='checkbox' class='form-check-input finaldestination ' checked $theMoveButtonEnabledState id='movedButtonID'>";
                    echo "<label class='form-check-label' for='movedButtonID'>Moved To Final Destination</label>";
                  } else {
                    echo "<input type='checkbox' class='form-check-input finaldestination ' $theMoveButtonEnabledState id='movedButtonID'>";
                    echo "<label class='form-check-label' for='movedButtonID'>Move To Final Destination?</label>";
                  }
                echo "</div>";
              echo "<span'>$theOriginalMovedDate</span>"; 
              echo "</div>";
           
            } else {
               echo "<div class='col-md-3'>";
                echo "<div class='custom-control custom-checkbox'>";
                  if ($theHandOffState) {
                    echo "<input type='checkbox' class='form-check-input handoff ' checked $theHandOffButtonState id='handoffButtonID'>";
                  } else {
                    echo "<input type='checkbox' class='form-check-input handoff ' $theHandOffButtonState id='handoffButtonID'>";
                  }
                  echo "<label class='form-check-label' for='frozenButtonID'>Handed Off?</label>";
                echo "</div>";
              // forces some space
              echo "<span>" . ($theOriginalHandOffDate ?: '&nbsp;') . "</span>";
              echo "</div>";
            }
          ?>
				</div>
        </div>
        
			</div>

      <div class="row">
        <div class="col-md-3 mb-3">
          <?php
          if ($isStrainBeingEdited) { //this shows only if we are editing.
            // mt-2 to give some space above
            echo "<label class='mt-2' for='dateThawed_InputID'>date thawed</label>";
            if ($theOriginalDateThawed != NULL) {
              echo "<input type='date' id='dateThawed_InputID' class='form-control' value=$theOriginalDateThawed name='dateThawed_htmlName' title='dateThawed'>";
            } else {
              echo "<input type='date' id='dateThawed_InputID' class='form-control' name='dateThawed_htmlName' title='dateThawed'>";
            }
            echo "<div class='invalid-feedback'>";
              echo "Assign a thawed date when last tube is checked.";
            echo "</div>";
          }
          ?>
        </div>
      </div>

      <div class="row">

        <div class="form-group col-md-5 mb-3">
          <?php
            // what should edit page display. we should display the actual values!
            if (!$isStrainBeingEdited) {
              require_once("../classes/classes_gene_elements.php");
              $theTempStrain = createStrainPlaceHolder();
              $theTempStrain->prepareNextFreezerNitrogenNumber($theFreezerNumber_placeholder, $thereezerIndex__placeholder, $theNitrogenNNumber__placeholder, $theNitrogenIndex__placeholder);
              $theTempStrain->returnFreezerNitrogenNumbers($theFreezerNumber,$theNitrogenNumber);
              echo "<label class='control-label' >tentative locations: $theFreezerNumber; $theNitrogenNumber</label>";
            } else {
              echo "<label class='control-label' >storage locations: $theOriginalFullFreezer; $theOriginalNitrogen</label>";
            }
          ?>
        </div>

      </div>

      <div class="row">
          <div class="col-md-5 mb-3">

          </div>
          <div class="col-md-2 mb-3">
            <button type="button" id="cancel" class="form-control">Cancel</button>
          </div>
          <div class="col-md-3 mb-3">

            <?php
              echo "<input type='hidden' name='originalStrainEdited' value=\"$isStrainBeingEdited\">";

              echo "<input type='hidden' name='originalHandOffDate_postvar' value=$theOriginalHandOffDate>";
              echo "<input type='hidden' name='originalDateFrozen_postvar' value=$theOriginalDateFrozen>";

            
              echo "<input type='hidden' name='originalSurvivalDate_postvar' value=$theOriginalSurvivalDate>";
              echo "<input type='hidden' name='originalMovedDate_postvar' value=$theOriginalMovedDate>";

              if ($isStrainBeingEdited) {
                echo "<input type=\"hidden\" name=\"originalGeneNumbers_postVar\" value=$strainNameArray[1]>";
                echo "<input type=\"hidden\" name=\"originalGeneLetters_postVar\" value=$strainNameArray[0]>";
                echo "<input type='hidden' name='originalgeneElementID_postVar' value=$theOriginalStrainID>";
                echo "<input type='hidden' name='comment_postvar' value=\"$theOriginalComment\">";
                echo "<input type='hidden' name='fullFreezer_postvar' value=\"$theOriginalFullFreezer\">";
                echo "<input type='hidden' name='fullNitrogen_postvar' value=\"$theOriginalNitrogen\">";
                echo "<input type='hidden' name='originalContributorID_postvar' value=\"$theOriginalContributor\">";
                
                echo "<input type='hidden' name='originalDateThawed_postvar' value=$theOriginalDateThawed>";
                echo "<input type='hidden' name='originalIsolationName_postvar' value=$theOriginalIsolationName>";

                echo "<input type='hidden' name='originalIsLastVial_postvar' value=$theOriginalIsLastVial>";
                echo "<input type='hidden' name='originalLastVialer_postvar' value=$theOriginalLastVialer>";

                


                echo "<input type='hidden' name='isLabProduced_postvar' value=$labProduced>";
                echo "<input type='submit' name='acceptNewStrainEntry_htmlName' class='btn btn-primary btn-block' value='Accept Edited Strain' alt='Accept Edited Strain' style='float:right'/>";
              } else {
                echo "<input type='submit' name='acceptNewStrainEntry_htmlName' class='btn btn-primary btn-block' value='Accept Strain Entry' alt='Accept Strain Entry' style='float:right'/>";
              }
            ?>
        </div>
      </div>
    </form>
  </div>

    <script>
      $('input[name=manufacturedWhere_htmlName]').change(function() {
      if($(this).val() == 'externally-sourced_value') {
        $('input[name=manufacturedWhereLetters_htmlName]').prop('disabled', false);
        $('input[name=manufacturedWhereNumbers_htmlName]').prop('disabled', false);
        edit_strain_source_buttons(); // it’s part of a function
       }
       else {
        $('input[name=manufacturedWhereLetters_htmlName]').prop('disabled', true);
        $('input[name=manufacturedWhereNumbers_htmlName]').prop('disabled', true);
        edit_strain_source_buttons();  // it’s part of a function
       }
      });

      $('input[name=locationInCell]').change(function()
      {
        if($(this).val() == 'integrated') {
          $('input[name=chromosome]').prop('disabled', false);
        }
        else {
          $('input[name=chromosome]').prop('disabled', true);
        }
      });
    </script>

  </body>
</html>
