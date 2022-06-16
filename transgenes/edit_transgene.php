<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>KurshanLab Strain Database</title>

  <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
  <script src="/js/jquery.min.js"></script>
  <script src="/js/bootstrap/js/bootstrap.min.js"></script>
  <script src="/js/selectize.js"></script>
  <script src="/js/common-functions.js"></script>
  <script src="/js/transgene-javascript.js"></script>

  <script>
	$( document ).ready(function()
	{

    $('#select-contributors').selectize({
      create: false,
      sortField: {
        field: 'text',
        direction: 'asc'
      },
      dropdownParent: 'body'
    });

    $('#select-transgene').selectize({
      create: false,
      sortField: {
        field: 'text',
        direction: 'asc'
      },
      dropdownParent: 'body'
    });

    $('#select-plasmid').selectize({
      create: false,
      sortField: {
        field: 'text',
        direction: 'asc'
      },
      dropdownParent: 'body'
    });

		$('#select-coinjection_markers').selectize({
      create: false,
      sortField: {
        field: 'text',
        direction: 'asc'
      },
      dropdownParent: 'body'
    });

    // by default disable parent transgene table
    $('#select-transgene')[0].selectize.disable();
		$('#select-plasmid')[0].selectize.disable();
		$('#select-coinjection_markers')[0].selectize.disable();

    all_transgene_update_buttons();
    edit_transgene_update_buttons();

    cancelButton();

	});
	</script>

</head>
  <body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="72" height="72">
        <h2>KurshanLab Strain Database</h2>
