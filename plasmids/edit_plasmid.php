<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <title>KurshanLab Strain Database</title>
   	<meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap/js/bootstrap.min.js"></script>
    <!-- for sequence file -->
    <script src="/js/bs-custom-file-input.min.js"></script>
    <script src="/js/selectize.js"></script>
    <script src="/js/common-functions.js"></script>

    <script>
      $( document ).ready(function()
      {
        <!-- for sequence file -->
        bsCustomFileInput.init();

        $('#select-promoter').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-gene').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-antibiotics').selectize({
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

        $('#select-n-fluorotags').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-c-fluorotags').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-internal-fluorotags').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

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
          $isEntityBeingEdited = false;
          if (isset($_POST['plasmidArray_htmlName'])) {
            $isEntityBeingEdited = true;
            $selectedElement = $_POST['plasmidArray_htmlName'];

            $geneElementObjectToEdit = new LoadPlasmid();
            $geneElementArrayToEdit = $geneElementObjectToEdit->returnSpecificRecord($selectedElement[0]);

            $theOriginalPlasmidID = $geneElementArrayToEdit['plasmid_id'];

            $theOriginalPlasmidName = htmlspecialchars($geneElementArrayToEdit['plasmidName_col'],ENT_QUOTES);

            $theOriginalOthercDNA = htmlspecialchars($geneElementArrayToEdit['other_cDNA_col'],ENT_QUOTES);

            $theOriginalPlasmidLocation = "";
            if ($geneElementArrayToEdit['plasmidLocation_col'] != NULL) {
              $theOriginalPlasmidLocation = htmlspecialchars($geneElementArrayToEdit['plasmidLocation_col'],ENT_QUOTES);
            }
            
            $theOriginalComment = htmlspecialchars($geneElementArrayToEdit['comments_col'],ENT_QUOTES);

            $theOriginalContributor = $geneElementArrayToEdit['contributor_fk'];
            $theOriginalPromoter = $geneElementArrayToEdit['promotorGene_fk'];
            $theOriginalGene = $geneElementArrayToEdit['gene_fk'];

            // load up the sequence file if it exists
            require_once("../sequence/pre-sequence.php");

            echo "<p class='lead'>Edit Plasmid, $theOriginalPlasmidName</p>";
          } else {
            echo "<p class='lead'>Add New Plasmid</p>";
          }
        ?>

      </div>

      <!-- enctype="multipart/form-data" is required to load the file -->
      <form class="needs-validation" action="../plasmids/submit_edited_plasmid.php" method="post" enctype="multipart/form-data">

  			<div class="row">
  				<div class="form-group col-md-5 mb-3">
            <?php
              if ($isEntityBeingEdited) {
                echo "<input type='text' name='plasmidName_htmlName' required class='form-control' maxlength='15' value=\"$theOriginalPlasmidName\" placeholder='enter the plasmid name' title='plasmidName'/>";
              } else {
                echo "<input type='text' name='plasmidName_htmlName' required class='form-control' maxlength='15' value='' placeholder='enter the plasmid name' title='plasmidName'/>";
              }
              ?>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-2 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $thePromoterListing = new LoadPromoter();
              if ($isEntityBeingEdited) {
                $thePromoterListing->buildSelectedTablesWithSingleEntry($theOriginalPromoter);
              } else {
                $isMultipleSelection = false;
                $thePromoterListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>
          <div class="form-group col-md-2 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theGeneListing = new LoadGene();
              if ($isEntityBeingEdited) {
                $theGeneListing->buildSelectedTablesWithSingleEntry($theOriginalGene);
              } else {
                $isMultipleSelection = false;
                $theGeneListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>
          <div class="form-group col-md-2 mb-3">
            <?php
              if ($isEntityBeingEdited) {
                echo "<input type='text' name='other_cDNA_htmlName' class='form-control' value=\"$theOriginalOthercDNA\" placeholder='other cDNA' title='other_cDNA'/>";
              } else {
                echo "<input type='text' name='other_cDNA_htmlName' class='form-control' value='' placeholder='other cDNA' title='other_cDNA'/>";
              }
            ?>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-3 mb-3">
            <label class='control-label' for="select-n-fluorotags">Select N-placed tags</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theFluoroTagListing = new LoadNFluoroTag();
              if ($isEntityBeingEdited) {
                $theMarkedFluoroTags = new LoadFluoroNTagsToPlasmid($theOriginalPlasmidID);
                $theMarkedFluoroTagsArray = $theMarkedFluoroTags->ReturnMarkedGeneElements();
                $theMarkedFluoroTags->PopulateHiddenArray();
                $theFluoroTagListing->buildSelectedTablesWithMultipleEntries($theMarkedFluoroTagsArray);
              } else {
                $isMultipleSelection = true;
                $theFluoroTagListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>
          <div class="form-group col-md-3 mb-3">
            <label class='control-label' for="select-c-fluorotags">Select C-placed tags</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theFluoroTagListing = new LoadCFluoroTag();
              if ($isEntityBeingEdited) {
                $theMarkedFluoroTags = new LoadFluoroCTagsToPlasmid($theOriginalPlasmidID);
                $theMarkedFluoroTagsArray = $theMarkedFluoroTags->ReturnMarkedGeneElements();
                $theMarkedFluoroTags->PopulateHiddenArray();
                $theFluoroTagListing->buildSelectedTablesWithMultipleEntries($theMarkedFluoroTagsArray);
              } else {
                $isMultipleSelection = true;
                $theFluoroTagListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>
          <div class="form-group col-md-3 mb-3">
            <label class='control-label' for="select-internal-fluorotags">Select internal-placed tags</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theFluoroTagListing = new LoadInternalFluoroTag();
              if ($isEntityBeingEdited) {
                $theMarkedFluoroTags = new LoadFluoroITagsToPlasmid($theOriginalPlasmidID);
                $theMarkedFluoroTagsArray = $theMarkedFluoroTags->ReturnMarkedGeneElements();
                $theMarkedFluoroTags->PopulateHiddenArray();
                $theFluoroTagListing->buildSelectedTablesWithMultipleEntries($theMarkedFluoroTagsArray);
              } else {
                $isMultipleSelection = true;
                $theFluoroTagListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theAntibioticListing = new LoadAntibiotic();
              if ($isEntityBeingEdited) {
                $theMarkedAntibioticTags = new LoadAntibioticsToPlasmid($theOriginalPlasmidID);
                $theMarkedAntibioticArray = $theMarkedAntibioticTags->ReturnMarkedGeneElements();
                $theMarkedAntibioticTags->PopulateHiddenArray();
                $theAntibioticListing->buildSelectedTablesWithMultipleEntries($theMarkedAntibioticArray);
              } else {
                $isMultipleSelection = true;
                $theAntibioticListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>

          <div class="form-group col-md-3 mb-3">
            <?php
              if ($isEntityBeingEdited) {
                echo "<input type='text' name='plasmidLocation_htmlName' class='form-control' value=\"$theOriginalPlasmidLocation\" placeholder='plasmid location' title='plasmid_location'/>";
              } else {
                echo "<input type='text' name='plasmidLocation_htmlName' class='form-control' value='' placeholder='plasmid location' title='plasmid_location'/>";
              }
            ?>
          </div>

        </div>

        <div class="row">
          <div class="form-group col-md-8 mb-3">
            <?php
              if ($isEntityBeingEdited) {
  					    echo "<input type='text' name='comment_htmlName' class='form-control' value=\"$theOriginalComment\" placeholder='comments go here' title='comment'/>";
              } else {
                echo "<input type='text' name='comment_htmlName' class='form-control' value='' placeholder='comments go here' title='comment'/>";
              }
  				  ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-1 mb-3">
            <label for="SourceStrainSelector_ID" style="padding-top:4px">source:</label>
          </div>
          <div class="form-group col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theContributorListing = new LoadContributor();
              if ($isEntityBeingEdited) {
                $theContributorListing->buildSelectedTablesWithSingleEntry($theOriginalContributor);
              } else {
                $isMultipleSelection = false;
                $theContributorListing->buildSelectTable($isMultipleSelection);
              }
            ?>
          </div>
        </div>

      <?php
        // common sequence code for plasmids and alleles
        require_once("../sequence/sequence-form.php");
      ?>

      <div class="row">
          <div class="col-md-4 mb-3">

            <?php
              echo "<input type='hidden' name='originalPlasmidEdited' value=\"$isEntityBeingEdited\">";
              if ($isEntityBeingEdited) {
                require_once("../sequence/post-sequence.php");
                echo "<input type='hidden' name='originalPlasmidID_postVar' value=\"$theOriginalPlasmidID\">";
                echo "<input type='hidden' name='originalPlasmidName_postVar' value=\"$theOriginalPlasmidName\">";
                echo "<input type='hidden' name='originalOthercDNA_postVar' value=\"$theOriginalOthercDNA\">";
                echo "<input type='hidden' name='originalPlasmidLocation_postVar' value=\"$theOriginalPlasmidLocation\">";
                echo "<input type='hidden' name='originalComment_postvar' value=\"$theOriginalComment\">";
                echo "<input type='hidden' name='originalContributor_postvar' value=\"$theOriginalContributor\">";
                echo "<input type='hidden' name='originalPromotor_postvar' value=\"$theOriginalPromoter\">";
                echo "<input type='hidden' name='originalGene_postvar' value=\"$theOriginalGene\">";
              }
            ?>

          </div>
          <div class="col-md-2 mb-3">
            <button type="button" id="cancel" class="form-control">Cancel</button>
          </div>
          <div class="col-md-3 mb-3">
            <?php
              if ($isEntityBeingEdited) {
                echo "<input type='submit' name='acceptNewPlasmidEntry_htmlName' class='btn btn-primary btn-block' value='Accept Plasmid Edit' alt='Accept Plasmid Edit'/>";
              }
              else {
                echo "<input type='submit' name='acceptNewPlasmidEntry_htmlName' class='btn btn-primary btn-block' value='Accept Plasmid Entry' alt='Accept Plasmid Entry'/>";
              }
            ?>
        </div>
      </div>
    </form>
  </div>

  <script src="/js/sequence.js"></script>

  </body>
</html>
