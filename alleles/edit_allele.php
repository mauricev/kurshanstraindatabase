<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>KurshanLab Strain Database</title>

  <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
  <script src="/js/jquery.min.js"></script>
  <script src="/js/bootstrap/js/bootstrap.min.js"></script>
  <!-- for sequence file -->
  <script src="/js/bs-custom-file-input.min.js"></script>
  <script src="/js/selectize.js"></script>
  <script src="/js/common-functions.js"></script>
  <script src="/js/allele-javascript.js"></script>

  <script>
    $( document ).ready(function()
    {
      <!-- for sequence file -->
      bsCustomFileInput.init();

      $('#select-gene').selectize({
        create: false,
        sortField: {
          field: 'text',
          direction: 'asc'
        },
        dropdownParent: 'body'
      });

      cancelButton();
    });

    $('input[name=geneElementLetters_htmlName]').prop('disabled', true);
		$('input[name=geneElementNumbers_htmlName]').prop('disabled', true);

    // checks to see if which one, lab-produced or externally-sourced is checked
    // and proceeds to update the edit fields accordingly.
    allelesUpdateEditFieldState();

  </script>

</head>
  <body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
        <?php
          $isEntityBeingEdited = false;
          if (isset($_POST['allelesArray_htmlName'])) {
            $isEntityBeingEdited = true;
            $selectedElement = $_POST['allelesArray_htmlName'];
            // $selectedElement[0] this is the allele_id

            $geneElementObjectToEdit = new LoadAllele();
            $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($selectedElement[0]);
            $alleleName = htmlspecialchars($geneElementArrayToEdit['alleleName_col'],ENT_QUOTES);
            echo "<p class='lead'>Edit Allele, $alleleName</p>";
          }
          else {
            echo "<p class='lead'>Add New Allele</p>";
          }
        ?>
      </div>

      <?php
        if ($isEntityBeingEdited) {
          // allele name is composed of letters and numbers; there is no hyphen!
          // solution is to create an array, one for the letters and one for the numbers
          // $selectedElement[0] and $geneElementArrayToEdit['alleleName_col'] should be identical
          $alleleNameArray = preg_split('/([a-z]+)/', $alleleName, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

          // $selectedElement[0] is identical to $geneElementArrayToEdit['allele_id']
          $theOrignalAlleleID = $geneElementArrayToEdit['allele_id'];

          // this is the gene the allele belongs to
          $theOriginalGeneOfTheAllele = $geneElementArrayToEdit['gene_fk'];

          $theOriginalComment = htmlspecialchars($geneElementArrayToEdit['comments_col'],ENT_QUOTES);

          // load up the sequence file if it exists
          require_once("../sequence/pre-sequence.php");
        }
      ?>
        <!-- <form action="../alleles/submit_edited_allele.php" onsubmit="$(this).find('input').prop('disabled', false)" method="post"> -->
        <!-- enctype="multipart/form-data" is required to load the file -->
        <form class="needs-validation" novalidate action="../alleles/submit_edited_allele.php" method="post" enctype="multipart/form-data">

        <div class="row">
          <div class="col-md-3 mb-3">
            <div class="form-check">
              <?php
                require_once("../classes/classes_gene_elements.php");
                if ($isEntityBeingEdited) {
                  if($alleleNameArray[0] == 'kur') {
                    echo "<input type = 'radio' id='lab-produced' name ='manufacturedWhere_htmlName' checked value= 'lab-produced' class='form-check-input' required>";
        					} else {
                    echo "<input type = 'radio' id='lab-produced' name ='manufacturedWhere_htmlName' value= 'lab-produced' class='form-check-input' required>";
        					}
                } else {
                  echo "<input type = 'radio' id='lab-produced' name ='manufacturedWhere_htmlName' value= 'lab-produced' class='form-check-input' required>";
                }
                $theTempAllele = new Allele("","","","","");
                $theTempAllele->getNextName($theGeneCurrentCount,$theNextName);
                echo "<input type='hidden' id='alleleBeingEditedHiddenField' name='hidden-label' value=$isEntityBeingEdited>";
                echo "<input type='hidden' id='kurAlleleHiddenField' name='hidden-label' value=$theNextName>";
                if ($isEntityBeingEdited) {
                  // we look at the existing allele name to know whether we were ORIGINALLY lab produced
                  // if we are, the display NOTHING
                  // else we need to display, BUT if we are currently lab produced and not being edited, we should display
                  $alleleIsLabProduced = ($alleleNameArray[0] == 'kur');
                  echo "<input type='hidden' id='alleleLabProducedStateHiddenField' name='hidden-label' value=$alleleIsLabProduced>";
                }
              ?>
              <label id='lab-produced-label' class="form-check-label" for="lab-produced">lab-produced</label>
            </div>
      		</div>
          <div class="col-md-1">
            <label id='letters-label' class="tinylabel" style="padding-left:10px" for="geneElementLetters_htmlName" >allele letters</label>
          </div>
          <div class="col-md-2">
            <label id='numbers-label' class="tinylabel" style="padding-left:10px" for="geneElementNumbers_htmlName" >allele numbers</label>
          </div>
      	</div>

          <div class="row">
            <div class="col-md-2 mb-3">
              <div class="form-check">
                <?php
                  if ($isEntityBeingEdited) {
                    if($alleleNameArray[0]== 'kur') {
                        echo "<input type = 'radio' id='externally-sourced' name ='manufacturedWhere_htmlName' value= 'externally-sourced' class='form-check-input' required>";
                    } else {
                        echo "<input type = 'radio' id='externally-sourced' name ='manufacturedWhere_htmlName' checked value= 'externally-sourced' class='form-check-input' required>";
            				}
                  } else {
                    echo "<input type = 'radio' id='externally-sourced' name ='manufacturedWhere_htmlName' value= 'externally-sourced' class='form-check-input' required>";
                  }
                ?>
                <label class="form-check-label" for="externally-sourced">externally-sourced</label>
                <div class="invalid-feedback">
    							Please pick lab-produced or externally-sourced.
    						</div>
              </div>
    				</div>
            <!-- <div class="form-check">
            <!-- <div class="col-xs-1 mb-3" style="padding-top:4px;padding-right:1px">
              <!-- <label for="geneElementLetters_htmlName">(</label> -->

            <!-- </div> -->

            <div class='col-md-2 mb-3'>
                <div>
                  <p>(</p>
                  <div class="form-check" style="margin-top:-40px">
                  <?php
                    if ($isEntityBeingEdited) {
                      if($alleleNameArray[0]== 'kur') {
            					  echo "<input type='text' name='geneElementLetters_htmlName' pattern='[a-z]{1,3}' disabled required class='form-control' title='letters'/>";
                      } else {
                        echo "<input type='text' name='geneElementLetters_htmlName' pattern='[a-z]{1,3}' required class='form-control' value=$alleleNameArray[0] title='letters'/>";
                      }
                    } else {
                      echo "<input type='text' name='geneElementLetters_htmlName' pattern='[a-z]{1,3}' required class='form-control' title='letters'/>";
                    }
                  ?>
                  <div class="invalid-feedback">
        						For externally-sourced alleles, please enter the lowercase letter designation. Leave out parentheses.
        					</div>
                </div>
              </div>
            </div>

    				<div class='col-md-3 mb-3'>
              <p style="margin-left:280px">)</p>
              <div class="form-check" style="margin-top:-40px">
                <?php
                  if ($isEntityBeingEdited) {
          					if($alleleNameArray[0]== 'kur') {
                      echo "<input type='text' name='geneElementNumbers_htmlName' pattern='[0-9]{1,6}' disabled required class='form-control' title='numbers'/>";
          				  } else {
                      echo "<input type='text' name='geneElementNumbers_htmlName' pattern='[0-9]{1,6}' required class='form-control' value=$alleleNameArray[1] title='numbers'/>";
                    }
                  } else {
                    echo "<input type='text' name='geneElementNumbers_htmlName' pattern='[0-9]{1,6}' required class='form-control' title='numbers'/>";
                  }
                ?>
                <div class="invalid-feedback">
      						For externally-sourced alleles, please enter the numeric designation. Leave out parentheses.
      					</div>
              </div>
    			</div>

          <div class="row">
      			<div class="col-md-4 mb-3">
      				<?php
                require_once("../classes/classes_load_elements.php");

                // the following statements are echo'ed in the original enter_new_allele
                // so they don't need a "double" echo.

      					$theGeneListing = new LoadGene();
      					$theArray = $theGeneListing->returnAll();
              ?>
      				<select id="select-gene" name="genes[]" placeholder="Select the corresponding gene...">
                <?php
                echo "<option value=\"\">Select the corresponding gene...</option>";
              	foreach($theArray as $row) {
      	  				$geneName = $row['geneName_col'];
      						$geneID = $row['gene_id'];
                  if (($isEntityBeingEdited) && ($geneID == $theOriginalGeneOfTheAllele)) {
                    echo "<option selected value='$geneID' >$geneName</option>";
                  } else  {
                    echo "<option value='$geneID'>$geneName</option>";
                  }
      					}
              ?>
      				</select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-9 mb-3">
      				<?php
                if ($isEntityBeingEdited) {
                  echo "<input type='text' id='comments_fieldID' name='comments_postvar' class='form-control' placeholder='comment' value=\"$theOriginalComment\">";
                } else {
                  echo "<input type='text' id='comments_fieldID' name='comments_postvar' class='form-control' placeholder='comment'>";
                }
              ?>
            </div>
        </div>

        <?php
          // common sequence code for plasmids and alleles
          // choose file
          // choose sequence file
          require_once("../sequence/sequence-form.php");
        ?>

        <div class=" row">
  				<div class="col-md-5 mb-3">
            <?php
              echo "<input type='hidden' name='originalAlleleEdited' value=\"$isEntityBeingEdited\">";
              if ($isEntityBeingEdited) {
                // pass the original values so we can check to see if they need updating.

                //sequence stuff first
                require_once("../sequence/post-sequence.php");

                echo "<input type='hidden' name='original_allele_id_postvar' value=$theOrignalAlleleID>";
                echo "<input type='hidden' name='original_allele_letters_postvar' value=$alleleNameArray[0]>";
                echo "<input type='hidden' name='original_allele_numbers_postvar' value=$alleleNameArray[1]>";
                echo "<input type='hidden' name='originalComment_htmlName' value='$theOriginalComment'>";
                echo "<input type='hidden' name='originalSelectedGene_htmlName' value='$theOriginalGeneOfTheAllele'>";
              }
            ?>
  				</div>
          <div class="col-md-2 mb-3">
  					<button type="button" id="cancel" class="form-control">Cancel</button>
  				</div>
  				<div class="col-md-3 mb-3">
            <?php
            if ($isEntityBeingEdited) {
  					  echo "<input type='submit' class='btn btn-primary btn-block' name='accept_allele_edit' value='Accept Allele Edit' alt='Accept Allele Edit'/>";
            }
            else {
              echo "<input type='submit' class='btn btn-primary btn-block' name='accept_allele_edit' value='Accept Allele Entry' alt='Accept Allele Entry'/>";
            }
            ?>
  				</div>
        </div>

      </form>
    </div>

    <script>
      $('input[name=manufacturedWhere_htmlName]').change(function() {
        allelesUpdateEditFieldState();
      });
    </script>
    <script src="/js/sequence.js"></script>


  </body>
</html>
