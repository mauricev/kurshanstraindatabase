<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>KurshanLab Strain Database</title>

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5/js/bootstrap.min.js"></script>
    <script src="../js/common-functions.js"></script>
    <script src="../js/search-javascript.js"></script>
    <script src="../js/FileSaver.js/src/FileSaver.js"></script>

    <style>
      body {
        font-size: 12px !important;
      }
    </style>

    <script>
      $( document ).ready(function()
      {
        downloadSearchAsExcelButton();
        cancelButton();
      });
    </script>

  </head>
  <body class="bg-light">
    <div class="container-fluid">
  		<div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="72" height="72">
        <h2>KurshanLab Strain Database</h2>
        <p class="lead">Search Results</p>
      </div>
      <form action="search_strains.php" method="post">
        <div class="row">
          <div class="col-md-2 mb-3">
  				</div>
          <div class="col-md-2 mb-2">
  					<button type="button" id="cancel" class="form-control">Return Home</button>
  				</div>
          <div class="col-md-2 mb-2">
  					<button type="button" id="excelDownloadBtn" class="form-control">Export to Excel</button>
  				</div>
  				<div class="col-md-2 mb-2">
  					<input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Search Strains Again" alt="Search Strains Again"/>
  				</div>
  			</div>
      </form>
      <?php

        require_once("../classes/classes_search.php");
        require_once("../classes/classes_load_elements.php");

        $theSearchResult = searchDatabaseForStrains();

        if ($theSearchResult === false) {
          echo "You didn't specify any search parameters.";
        } else {

          require_once("../classes/classes_search_output.php");
          $theTableOutputClass = new TableOutputClass();

          // code below is divided into three parts. First part is simply echoing the table header row
          // next part is echoing the items that we’re searching for
          // third part is echoing the actual data, which is usually going to be a loop

          $theStrainClass = new LoadParentStrains();

          echo "<table class='table table-striped table-hover table-bordered'>";

// SECTION 1 first row is the heading colunn row
            echo "<tr class='table-primary'>"; // this is start of a table row; we don’t need to put this in excel; only the ending row call
              $theTableOutputClass->appendTableHeader('ID'); // could we make this class TableOutputClass and pass the type

              $theTableOutputClass->appendTableHeader('strain');

              $theTableOutputClass->appendTableHeader('genotype');

              $theTableOutputClass->appendTableHeader('isolation');

              $theTableOutputClass->appendTableHeader('strain comments');

              $theTableOutputClass->appendTableHeader('genotype comments');

              $theTableOutputClass->appendTableHeader('transgene info');

              $theTableOutputClass->appendTableHeader('parent strains');

              $theTableOutputClass->appendTableHeader('contributor');

              $theTableOutputClass->appendTableHeader('freezer');

              $theTableOutputClass->appendTableHeader('nitrogen');

              $theTableOutputClass->appendTableHeader('frozen on');

              $theTableOutputClass->appendTableHeader('thawed on');
              // when searching for comments in genes/alleles/transgenes, this field will be populated for the second header

              $theTableOutputClass->appendTableHeader('authored by');

              $theTableOutputClass->appendTableHeader('most recently edited by');

            $theTableOutputClass->appendTableRow();

            // This second header row shows what was searched for
            $theTableOutputClass->appendTableRowStartWithClass('table-primary');

// SECTION 2
// placeholder for the strain id
              $theTableOutputClass->appendTableHeader('strain id');
// strain name
              // we need to test element 0 of the array
              if ((isset($_POST['trueStrainsArray_htmlName'])) && $_POST['trueStrainsArray_htmlName'][0] != "") {
                $header = $_POST['trueStrainsArray_htmlName'][0] . "blank";
                $theTableOutputClass->appendTableHeader($header);
              } else if (isset($_POST['allStrains_chkbox_htmlName'])) {
                $theTableOutputClass->appendTableHeader('all strains');
              } else {
                $theTableOutputClass->appendTableHeader('no strain name');
              }

// genotype
// displays both alleles and genes being searched for
              $theGeneAlleleTransGeneLine = "";
              // first let’s collect the alleles
              $theAlleleString = "";
              if ( (isset($_POST['allelesArray_htmlName']) ) && ($_POST['allelesArray_htmlName'][0] != "") ) {
                $theCount = 1;
                $theArraySize = count($_POST['allelesArray_htmlName']);
                foreach($_POST['allelesArray_htmlName'] as $theAlleleID) {
                  $theAllele = new LoadAllele();
                  $theAlleleRecord = $theAllele->returnSpecificRecord($theAlleleID);

                  if ($theCount == $theArraySize) {
                    $theAlleleString = "(" . $theAlleleString . htmlspecialchars($theAlleleRecord['alleleName_col'],ENT_QUOTES) . ")";

                  } else {
                    $theAlleleString = "(" . $theAlleleString . htmlspecialchars($theAlleleRecord['alleleName_col'],ENT_QUOTES) . ")" . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }
// still section 2
              // then let’s collect the genes

              // DRY the below may be turned into a function (see also plasmid and parent strain below) DRY

              $theGeneString = "";
              if ( (isset($_POST['genesArray_htmlName']) ) && ($_POST['genesArray_htmlName'][0] != "") ) {
                $theCount = 1;
                $theArraySize = count($_POST['genesArray_htmlName']);
                foreach($_POST['genesArray_htmlName'] as $theGeneID) {
                  $theGene = new LoadGene();
                  $theGeneRecord = $theGene->returnSpecificRecord($theGeneID);
                  if ($theCount == $theArraySize) {
                    $theGeneString = $theGeneString . htmlspecialchars($theGeneRecord['geneName_col'],ENT_QUOTES);

                  } else {
                    $theGeneString = $theGeneString . htmlspecialchars($theGeneRecord['geneName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }

              $theBalancerString = "";
              if ( (isset($_POST['balancersArray_htmlName']) ) && ($_POST['balancersArray_htmlName'][0] != "") ) {
                $theCount = 1;
                $theArraySize = count($_POST['balancersArray_htmlName']);
                foreach($_POST['balancersArray_htmlName'] as $theBalancerID) {
                  $theBalancers= new LoadBalancer();
                  $theBalancerRecord = $theBalancers->returnSpecificRecord($theBalancerID);
                  if ($theCount == $theArraySize) {
                    $theBalancerString = $theBalancerString . htmlspecialchars($theBalancerRecord['balancerName_col'],ENT_QUOTES);
                  } else {
                    $theBalancerString = $theBalancerString . htmlspecialchars($theBalancerRecord['balancerName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }

              $theTransGeneString = "";
              if ( (isset($_POST['transgeneArray_htmlName']) ) && ($_POST['transgeneArray_htmlName'][0] != "") ) {
                $theCount = 1;
                $theArraySize = count($_POST['transgeneArray_htmlName']);
                foreach($_POST['transgeneArray_htmlName'] as $theTransGeneID) {
                  $theTransGenes = new LoadTransGene();
                  $theTransGeneRecord = $theTransGenes->returnSpecificRecord($theTransGeneID);
                  if ($theCount == $theArraySize) {
                    $theTransGeneString = $theTransGeneString . htmlspecialchars($theTransGeneRecord['transgeneName_col'],ENT_QUOTES);
                  } else {
                    $theTransGeneString = $theTransGeneString . htmlspecialchars($theTransGeneRecord['transgeneName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }
// still section 2

              $theGeneAlleleTransGeneString = "";
              // if there’s a gene string and an allele string, we add a semicolon because the allele is coming in the next section
              if ($theGeneString != "") {
                if ($theAlleleString != "") { // if I am going to append alleles, I need to separate them from the genes with a semicolon
                  $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . $theGeneString . "; ";
                } else {
                  $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . $theGeneString;
                }
              }
              // only append the allele string if there is a allele string; if it’s emtpy, nothing gets added
              $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . $theAlleleString;


              // if we have a gene or an allele, we need to prepend ; otherwise, we just add the transgene by itself
              if (($theGeneString != "") || ($theAlleleString != "")) {
                if ($theBalancerString != "") {
                  $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . "; " . $theBalancerString;
                }
              } else {
                // the balancer string may be empty, so then nothing gets added
                $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . $theBalancerString;
              }

              // if we have a gene or an allele, we need to prepend ; otherwise, we just add the transgene by itself
              if (($theGeneString != "") || ($theAlleleString != "") || ($theBalancerString != "")) {
                if ($theTransGeneString != "") {
                  $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . "; " . $theTransGeneString;
                }
              } else {
                // we don’t care if transgenestring is empty here because that adds nothing; it‘s just a wasted line’
                $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . $theTransGeneString;
              }

              // we will append this to the above, but we'll prefix it like so: V on transgenes
              $theTransGeneChromosomeString = "";
              if ((isset($_POST['chromosomeTransGenes_htmlName'])) && ($_POST['chromosomeTransGenes_htmlName'] != "")) {
                $theTransGeneChromosomeString = $_POST['chromosomeTransGenes_htmlName'] . " on transgenes";
              }

              // if anything has content, prepend with a ;
              if ($theTransGeneChromosomeString != "") {
                if (($theGeneString != "") || ($theAlleleString != "") || ($theTransGeneString != "") || ($theBalancerString != "") ) {
                    $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . "; " . $theTransGeneChromosomeString;
                } else {
                    $theGeneAlleleTransGeneLine = $theGeneAlleleTransGeneLine . $theTransGeneChromosomeString;
                }
              }
// still section 2
              $theTableOutputClass->appendTableHeader($theGeneAlleleTransGeneLine);
// placeholder for isolation name
              $theTableOutputClass->appendTableHeader('isolation name');
// strain comment
              if ((isset($_POST['comment_htmlName'])) && $_POST['comment_htmlName'] != "") {
                $theTableOutputClass->appendTableHeader(htmlspecialchars($_POST['comment_htmlName'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('no strain comment');
              }
//genotype comments are the same as strain comments; nothing goes here
              $theTableOutputClass->appendTableHeader('');
//transgene info
              // transgenes were already listed under genotype
              // here we list coinjection markers and plasmids

              $theCoInjectionMarkerString = "";
              // coinjection is a record of 1, so how are we searching for it as an array?
              if ( (isset($_POST['coinjectionMarkerArray_htmlName'])) && ($_POST['coinjectionMarkerArray_htmlName'][0] != NULL) ) {
                $theCoInjectionMarker = new LoadCoInjectionMarker();
                $theCoInjectionMarkerRecord = $theCoInjectionMarker->returnSpecificRecord($_POST['coinjectionMarkerArray_htmlName'][0]);
                $theCoInjectionMarkerString = $theCoInjectionMarkerString . htmlspecialchars($theCoInjectionMarkerRecord['coInjectionMarkerName_col'],ENT_QUOTES);
              }

              // what plasmids did we search for; it will be appended to the $theCoinjectionPlasmidTransGeneString, which will ultimately be the string we display
              $thePlasmidString = "";
              if ( (isset($_POST['plasmidArray_htmlName']) ) && ($_POST['plasmidArray_htmlName'][0] != "") ) {
                $theArraySize = count($_POST['plasmidArray_htmlName']);
                $theCount = 1;
                foreach($_POST['plasmidArray_htmlName'] as $thePlasmidID) {
                  $thePlasmid = new LoadPlasmid();
                  $thePlasmidRecord = $thePlasmid->returnSpecificRecord($thePlasmidID);
                  if ($theCount == $theArraySize) {
                    $thePlasmidString = $thePlasmidString .   htmlspecialchars($thePlasmidRecord['plasmidName_col'],ENT_QUOTES);
                  } else {
                    $thePlasmidString = $thePlasmidString . htmlspecialchars($thePlasmidRecord['plasmidName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }
// still section 2
              $theCoinjectionPlasmidTransGeneString = "";
              // comments don't get listed in the search header here even though we show comments for every entity with comments but strains

              // $theCoInjectionMarkerString and $thePlasmidString and they go into $theCoinjectionPlasmidTransGeneString

              if ($theCoInjectionMarkerString != "") {
                $theCoinjectionPlasmidTransGeneString = $theCoInjectionMarkerString;
              }
              if (($theCoinjectionPlasmidTransGeneString !="") &&  ($thePlasmidString != "")) {
                  $theCoinjectionPlasmidTransGeneString = $theCoinjectionPlasmidTransGeneString . "; " . $thePlasmidString;
              } else  {
                $theCoinjectionPlasmidTransGeneString = $theCoinjectionPlasmidTransGeneString . $thePlasmidString;
              }

              // this line is a list of every transgene/plasmid being searched for (plasmids are part of the transgenes)
              // i have no idea what this empty line was for
              //echo "<th>" . "</th>";

              $theTableOutputClass->appendTableHeader($theCoinjectionPlasmidTransGeneString);
// parent strains being searched for
              $theParentStrainString = "";
              if (isset($_POST['parentStrainsArray_htmlName'])) {
                $theCount = 1;
                $theArraySize = count($_POST['parentStrainsArray_htmlName']);
                foreach($_POST['parentStrainsArray_htmlName'] as $theParentStrainID) {
                  $theParentStrains = new LoadParentStrains();
                  $theParentStrainRecord = $theParentStrains->returnSpecificRecord($theParentStrainID);
                  if ($theCount == $theArraySize) {
                    $theParentStrainString = $theParentStrainString . htmlspecialchars($theParentStrainRecord['strainName_col'],ENT_QUOTES);
                  } else {
                    $theParentStrainString = $theParentStrainString . htmlspecialchars($theParentStrainRecord['strainName_col'],ENT_QUOTES) . ", ";
                  }
                  $theCount = $theCount + 1;
                }
              }
              $theTableOutputClass->appendTableHeader($theParentStrainString);
// still section 2
// contributor
              if ( (isset($_POST['contributorArray_htmlName'])) && ($_POST['contributorArray_htmlName'][0] != "")) {
                $theContributor = new LoadContributor();
                $theContributorArray = $theContributor->returnSpecificRecord($_POST['contributorArray_htmlName'][0]);
                $theTableOutputClass->appendTableHeader(htmlspecialchars($theContributorArray['contributorName_col'],ENT_QUOTES));
              } else {
                $theTableOutputClass->appendTableHeader('');
              }
// freezer
              if ( (isset($_POST['freezer_htmlName'])) && $_POST['freezer_htmlName'] != "") {
                  $theTableOutputClass->appendTableHeader($_POST['freezer_htmlName']);
                } else {
                  $theTableOutputClass->appendTableHeader('');
                }
// nitrogen
              if ((isset($_POST['nitrogen_htmlName'])) && $_POST['nitrogen_htmlName'] != "") {
                $theTableOutputClass->appendTableHeader($_POST['nitrogen_htmlName']);
              } else {
                $theTableOutputClass->appendTableHeader('');
              }
// date frozen
              if ( (isset($_POST['dateFrozenBeginning_htmlName'])) && ($_POST['dateFrozenBeginning_htmlName'] != "") && ($_POST['dateFrozenEnding_htmlName'] != "") ) {
                $header = htmlspecialchars($_POST['dateFrozenBeginning_htmlName'],ENT_QUOTES) . "–" . htmlspecialchars($_POST['dateFrozenEnding_htmlName'],ENT_QUOTES);
                $theTableOutputClass->appendTableHeader($header);
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

// date thawed is not a search term
              $theTableOutputClass->appendTableHeader('');

// author header
              if ( (isset($_POST['authorArray_htmlName'])) && ($_POST['authorArray_htmlName'][0] != "")) {
                $theAuthor = new LoadAuthors();
                $theAuthorArray = $theAuthor->returnSpecificRecord($_POST['authorArray_htmlName'][0]);
                $theTableOutputClass->appendTableHeader($theAuthorArray['authorName_col']);
              } else {
                $theTableOutputClass->appendTableHeader('');
              }

// editor header
              if ( (isset($_POST['editorArray_htmlName'])) && ($_POST['editorArray_htmlName'][0] != "")) {
                $theEditor = new LoadEditors();
                $theEditorArray = $theEditor->returnSpecificRecord($_POST['editorArray_htmlName'][0]);
                $theTableOutputClass->appendTableHeader($theEditorArray['authorName_col']);
              } else {
                $theTableOutputClass->appendTableHeader('');
              }
// still section 2
          $theTableOutputClass->appendTableRow();
// Start of SECTION 3
          // here we loop through the search results
          foreach ($theSearchResult as $theStrainID) {
            $theStrainArray = $theStrainClass->returnSpecificRecord($theStrainID['strain_id']);

            $theTableOutputClass->appendTableRow();

              $data = $theStrainArray['strain_id'];
              $theTableOutputClass->appendTableData($data);

              $data = htmlspecialchars($theStrainArray['strainName_col'],ENT_QUOTES);
            //  $theTableOutputClass->appendTableDataWithClass($data,'strain'); // what class is this?
              $theTableOutputClass->appendTableData($data); // what class is this?

// this is the genotype cell
// we display the allele and transgene for each chromosome with the chromosome number
// appended at the end and a comma separating them internally and a semicolon separating the
// chromosome groups. The last array option is "" for transgenes that aren’t associated with any chromosome
//

              $theAlleleObject = new LoadAllele();
              $theAlleleArray = $theAlleleObject->searchRelatedToStrain($theStrainID['strain_id']);

              $theTransGeneObject = new LoadTransGene();
              $theTransGeneArray = $theTransGeneObject->searchRelatedToStrain($theStrainID['strain_id']);

              $theBalancerObject = new LoadBalancer();
              $theBalancerArray = $theBalancerObject->searchRelatedToStrain($theStrainID['strain_id']);

              // the names of alleles and transgenes are extracted differently, so we use an object to handle that

              // let’s reverse how we do this
              // $theGenotypeObjectsArray wil contain each allele object and each transgene object and each balancer object, some of the balancers twice
              //
              $theGenotypeObjectsArray = array();
              foreach ($theAlleleArray as $theAllele) {
                $theGenotypeObject = new AlleleGenotypeObject($theAllele);
                $theGenotypeObject->AddToObjectArray($theGenotypeObjectsArray);
              }
              foreach ($theTransGeneArray as $theTransGene) {
                $theGenotypeObject = new TransGeneGenotypeObject($theTransGene);
                $theGenotypeObject->AddToObjectArray($theGenotypeObjectsArray);
              }
              foreach ($theBalancerArray as $theBalancer) {
                $theGenotypeObject = new BalancerGenotypeObject($theBalancer);
                $theGenotypeObject->AddToObjectArray($theGenotypeObjectsArray);
              }
// SECTION 3
              // objects that don't have a chromosome must be empty stringed
              $theChromosomeArray = array("I", "II", "III", "IV", "V", "X", "");

              // first time we loop, we get a count of how many have chromosomes we have since each set of objects tied
              // to a chromosome will end in a semicolon and we need to know where not to end in a semicolon

              // $thePreflightCountAcrossChromosomes is the number of different chromosomes there are so we can know where to put the last semicolon;
              // $theActualCountAcrossChromosomes counts the number of chromosomes we process: when it’s equal to $thePreflightCountAcrossChromosomes, we stop adding semicolons
              // $theCountPerChromosome counts the number of elements per chromosome so we can know how many commas to add

              $theDisplayGenotypeString = "";

              // loop through each object we will display and fetch its chromosome
              // now for balancers we have a HUGE problem. they may have two chromosomes, so how do know which one we get back?
              // the only I can think of is to change the interface to FetchChromosome to return an array
              // there’s another problem, we are empty stringing for objects that don’t have a chromosome, but
              // we need to change balancers to have a NULL chromosome, perhaps?
              // or we can have an option that literally says "no chromosome"
              // OK, it probably doesnt matter if doesn’t match the empty second chromosome, but what if a balancer has two real chromosomes
              // we still need to return the array and the same balancer appears twice in the results. I think this, in fact, what we want.
              // if a balancer has chromsome I and nothing in the second chromosome, we disregard the second
              // that is, we treat the first and second differently
              // we need two chromosome arrays one for checking the first and one for the checking the second
              // key is treating the second differently only if it’s not empty; we secretly knows its type as a balancer then.
              // we have a problem again, the balancer is added once to the array
              // it can’t work this way, the alternative is that we need to add the balancer twice to the array, but only if its
              // second chromosome is not "";
              $thePreflightCountAcrossChromosomes = 0;
              foreach ($theChromosomeArray as $theChromosome) {
                $foundChromosome = false;
                foreach ($theGenotypeObjectsArray as $theGenotypeObject) {
                  if ($theChromosome == $theGenotypeObject->FetchChromosome()) {
                    $foundChromosome = true;
                  } else {
                    $theSecondChromosome = $theGenotypeObject->FetchChromosome();
                    if (($theChromosome == $theSecondChromosome) && ($theChromosome != "")) {
                      $foundChromosome = true;
                    }
                  }
                }
                if ($foundChromosome) {
                  $thePreflightCountAcrossChromosomes = $thePreflightCountAcrossChromosomes + 1;
                }
              }
// SECTION 3
              $theActualCountAcrossChromosomes = 0;
              $theCount = 0;
              foreach ($theChromosomeArray as $theChromosome) {
                $theDisplayGenotypeArray = array();
                $foundChromosome = false;
                foreach ($theGenotypeObjectsArray as $theGenotypeObject) {
                  if ($theChromosome == $theGenotypeObject->FetchChromosome()) {
                    // why is this an array: we need to know when to use a comma and when to use a
                    array_push($theDisplayGenotypeArray, $theGenotypeObject);
                    $foundChromosome = true;
                  }
                }
                if ($foundChromosome == true) {
                  $theActualCountAcrossChromosomes = $theActualCountAcrossChromosomes + 1;
                }
                // $theDisplayGenotypeArray has all the objects for a given chromosome
                $theDisplayGenotypeArraySize = count($theDisplayGenotypeArray);
                $theCountPerChromosome = 1;

                $theChromosomeString = "";
                foreach ($theDisplayGenotypeArray as $theDisplayGenoTypeObject) {
                  $theGenotypeName = $theDisplayGenoTypeObject->FetchName();

                  $theChromosomeString = $theChromosomeString . $theGenotypeName;

                  // problem is that the comma is being added by the current object which is the item before balancer
                  // we know there is another one to add because of the count, but we need the next one over
                  // we use the next function on the array

                  if ($theCountPerChromosome < $theDisplayGenotypeArraySize) {
                    $theNextDisplayGenoTypeObject = next($theDisplayGenotypeArray);
                    $theChromosomeString = $theChromosomeString . $theNextDisplayGenoTypeObject->FetchChromosomeDelimeter();
                  }
                  $theCountPerChromosome = $theCountPerChromosome + 1;
                }

                if (($theChromosomeString != "") && ($theChromosome != "")) {
                  $theChromosomeString = $theChromosomeString . " " . $theChromosome;
                } else {
                  if ($theChromosomeString != "") {
                  //  $theChromosomeString = $theChromosomeString . " " . "NC given"; // NC is no chromosome
                    // we no longer append NC
                  }
                }

                if ($theActualCountAcrossChromosomes < $thePreflightCountAcrossChromosomes) {
                  if ($theChromosomeString != "") {
                    $theChromosomeString = $theChromosomeString . "; ";
                  }
                }
                $theDisplayGenotypeString = $theDisplayGenotypeString . $theChromosomeString;
              }
              //echo $theDisplayGenotypeString;

              //echo "</td>";
              $theTableOutputClass->appendTableData($theDisplayGenotypeString);
// SECTION 3, end genotype
// isolation name
              $data = htmlspecialchars($theStrainArray['isolationName_col'],ENT_QUOTES);
              $theTableOutputClass->appendTableData($data);

              $theTableOutputClass->appendTableData($theStrainArray['comments_col']);

              // we may change this: put chromosome at beginning and bold it, put in a return
              // IV:
              //echo "<td>"; // placholder for where another cell will start
// genotype comments
              // we need to search searchRelatedToStrain on alleles, but it can give us back two two sets of comments, one
              // solution may need to be we return an array of comments
              // FetchComments; we always return an array for transgenes, it's an array of one
              // for the name, we always return one name

              // the names of alleles and transgenes are extracted differently, so we use an object to handle that

              // all we need is $theGenotypeObjectsArray
              // this array contains objects for each (gene)allele/transgene

              $thePreflightCountAcrossComments = 0;
              $foundComment = false;
              foreach ($theGenotypeObjectsArray as $theCommentObject) {
                // how about $theCommentObject->FetchComment this returns 0,1, or 2 and we pass in an array to collect the comments
                if ($theCommentObject->FetchComment($theCommentArray)) {
                  $foundComment = true;
                }
                if ($foundComment) {
                  $thePreflightCountAcrossComments = $thePreflightCountAcrossComments + 1;
                }
              }
// SECTION 3
              $theCommentString = "";
              $theActualCommentCount  = 0;
              foreach ($theGenotypeObjectsArray as $theCommentObject) {
                // how about $theCommentObject->FetchComment this returns 0,1, or 2 and we pass in an array to collect the comments
                $theActualCommentCount = $theActualCommentCount + 1;
                $theCommentCount = $theCommentObject->FetchComment($theCommentArray);
                if ($theCommentCount > 0 ) {
                  $theCommentString = $theCommentString . $theCommentObject->FetchName() . ": ";
                  $theCommentString = $theCommentString . $theCommentArray[0];
                }
                if ($theCommentCount > 1 ) {
                  $theCommentString = $theCommentString . ", " . $theCommentArray[1];
                }
                if (($theCommentCount > 0 ) && ($theActualCommentCount < $thePreflightCountAcrossComments)) {
                  $theCommentString = $theCommentString . "<br>";
                }
              }

              $theTableOutputClass->appendTableData($theCommentString);

              $theTransGeneLine = "";

              // for each transgene, list coinjection marker
              // for each transgene, list plasmids not clear if I should separate these into one line or many
              // for each transgene, give transgene followed by colon and a space
              // if there's a coinjection marker give it; otherwise, display "no marker specified"
              // now loop through plasmids first we need to get a count of the array

              // problem if there's an extra-chromosomal transgene associated with the transgene; we want to list *its*
              // coinjection marker and plasmids instead. we could append: "wnyIs3 (parent Ex: tvnEx2):"

              // how do we distinguish them
              // start with transgene; check if it's extra-chromosomal (see above)
              // then we list plasmids, but
              // must know how many plasmids there are because if there are none, then ; after coinjection is WRONG

// SECTION 3
              // checks if parent is not null. if it's not it gets a count of plasmids from parent
              // otherwise it gets a count of plasmids from itself.

              $theTransGeneTotal = count($theTransGeneArray);
              $theTransGeneCount = 1;

              foreach ($theTransGeneArray as $theTransGene) {
                $theTransGeneLine = $theTransGeneLine . htmlspecialchars($theTransGene['transgeneName_col'],ENT_QUOTES);
                $theTransGeneCoinjectionMarkerObject = new LoadCoInjectionMarker();
                if ($theTransGene['parent_transgene_col'] != "") {
                  $theExtraChromosomalTransGeneObject = new LoadTransGene();
                  $theExtraChromosomalTransGene = $theExtraChromosomalTransGeneObject->returnSpecificRecord($theTransGene['parent_transgene_col']);
                  $theTransGeneLine = $theTransGeneLine . " (parent Ex is: " . htmlspecialchars($theExtraChromosomalTransGene['transgeneName_col'],ENT_QUOTES) . ")";

                  $theTransGeneCoinjectionMarkerRecord = $theTransGeneCoinjectionMarkerObject->returnSpecificRecord($theExtraChromosomalTransGene['coInjectionMarker_fk']);

                  $theMarkedPlasmidsObject = new LoadPlasmidsToTransGenes($theTransGene['parent_transgene_col']);

                } else {

                  $theTransGeneCoinjectionMarkerRecord = $theTransGeneCoinjectionMarkerObject->returnSpecificRecord($theTransGene['coInjectionMarker_fk']);

                  $theMarkedPlasmidsObject = new LoadPlasmidsToTransGenes($theTransGene['transgene_id']);
                }

                $theMarkedPlasmidsArray = $theMarkedPlasmidsObject->ReturnMarkedGeneElements();
                $thePlasmidArrayCount = count($theMarkedPlasmidsArray);

                $theTransGeneLine = $theTransGeneLine . ": ";

                $theTransGeneCoinjectionMarkerName = "";
                if ($theTransGeneCoinjectionMarkerRecord != NULL) {
                  $theTransGeneCoinjectionMarkerName = $theTransGeneCoinjectionMarkerRecord['coInjectionMarkerName_col'];
                }
                if ($theTransGeneCoinjectionMarkerName != "") {
                  $theTransGeneLine = $theTransGeneLine . $theTransGeneCoinjectionMarkerName;
                } else {
                  // no coinjection marker
                  // we display nothing here
                }
// SECTION 3
                // prepare for plasmids
                $theTransGeneLine = $theTransGeneLine . "; ";
                if ($thePlasmidArrayCount > 0) {
                  $theCount = 1;
                  foreach ($theMarkedPlasmidsArray as $thePlasmidID) {

            				$thePlasmid = new LoadPlasmid();
            				$thePlasmidArray = $thePlasmid->returnSpecificRecord($thePlasmidID);
                    $theTransGeneLine = $theTransGeneLine . $thePlasmidArray['plasmidName_col'];

                    if ($theCount < $thePlasmidArrayCount) {
                      $theTransGeneLine = $theTransGeneLine . ", ";
                    }
                    $theCount = $theCount + 1;
                  }
                }

                // so long as we are not the last transgene, add a new line for the next one
                if ($theTransGeneCount < $theTransGeneTotal) {
                  $theTransGeneLine = $theTransGeneLine . "<br>";
                }
                $theTransGeneCount = $theTransGeneCount + 1;
              }

// SECTION 3
              $theTableOutputClass->appendTableData($theTransGeneLine);

              $theParentStrainObject = new LoadParentStrains();
              $theParentStrainArray = $theParentStrainObject->searchRelatedToStrain($theStrainID['strain_id']);

              $theParentStrainLine = "";
              foreach ($theParentStrainArray as $theParentStrain) {
                if ($theParentStrain['strainName_col']  != "") {
                  $theParentStrainLine = addSemiColon($theParentStrainLine);
                  $theParentStrainLine = $theParentStrainLine . htmlspecialchars($theParentStrain['strainName_col'],ENT_QUOTES);
                }
              }

              $theTableOutputClass->appendTableData($theParentStrainLine);

              // this needs to do a lookup
              $theContributor = new LoadContributor();
              $data = "";
              if ((isset($theStrainArray['contributor_fk'])) && ($theStrainArray['contributor_fk'] != 0) && ($theStrainArray['contributor_fk'] != NULL) ) {
                $theContributorArray = $theContributor->returnSpecificRecord($theStrainArray['contributor_fk']);
                $data = htmlspecialchars($theContributorArray['contributorName_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

              $theTableOutputClass->appendTableData($theStrainArray['fullFreezer_col']);

              $theTableOutputClass->appendTableData($theStrainArray['fullNitrogen_col']);

              $theTableOutputClass->appendTableData(htmlspecialchars($theStrainArray['dateFrozen_col'],ENT_QUOTES));

              $data = "";
              if (isset($theStrainArray['dateThawed_col']) && $theStrainArray['dateThawed_col'] != "") {
                $data = htmlspecialchars($theStrainArray['dateThawed_col'],ENT_QUOTES);
              }
              $theTableOutputClass->appendTableData($data);

// SECTION 3

              // this needs to do a lookup
              $data = "";
              $theAuthor = new LoadAuthors();
              if ((isset($theStrainArray['author_fk'])) && ($theStrainArray['author_fk'] != 0) && ($theStrainArray['author_fk'] != NULL) ) {
                $theAuthorArray = $theAuthor->returnSpecificRecord($theStrainArray['author_fk']);
                $data = $theAuthorArray['authorName_col'];
              }
              $theTableOutputClass->appendTableData($data);

              // this needs to do a lookup
              $data = "";
              $theEditor = new LoadEditors();
              if ((isset($theStrainArray['editor_fk'])) && ($theStrainArray['editor_fk'] != 0) && ($theStrainArray['editor_fk'] != NULL) ) {
                $theEditorArray = $theEditor->returnSpecificRecord($theStrainArray['editor_fk']);
                $data = $theEditorArray['authorName_col'];
              }
              $theTableOutputClass->appendTableData($data);

            $theTableOutputClass->appendTableRow();
          }
          echo "</table>";

          echo "<input type='hidden' id='excelWhichSearch' value='strainSearchResults'>";
          $theFileData = $theTableOutputClass->returnTheFileData();
          echo "<input type='hidden' id='excelDownloadData' value=\"$theFileData\">";
        }
      ?>
  </body>
</html>