<!-- title of page, with transgene name when editing -->
<!-- also fills the array with the transgene info -->
        <?php
          $isTransGeneBeingEdited = false;
          if (isset($_POST['transgeneArray_htmlName'])) {
            $isTransGeneBeingEdited = true;
            // id of selected transgene inside lister
            $selectedElement = $_POST['transgeneArray_htmlName'];

            $geneElementObjectToEdit = new LoadTransGene();
            $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($selectedElement[0]);
            $theTransGeneName = htmlspecialchars($geneElementArrayToEdit['transgeneName_col'],ENT_QUOTES);
            echo "<p class='lead'>Edit Transgene, $theTransGeneName</p>";
          } else {
            echo "<p class='lead'>Add New Transgene</p>";
          }
        ?>
      </div>

      <?php
          // this value contains the transgene_id
        if ($isTransGeneBeingEdited) {

          $theTransGeneID = $geneElementArrayToEdit['transgene_id'];

          // reg split string between Ex, Is, and Si
          $transGeneNameArray = preg_split( "/(Ex|Is|Si)/", $theTransGeneName);
          $theOriginalChromosome = $geneElementArrayToEdit['chromosomeName_col'];

          $theOriginalComment = htmlspecialchars($geneElementArrayToEdit['comments_col'],ENT_QUOTES);

          $theOriginalContributor = $geneElementArrayToEdit['contributor_fk'];

          $theOriginalParentTransGeneID = $geneElementArrayToEdit['parent_transgene_col'];

          $theOldTransgeneLocationString = "Ex";
          $isIntegrated = preg_match("/Is/",$theTransGeneName);
          if ($isIntegrated) {
            $theOldTransgeneLocationString = "Is";
          }
          $isSingleInsertion = preg_match("/Si/",$theTransGeneName);
          if ($isSingleInsertion) {
            $theOldTransgeneLocationString = "Si";
          }

          $theOriginalCoInjectionMarkerID = $geneElementArrayToEdit['coInjectionMarker_fk'];

          // // we store the orignal values in post variables
          // $_POST['original_name'] = $theTransGeneName;
          // $_POST['original_chromosome_postvar'] = $theOriginalChromosome;
          // $_POST['original_comment_postvar'] = $theOriginalComment;
          //
          // $_POST['original_parentTransGene_postvar'] = $theOriginalParentTransGeneID;
          //
          // // this value contains the transgene_id
          // $_POST['selectedElement'] = $selectedElement[0];

          $IsInternallyProduced = preg_match("/kur/",$theTransGeneName);
        }
      ?>
      <form class="needs-validation" novalidate action="../transgenes/submit_edited_transgene.php" method="POST">
        <div class="row">
          <div class="col-md-3 mb-3">
            <div class="custom-control custom-radio">
              <?php
                if($isTransGeneBeingEdited) {
                  if($isIntegrated){
                    echo "<input type = 'radio' id='extra-chromosomal_id' name ='locationInCell_htmlName' value= 'extra-chromosomal' class='custom-control-input' required>";
                  } else {
                    echo "<input type = 'radio' id='extra-chromosomal_id' name ='locationInCell_htmlName' value= 'extra-chromosomal' class='custom-control-input' checked required>";
                  }
                } else {
                  echo "<input type = 'radio' id='extra-chromosomal_id' name ='locationInCell_htmlName' value= 'extra-chromosomal' class='custom-control-input' required>";
                }
              ?>
              <label class="custom-control-label" for="extra-chromosomal_id">extra-chromosomal (Ex)</label>
            </div>
          </div>
          <div class="col-md-2 mb-3">
            <div class="custom-control custom-radio">
              <?php
                if($isTransGeneBeingEdited) {
                  if($isIntegrated){
                    echo "<input type = 'radio' id='integrated_id' name ='locationInCell_htmlName' value= 'integrated' class='custom-control-input' checked required>";
                  } else {
                    echo "<input type = 'radio' id='integrated_id' name ='locationInCell_htmlName' value= 'integrated' class='custom-control-input' required>";
                  }
                }
                else {
                  echo "<input type = 'radio' id='integrated_id' name ='locationInCell_htmlName' value= 'integrated' class='custom-control-input' required>";
                }
              ?>
              <label class="custom-control-label" for="integrated_id">integrated (Is)</label>
              <div class="invalid-feedback">
						    Please pick extra-chromosomal, integrated or single insertion.
					    </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <?php
              echo "<select id='select-chromosome' class='form-control' name='chromosome_htmlName' placeholder='associated chromosome...'>";
              echo "<option value=''>enter associated chromosome...</option>";
              $theChromosomeArray = array("I", "II", "III", "IV", "V", "X");
              foreach ($theChromosomeArray as $theChromosome) {
                if ( ($isTransGeneBeingEdited) && ($isIntegrated || $isSingleInsertion) && ($theChromosome == $theOriginalChromosome) ) {
                  echo "<option value=$theChromosome selected>$theChromosome</option>";
                } else {
                  echo "<option value=$theChromosome>$theChromosome</option>";
                }
              }
              echo "</select>";
            ?>
          </div>
        </div>

        <!-- single insertion button -->
        <div class="row">
          <div class="col-md-3 mb-3">
          </div>
          <div class="col-md-3 mb-3">
            <div class="custom-control custom-radio">
              <?php
                if($isTransGeneBeingEdited) {
                  if($isSingleInsertion){
                    echo "<input type = 'radio' id='single_insertion_id' name ='locationInCell_htmlName' value= 'single insertion' class='custom-control-input' checked required>";
                  } else {
                    echo "<input type = 'radio' id='single_insertion_id' name ='locationInCell_htmlName' value= 'single insertion' class='custom-control-input' required>";
                  }
                } else {
                  echo "<input type = 'radio' id='single_insertion_id' name ='locationInCell_htmlName' value= 'single insertion' class='custom-control-input' required>";
                }
              ?>
              <label class="custom-control-label" for="single_insertion_id">single insertion (Si)</label>
            </div>
          </div>
        </div>

        <div class="row">
			    <div class="col-md-3 mb-3">
				   <div class="custom-control custom-radio">
            <?php
              // we are assigning the html to a variable
              $plainLabProducedRadioBtnText = "<input type = 'radio' id='lab-produced_id' name ='manufacturedWhere_htmlName' value= 'lab-produced' class='custom-control-input' required>";
              if ($isTransGeneBeingEdited) {
                if($IsInternallyProduced){
    						  echo "<input type = 'radio' id='lab-produced_id' name ='manufacturedWhere_htmlName' value= 'lab-produced' checked class='custom-control-input' required>";
    						} else {
                  echo $plainLabProducedRadioBtnText;
                }
              } else {
                echo $plainLabProducedRadioBtnText;
              }

              // the code below is getting the next number
              // we only display here the upcoming number if it's appropriate; the current number is in the title
              require_once("../classes/classes_gene_elements.php");

              buildTransGeneHiddenField("Ex","kurNumberExHiddenField");
              buildTransGeneHiddenField("Is","kurNumberIsHiddenField");
              buildTransGeneHiddenField("Si","kurNumberSiHiddenField");

              // we need to pass $isTransGeneBeingEdited, so javascript knows whether we are new or editing
              echo "<input type='hidden' id='isTransGeneBeingEditedHiddenField' name='hidden-label' value=$isTransGeneBeingEdited>";
              if ($isTransGeneBeingEdited) {
                echo "<input type='hidden' id='kurLabProducedStateHiddenField' name='hidden-label' value=$IsInternallyProduced>";
                // was $isIntegrated: this was a big bug
                echo "<input type='hidden' id='kurExOrIsStateHiddenField' name='hidden-label' value=$theOldTransgeneLocationString>";
              }
            ?>
            <label id='lab-label' class='custom-control-label' for='lab-produced_id'></label>
  				</div>
  			</div>
        <div class="col-md-1 mb-0">
          <label id='letters-label' class="tinylabel" style="padding-left:10px" for="transgene_letters_name" >transgene letters</label>
        </div>
        <div class="col-md-1 mb-0">
          <label id='numbers-label' class="tinylabel" style="padding-left:10px" for="transgene_numbers_name" >transgene numbers</label>
        </div>
  		</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<?php
              $plainExternallySourcedRadioBtnText = "<input type = 'radio' id='externally-sourced_id'  name ='manufacturedWhere_htmlName' value= 'externally-sourced' class='custom-control-input' required>";
              if ($isTransGeneBeingEdited) {
                if($IsInternallyProduced){
                  echo $plainExternallySourcedRadioBtnText;
    						} else {
                  echo "<input type = 'radio' id='externally-sourced_id'  name ='manufacturedWhere_htmlName' value= 'externally-sourced' checked class='custom-control-input' required>";
                }
              } else {
                echo $plainExternallySourcedRadioBtnText;
              }
            ?>
            <label class="custom-control-label" for="externally-sourced_id">externally-sourced</label>
            <div class="invalid-feedback">
							Please pick lab-produced or externally-sourced.
						</div>
					</div>
  			</div>
				<div class="col-md-1 mb-3 pr-0">
          <?php
            if ($isTransGeneBeingEdited) {
              if($IsInternallyProduced){
    					 echo "<input type='text' name='transgene_letters_name' pattern='[a-z]{1,3}' required class='form-control' disabled/>";
              } else {
               echo "<input type='text' name='transgene_letters_name' pattern='[a-z]{1,3}' required class='form-control' value=$transGeneNameArray[0]>";
              }
            } else {
              echo "<input type='text' name='transgene_letters_name' pattern='[a-z]{1,3}' required class='form-control'/>";
            }
          ?>
         <div class="invalid-feedback">
						For externally-sourced transgenes, please enter the lowercase letter designation.
					</div>
				</div>
			  <div class="col-md-2 mb-3">
          <!-- this EX-IS-LABEL is for labeling an externally-sourced transgene as Ex or Is -->
          <label id="ex-is-label" class="label mt-2" style="font-size:9px !important" for="transgene_numbers_name"></label>
					<?php
            if ($isTransGeneBeingEdited) {
              if($IsInternallyProduced){
    					 echo "<input type='text' name='transgene_numbers_name' pattern='[0-9]{1,6}' required class='form-control' disabled style='float:right;width:85%'/>";
              } else {
               echo "<input type='text' name='transgene_numbers_name' pattern='[0-9]{1,6}' required class='form-control' value=$transGeneNameArray[1]  style='float:right;width:85%'/>";
              }
            } else {
              echo "<input type='text' name='transgene_numbers_name' pattern='[0-9]{1,6}' required class='form-control' style='float:right;width:85%'/>";
            }
          ?>
          <div class="invalid-feedback">
					  For externally-sourced transgenes, please enter the numeric designation.
				  </div>
        </div>
			</div>

      <div class="row">
        <div class="form-group col-md-3 mb-3">
          <?php
            require_once("../classes/classes_load_elements.php");
            $theContributorListing = new LoadContributor();
            if ($isTransGeneBeingEdited) {
              $theContributorListing->buildSelectedTablesWithSingleEntry($theOriginalContributor);
            } else {
              $isMultipleSelection = false;
              $theContributorListing->buildSelectTable($isMultipleSelection);
            }
          ?>
        </div>
      </div>

      <div class="row">
				<div class="col-md-9 mb-3">
          <?php
            if ($isTransGeneBeingEdited) {
					    echo "<input type='text' id='comments_fieldID' class='form-control' value=\"$theOriginalComment\" class='form-control' placeholder='comments go here' name='comments_fieldName'>";
            } else {
              echo "<input type='text' id='comments_fieldID' class='form-control' class='form-control' placeholder='comments go here' name='comments_fieldName'>";
            }
          ?>
				</div>
			</div>

      <div class="row">
				<div id="transgene_div" class="col-md-3 mb-3">
					<select id="select-transgene" name="transgeneArray_htmlName[]" placeholder="associated extra-chromosomal transgene...">
					<option value=''>associated the extra-chromosomal transgene...</option>
          <?php
  					require_once("../classes/classes_load_elements.php");

  					$theTransGeneListing = new LoadTransGene();
  					$theArray = $theTransGeneListing->returnAll();
            foreach($theArray as $row) {
  	  				$transGeneName = htmlspecialchars($row['transgeneName_col'],ENT_QUOTES);
  						$transGeneID = $row['transgene_id'];

  						$theExMatch = preg_match ("/Ex/",$transGeneName);
  						if ($theExMatch){
                if ( ($isTransGeneBeingEdited) && ($transGeneID == $theOriginalParentTransGeneID) ) {
                  echo "<option selected value='$transGeneID'>$transGeneName</option>";
                } else {
                  echo "<option value='$transGeneID'>$transGeneName</option>";
                }

  						}
  					}
          ?>
					</select>
				</div>
        <div class="col-md-1 mb-3">

  		  </div>

        <div class="col-md-4 mb-3">
          <!-- BUG doesn't read saved value from anywhere -->
          <input class="form-check-input" type="checkbox" value="" id="exTransGeneNA_htmlID">
          <label class="form-check-label" for="exTransGeneNA_htmlID">extra-chromosomal transgene unknown</label>
        </div>
  		</div>

      <div class="row">
        <div class="col-md-2 mb-3">
          <?php
            require_once("../classes/classes_load_elements.php");
            $theCoInjectionMarkerListing = new LoadCoInjectionMarker();
            if ($isTransGeneBeingEdited) {
              $theCoInjectionMarkerListing->buildSelectedTablesWithSingleEntry($theOriginalCoInjectionMarkerID);
            } else {
              $multiple = false;
					    $theCoInjectionMarkerListing->buildSelectTable($multiple);
            }
          ?>
        </div>
  		</div>

      <div class="row">
        <div class="col-md-3 mb-3">
          <?php
            require_once("../classes/classes_load_elements.php");
            $thePlasmidListing = new LoadPlasmid();
            if ($isTransGeneBeingEdited) {
              $theMarkedPlasmids = new LoadPlasmidsToTransGenes($theTransGeneID);
              $theMarkedPlasmidsArray = $theMarkedPlasmids->ReturnMarkedGeneElements();
              //create a hidden field to house the original plamsids associated with this
              $theMarkedPlasmids->PopulateHiddenArray();
              $thePlasmidListing->buildSelectedTablesWithMultipleEntries($theMarkedPlasmidsArray);
            } else {
              $multiple = true;
					    $thePlasmidListing->buildSelectTable($multiple);
            }
          ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-5 mb-3">
				</div>
        <div class="col-md-1 mb-3">
            <button type="button" id="cancel" class="form-control">Cancel</button>
        </div>
        <div class="col-md-3 mb-3">
          <?php
            if ($isTransGeneBeingEdited) {
              echo "<input type='submit' name='accept_transgene_entry' class='btn btn-primary btn-block' value='Edit Transgene Entry' alt='Edit Transgene Entry'/>";
            } else {
              echo "<input type='submit' name='accept_transgene_entry' class='btn btn-primary btn-block' value='Accept Transgene Entry' alt='Accept Transgene Entry'/>";
            }
          ?>
        </div>
          <?php
            echo "<input type='hidden' name='originalTransGeneEdited' value=\"$isTransGeneBeingEdited\">";
            if ($isTransGeneBeingEdited) {
              echo "<input type='hidden' name='original_transgeneLocation_postVar' value=$theOldTransgeneLocationString>";

              echo "<input type='hidden' name='orginalChromosome_postVar' value=$theOriginalChromosome>";
              echo "<input type='hidden' name='originalComment_postVar' value=\"$theOriginalComment\">";
              echo "<input type='hidden' name='originalContributorID_postvar' value=\"$theOriginalContributor\">";
              echo "<input type='hidden' name='originalcoInjectionMarker_postvar' value=$theOriginalCoInjectionMarkerID>";
              echo "<input type='hidden' name='originalParentExTransGene_postvar' value=$theOriginalParentTransGeneID>";
              echo "<input type='hidden' name='originalGeneNumbers_postVar' value=$transGeneNameArray[1]>";
              echo "<input type='hidden' name='originalGeneLetters_postVar' value=$transGeneNameArray[0]>";

              echo "<input type='hidden' name='originalgeneElementID_postVar' value=$selectedElement[0]>";  // this is the transgene ID
              // plasmids are saved separately above
            }
          ?>
      </form>
    </div>

    <script>

  		$('input[name=manufacturedWhere_htmlName]').change(function() {
        all_transgene_update_buttons();
        edit_transgene_update_buttons();
  		});

  		$("input[name='locationInCell_htmlName']").change(function() {
        all_transgene_update_buttons();
        edit_transgene_update_buttons();
  		});

      $('#exTransGeneNA_htmlID').change(function() {
        all_transgene_update_buttons();
  		});

  	</script>
  </body>
</html>
