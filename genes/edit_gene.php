<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/common-functions.js"></script>

    <script>
      $( document ).ready(function()
      {
      	cancelButton();
      });

      function toggleGeneFields() {
        const isChecked = document.getElementById('toggleFields').checked;
        const geneFields = document.querySelectorAll('.gene-field');
        const alternateInput = document.getElementById('alternateInput');

        geneFields.forEach(field => {
          field.disabled = isChecked;
          field.required = !isChecked; // toggle required attribute
        });

        alternateInput.disabled = !isChecked;
        alternateInput.required = isChecked; // toggle required attribute
      }

    </script>

  </head>
  <body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
        <?php
          $isGeneBeingEdited = false;
          if (isset($_POST['genesArray_htmlName'])) {
            $isGeneBeingEdited = true;
            $selectedElement = $_POST['genesArray_htmlName'];
            $geneElementObjectToEdit = new LoadGene();
            $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($selectedElement[0]);

            $geneName = htmlspecialchars($geneElementArrayToEdit['geneName_col'],ENT_QUOTES);

            echo "<p class='lead'>Edit Gene $geneName</p>";
          } else {
          echo "<p class='lead'>Add New Gene</p>";
        }
        ?>
      </div>
      <?php
      // $geneElementArrayToEdit contains an associative array of the gene record
      // first entry , gene_name has the hyphen if it a not a custom name;
      // if it is custom, we ignore any hyphens and we populate the custom name field
        if ($isGeneBeingEdited) {
          if ($geneElementArrayToEdit['customNameFlag'] != true) {
            $geneNameArray = explode("-",$geneName);
          } else {
            $geneNameArray[0] = $geneName;
          }
          
          $theOldChromosome = htmlspecialchars($geneElementArrayToEdit['chromosomeName_col'],ENT_QUOTES);

          $theOldComment = htmlspecialchars($geneElementArrayToEdit['comments_col'],ENT_QUOTES);

          $theOldCustomFlag = $geneElementArrayToEdit['customNameFlag'];

          $_POST['chromosomeName_postvar'] = $theOldChromosome;
          $_POST['geneNameArray_postvar'] = $geneNameArray;
          $_POST['geneCustomFlag_postvar'] = $theOldCustomFlag;
          // BUGfixed commented out below
          //$_POST['selectedElement_postvar'] = $selectedElement[0];
        } else {
          $theOldCustomFlag = false;
        }
      ?>
      <form class="needs-validation" novalidate action="../genes/submit_edited_gene.php" method="post">
        <div class="row">
          <div class="col-md-1">
            <label id='letters-label' class="tinylabel" for="geneLetters_postvar" >gene letters</label>
          </div>
          <div class="col-md-1">
            <label id='numbers-label' class="tinylabel" for="geneNumbers_postvar" >gene numbers</label>
          </div>
        </div>
        <div class="row">
          <div class="col-md-1 mb-3">
            <?php
              // Determine geneLetters_postvar field value and state based on $theOldCustomFlag
              $geneLettersValue = $isGeneBeingEdited && !$theOldCustomFlag ? $geneNameArray[0] : '';
              $geneLettersDisabled = $theOldCustomFlag ? 'disabled' : '';
              echo "<input name='geneLetters_postvar' type='text' pattern='[a-z]{1,4}' class='form-control gene-field' value='$geneLettersValue' $geneLettersDisabled required autofocus>";
            ?>
            <div class="invalid-feedback">
              Valid lowercase letters of the gene are required. Leave out the hyphen.
            </div>
          </div>

          <div class="col-md-1 mb-3">
            <?php
              // Determine geneNumbers_postvar field value and state based on $theOldCustomFlag
              $geneNumbersValue = $isGeneBeingEdited && !$theOldCustomFlag ? $geneNameArray[1] : '';
              $geneNumbersDisabled = $theOldCustomFlag ? 'disabled' : '';
              echo "<input name='geneNumbers_postvar' type='text' pattern='[0-9]{1,6}' class='form-control gene-field' value='$geneNumbersValue' $geneNumbersDisabled required>";
            ?>
            <div class="invalid-feedback">
              Valid numbers of the gene are required. Leave out the hyphen.
            </div>
          </div>

          <div class="col-md-1 mb-3">
            <!-- Set the checkbox checked attribute if $theOldCustomFlag is true -->
            <input type="checkbox" id="toggleFields" onclick="toggleGeneFields()" <?php echo $theOldCustomFlag ? 'checked' : ''; ?>> Use custom name:
          </div>

          <div class="col-md-4 mb-3">
            <!-- Set the value of alternateGeneInput and enable it based on $theOldCustomFlag -->
            <input type="text" name="alternateGeneInput" class="form-control" id="alternateInput" 
                   value="<?php echo $theOldCustomFlag ? $geneNameArray[0] : ''; ?>" 
                   <?php echo $theOldCustomFlag ? '' : 'disabled'; ?>>
          </div>
          <div class="col-md-4 mb-3">
            <?php
              echo "<select id='select-chromosome' class='form-control' name='chromosome_postvar' placeholder='enter associated chromosome...'>";
              echo "<option value=''>enter associated chromosome...</option>";
              $theChromosomeArray = array("I", "II", "III", "IV", "V", "X");
              foreach ($theChromosomeArray as $theChromosome) {
                if (($isGeneBeingEdited) && ($theChromosome == $theOldChromosome)) {
                  echo "<option value=$theChromosome selected>$theChromosome</option>";
                } else {
                  echo "<option value=$theChromosome>$theChromosome</option>";
                }
              }
              echo "</select>";
            ?>
          </div>

        </div>
        <div class="row">
          <div class="col-md-10 mb-3">
            <?php
              if ($isGeneBeingEdited) {
                echo "<textarea id='strainSpecificComments_ID' class='form-control rounded-0' rows='2' name='comments_postvar' title='strainSpecificComments' placeholder='comments about this gene' style='width:100%'>$theOldComment</textarea>";
              } else {
                echo "<textarea id='strainSpecificComments_ID' class='form-control rounded-0' rows='2' name='comments_postvar' title='strainSpecificComments' placeholder='comments about this gene' style='width:100%'></textarea>";

              }
              ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7 mb-3">
            <?php
              echo "<input type='hidden' name='originalGeneEdited' value=\"$isGeneBeingEdited\">";
              if ($isGeneBeingEdited) {
                echo "<input type='hidden' name='orginalChromosome_htmlName' value=\"$theOldChromosome\">";
                echo "<input type='hidden' name='originalComment_htmlName' value=\"$theOldComment\">";
                echo "<input type='hidden' name='originalGeneLetters_htmlName' value=$geneNameArray[0]>";
                if ($theOldCustomFlag == 0) {
                  echo "<input type='hidden' name='originalGeneNumbers_htmlName' value=$geneNameArray[1]>";
                }
                echo "<input type='hidden' name='originalgeneElementID_htmlName' value=$selectedElement[0]>";
                echo "<input type='hidden' name='originalCustomName_htmlName' value=$theOldCustomFlag>";
              }
            ?>
          </div>
          <div class="col-md-2 mb-3">
            <button type="button" id="cancel" class="form-control"  >Cancel</button>
          </div>
          <div class="col-md-3 mb-3">
            <?php
              if ($isGeneBeingEdited) {
                echo "<button type=submit class='btn btn-primary btn-block'>Accept Gene Edit</button>";
              }
              else {
                echo "<button type=submit class='btn btn-primary btn-block'>Accept Gene Entry</button>";
              }
            ?>
          </div>
          <div class="col-md-3 mb-3">
          </div>
      </div>
    </form>
  </div>
  </body>
</html>
