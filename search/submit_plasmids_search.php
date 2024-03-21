<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>KurshanLab Strain Database</title>

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/common-functions.js"></script>
    <script src="../js/he.js"></script>
    <script src="/js/search-javascript.js"></script>
    <script src="/js/FileSaver.js/src/FileSaver.js"></script>

    <style>

      body {
        font-size: 10px !important;
      }

    </style>

    <script>
      $( document ).ready(function()
      {
        cancelButton();
        downloadSequenceButton();
        downloadSearchAsExcelButton();
      });
    </script>
  </head>
  <body class="bg-light">
    <div class="container-fluid">
  		<div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
        <p class="lead">Plasmid Search Results</p>
      </div>
       <!-- form action is to re-run the search if the user wants -->
      <form action="search_plasmids.php" method="post">
        <div class="row">
          <div class="col-md-2 mb-3">
  				</div>
          <div class="col-md-2 mb-2">
  					<button type="button" id="cancel" class="form-control">Return Home</button>
  				</div>
          <div class="col-md-2 mb-2">
  					<button type="button" id="excelDownloadBtn" class="form-control">Export to Excel</button>
  				</div>
  				<div class="col-md-3 mb-3">
  					<input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Search Plasmids Again" alt="Search Plasmids Again"/>
  				</div>
  			</div>
      </form>
      <?php

        require_once("../classes/classes_search.php");
        require_once("../classes/classes_load_elements.php");

        $theSearchResult = searchDatabaseForPlasmids();

        if ($theSearchResult === false) {
          echo "You didn't specify any search parameters.";
        } else {

          require_once("../classes/classes_search_output.php");
          $theTableOutputClass = new TableOutputClass();

          $thePlasmidClass = new LoadPlasmid();

          // first we build the table itself and then the header rows
          // these rows show what was searched for, not the actual data
          // that comes afterward
          echo "<table class='table table-striped table-hover table-bordered'>";

            // first row is the heading colunn row
            echo "<tr class='table-primary'>";
              $theTableOutputClass->appendTableHeader('plasmid');

              $theTableOutputClass->appendTableHeader('ID');

              $theTableOutputClass->appendTableHeader('cDNA');

              $theTableOutputClass->appendTableHeader('contributor');
              $theTableOutputClass->appendTableHeader('comment');

              $theTableOutputClass->appendTableHeader('location');

              $theTableOutputClass->appendTableHeader('promoter');
              $theTableOutputClass->appendTableHeader('gene');

              $theTableOutputClass->appendTableHeader('antibiotic');

              $theTableOutputClass->appendTableHeader('fluorotags');

              $theTableOutputClass->appendTableHeader('sequence data');

              $theTableOutputClass->appendTableHeader('authored by');
              $theTableOutputClass->appendTableHeader('most recently edited by');

            $theTableOutputClass->appendTableRow();

            // This second header row shows what was searched for
            $theTableOutputClass->appendTableRowStartWithClass('table-primary');
              if ((isset($_POST['plasmidArray_htmlName'])) && $_POST['plasmidArray_htmlName'] != "") {
                $theTableOutputClass->appendTableHeader($_POST['plasmidArray_htmlName'][0]);
              } else if (isset($_POST['allPlasmids_chkbox_htmlName'])) {
                $theTableOutputClass->appendTableHeader('all plasmids');
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              // placeholder for the plasmid id
              $theTableOutputClass->appendTableHeader('');

              if ((isset($_POST['cDNA_htmlName'])) && ($_POST['cDNA_htmlName'] != "")) {
                $theTableOutputClass->appendTableHeader(htmlspecialchars($_POST['cDNA_htmlName'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              if ((isset($_POST['contributorArray_htmlName'])) && ($_POST['contributorArray_htmlName'][0] != "")) {
                $theContributor = new LoadContributor();
                $theContributorArray = $theContributor->returnSpecificRecord($_POST['contributorArray_htmlName'][0]);
                $theTableOutputClass->appendTableHeader(htmlspecialchars($_POST['contributorName_col'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              if ((isset($_POST['comment_htmlName'])) && $_POST['comment_htmlName'] != "") {
                $theTableOutputClass->appendTableHeader(htmlspecialchars($_POST['comment_htmlName'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              if ((isset($_POST['plasmidLocation_htmlName'])) && ($_POST['plasmidLocation_htmlName'] != "")) {
                $theTableOutputClass->appendTableHeader(htmlspecialchars($_POST['plasmidLocation_htmlName'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              if ( (isset($_POST['promoterArray_htmlName'])) && ($_POST['promoterArray_htmlName'][0] != "")) {
                $thePromoter = new LoadPromoter();
                $thePromoterRecord = $thePromoter->returnSpecificRecord($_POST['promoterArray_htmlName'][0]);
                $header = "P" . htmlspecialchars($thePromoterRecord['geneName_col'],ENT_QUOTES);
                $theTableOutputClass->appendTableHeader($header);
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              if ( (isset($_POST['genesArray_htmlName'])) && ($_POST['genesArray_htmlName'][0] != "")) {
                $theGene = new LoadGene();
                $theGeneRecord = $theGene->returnSpecificRecord($_POST['genesArray_htmlName'][0]);
                $theTableOutputClass->appendTableHeader(htmlspecialchars($_POST['geneName_col'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              // which genes and alleles did the user search for
            //  $theFluroTagLine = "<th>";
              $theAntibioticString = "";
              if (isset($_POST['antibioticArray_htmlName'])) {
                $theCount = 1;
                $theArraySize = count($_POST['antibioticArray_htmlName']);
                foreach($_POST['antibioticArray_htmlName'] as $theAntibioticID) {
                  $theAntibiotic = new LoadAntibiotic();
                  $theAntibioticRecord = $theAntibiotic->returnSpecificRecord($theAntibioticID);
                  if ($theCount == $theArraySize) {
                    $theAntibioticString = $theAntibioticString . htmlspecialchars($theAntibioticRecord['antibioticName_col'],ENT_QUOTES);

                  } else {
                    $theAntibioticString = $theAntibioticString . htmlspecialchars($theAntibioticRecord['antibioticName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }
              $theTableOutputClass->appendTableHeader($theAntibioticString);

              // which genes and alleles did the user search for
              $theFluoroTagString = "";
              if (isset($_POST['fluorotagArray_htmlName'])) {
                $theCount = 1;
                $theArraySize = count($_POST['fluorotagArray_htmlName']);
                foreach($_POST['fluorotagArray_htmlName'] as $theFluroTagID) {
                  $theFluoroTag = new LoadFluoroTag();
                  $theFluoroTagRecord = $theFluoroTag->returnSpecificRecord($theFluroTagID);
                  if ($theCount == $theArraySize) {
                    $theFluoroTagString = $theFluoroTagString . htmlspecialchars($theFluoroTagRecord['fluoroTagName_col'],ENT_QUOTES);

                  } else {
                    $theFluoroTagString = $theFluoroTagString . htmlspecialchars($theFluoroTagRecord['fluoroTagName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }
              $theTableOutputClass->appendTableHeader($theFluoroTagString);

              // placeholder for sequence data, which has no name to look up here
              $theTableOutputClass->appendTableHeader('');

              if ( (isset($_POST['authorArray_htmlName'])) && ($_POST['authorArray_htmlName'][0] != "")) {
                $theAuthor = new LoadAuthors();
                $theAuthorArray = $theAuthor->returnSpecificRecord($_POST['authorArray_htmlName'][0]);
                echo "<th>" . $theAuthorArray['authorName_col'] . "</th>";
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

              if ( (isset($_POST['editorArray_htmlName'])) && ($_POST['editorArray_htmlName'][0] != "")) {
                $theEditor = new LoadEditors();
                $theEditorArray = $theEditor->returnSpecificRecord($_POST['editorArray_htmlName'][0]);
                $theTableOutputClass->appendTableHeader($theEditorArray['authorName_col']);
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

          $theTableOutputClass->appendTableRow();
          foreach ($theSearchResult as $thePlasmidID) {
            $thePlasmidArray = $thePlasmidClass->returnSpecificRecord($thePlasmidID['plasmid_id']);
            echo "<tr>";
              $theTableOutputClass->appendTableData(htmlspecialchars($thePlasmidArray['plasmidName_col'],ENT_QUOTES));

              $theTableOutputClass->appendTableData(htmlspecialchars($thePlasmidArray['plasmid_id'],ENT_QUOTES));

              $theTableOutputClass->appendTableData(htmlspecialchars($thePlasmidArray['other_cDNA_col'],ENT_QUOTES));

              // this needs to do a lookup
              $data = "";
              $theContributor = new LoadContributor();
              if ((isset($thePlasmidArray['contributor_fk'])) && ($thePlasmidArray['contributor_fk'] != 0) && ($thePlasmidArray['contributor_fk'] != NULL) ) {
                $theContributorArray = $theContributor->returnSpecificRecord($thePlasmidArray['contributor_fk']);
                $data = htmlspecialchars($theContributorArray['contributorName_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

              $data = "";
              if (isset($thePlasmidArray['comments_col'])) {
                $data = htmlspecialchars($thePlasmidArray['comments_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

              $data = "";
              if (isset($thePlasmidArray['plasmidLocation_col'])) {
                $data = htmlspecialchars($thePlasmidArray['plasmidLocation_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

              $data = "";
              $thePromoterObject = new LoadPromoter();
              if ((isset($thePlasmidArray['promotorGene_fk'])) && ($thePlasmidArray['promotorGene_fk'] != 0) && ($thePlasmidArray['promotorGene_fk'] != NULL) ) {
                $thePromoterArray = $thePromoterObject->returnSpecificRecord($thePlasmidArray['promotorGene_fk']);
                $data = "P" . htmlspecialchars($thePromoterArray['geneName_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

              $data = "";
              $theGeneObject = new LoadGene();
              if ((isset($thePlasmidArray['gene_fk'])) && ($thePlasmidArray['gene_fk'] != 0) && ($thePlasmidArray['gene_fk'] != NULL) ) {
                $theGeneArray = $theGeneObject->returnSpecificRecord($thePlasmidArray['gene_fk']);
                $data = htmlspecialchars($theGeneArray['geneName_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

              //echo "<td>"; placeholder

              $theAntibioticTagObject = new LoadAntibiotic();
              $theAntibioticObjectArray = $theAntibioticTagObject->searchRelatedToPlasmid($thePlasmidID['plasmid_id']);

              $theAntibioticTagLine = "";
              foreach ($theAntibioticObjectArray as $theAntibiotic) {
                if ($theAntibiotic['antibioticName_col']  != "") {
                  $theAntibioticTagLine = addSemiColon($theAntibioticTagLine);
                  $theAntibioticTagLine = $theAntibioticTagLine . htmlspecialchars($theAntibiotic['antibioticName_col'],ENT_QUOTES);
                }
              }
              $theTableOutputClass->appendTableData($theAntibioticTagLine);

              $theFluoroTagObject = new LoadFluoroTag();
              $theFluroObjectArray = $theFluoroTagObject->searchRelatedToPlasmid($thePlasmidID['plasmid_id']);

              $theFluroTagLine = "";
              foreach ($theFluroObjectArray as $theFluoroTag) {
                if ($theFluoroTag['fluoroTagName_col']  != "") {
                  $theFluroTagLine = addSemiColon($theFluroTagLine);
                  $theFluroTagLine = $theFluroTagLine . htmlspecialchars($theFluoroTag['fluoroTagName_col'],ENT_QUOTES);
                }
              }
              $theTableOutputClass->appendTableData($theFluroTagLine);

              echo "<td>";
              // we need to have a button here that can retrieve the sequence data and then load it
              // we create a hidden field with the id of the plasmid, $theHiddenID, and incorporate the sequence data to it
              // we create a button and the id of the button also gets the id of the plasmid
              // we use the javascript tool to convert this file to a blob and then download it

              // bug fix needed to check that it's not empty
              $theSequenceFileName = "";
              if (isset($thePlasmidArray['sequence_data_col']) && $thePlasmidArray['sequence_data_col'] != "") {
                if (isset($thePlasmidArray['sequenceDataName_col']) && $thePlasmidArray['sequenceDataName_col'] != "") {
                  $theSequenceFileName = htmlspecialchars($thePlasmidArray['sequenceDataName_col'],ENT_QUOTES);

                  echo "<div>\"$theSequenceFileName\" </div>";

                  $theHiddenID = "hidden-" . $thePlasmidID['plasmid_id'];
                  $theButtonID = "button-" . $thePlasmidID['plasmid_id'];
                  $theSequenceData = htmlspecialchars($thePlasmidArray['sequence_data_col'],ENT_QUOTES);
                  echo "<input type='hidden' id=$theHiddenID name=\"$theSequenceFileName\" value=\"$theSequenceData\">";
                  // class "download" is used to identify the button
                  // we use a class because there can be multiple buttons on the search results page
                  echo "<button type='button' id=$theButtonID class='btn btn-outline-info btn-sm download'>download</button>";
                }
              }
              echo "</td>";
              $theTableOutputClass->appendExportedTableData($theSequenceFileName);


              // this needs to do a lookup
              $data = "";
              $theAuthor = new LoadAuthors();
              if ((isset($thePlasmidArray['author_fk'])) && ($thePlasmidArray['author_fk'] != 0) && ($thePlasmidArray['author_fk'] != NULL) ) {
                $theAuthorArray = $theAuthor->returnSpecificRecord($thePlasmidArray['author_fk']);
                $data = $theAuthorArray['authorName_col'];
              }
              $theTableOutputClass->appendTableData($data);

              $data = "";
              // this needs to do a lookup
              $theEditor = new LoadEditors();
              if ((isset($thePlasmidArray['editor_fk'])) && ($thePlasmidArray['editor_fk'] != 0) && ($thePlasmidArray['editor_fk'] != NULL) ) {
                $theEditorArray = $theEditor->returnSpecificRecord($thePlasmidArray['editor_fk']);
                $data = $theEditorArray['authorName_col'];
              }
              $theTableOutputClass->appendTableData($data);

            $theTableOutputClass->appendTableRow();
          }
          echo "</table>";

          echo "<input type='hidden' id='excelWhichSearch' value='plasmidSearchResults'>";
          $theFileData = $theTableOutputClass->returnTheFileData();
          echo "<input type='hidden' id='excelDownloadData' value=\"$theFileData\">";
        }
      ?>
  </body>
</html>
