<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <title>KurshanLab Strain Database</title>
   	<meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/selectize.js"></script>
    <script src="/js/common-functions.js"></script>
    <script src="/js/bootstrap-datepicker.min.js"></script>
    <script src="/js/search-javascript.js"></script>

  	<style>
  	  body {
  	    font-family: "Open Sans";
        font-size: 12px;
  	  }
      .form-control {
        font-size: 12px;
      }

      input[type=checkbox] {
        margin-right:3px;
      }

      h5 {
        text-indent: -1em;
      }
  	</style>

    <script>
      var strainNameBtnState = "empty";

      $( document ).ready(function()
      {
        $('#select-trueStrains').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          onChange: function(value) {
            if (value != "" ) {
              strainNameBtnState = "filled";
            } else {
              strainNameBtnState = "empty";
            }
            allStrainsButtonUpdate(false);
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

        // watches dropdowns to see what's selected; if it’s two or more items, then enable the corresponding OR buttons
        $('#select-parentStrains').on("change", function(event) {
          orAndButtonSetup(event);
        });

        $('#select-contributors').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-author').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-editor').selectize({
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

        $('#select-gene').on("change", function(event) {
          orAndButtonSetup(event);
      	});

        $('#select-balancers').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-balancers').on("change", function(event) {
          orAndButtonSetup(event);
        });

        $('#select-allele').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-allele').on("change", function(event) {
          orAndButtonSetup(event);
        });

        $('#select-transgene').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-transgene').on("change", function(event) {
          orAndButtonSetup(event);
        });

        $('#select-coinjection_markers').selectize({
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

        // $('#select-plasmid').on("change", function(event) {
        //   orAndButtonSetup(event);
        // });

        $('#select-freezer').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('#select-nitrogen').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        $('.input-daterange').datepicker({
             format: "mm/dd/yyyy",
             startView: "year",
             //minViewMode: "months"
        });

        if($("#all-strains").is(":checked")) {
            allStrainsButtonUpdate(true);
        } else {
            allStrainsButtonUpdate(false);
        }

        disableORbuttons();
        cancelButton();

      });
    </script>
  </head>
  <body class="bg-light">
  	<div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
        <p class="lead">Search Strains</p>
      </div>
      <form class="form-horizontal" action="submit_strains_search.php" method="post">

        <div class='row'>
          <div class="col-md-3 mb-3">
<!-- strains -->
            <label class="input-group-addon hidden" style="padding-bottom:-5px"><input type="checkbox" class="hidden"></label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theTrueStrainListing = new LoadTrueStrains();
              $isMultiple = false;
              $theTrueStrainListing->buildSelectTable($isMultiple);
            ?>
          </div>
        </div>

        <div class='row'>
<!-- alleles and genes -->
          <div class="col-md-3 mb-3">
            <label class="input-group-addon"><input type="checkbox" id='alleleName_chkboxID' checked name="alleleName_chkbox_htmlName">OR search</label>
            <label class="input-group-addon px-2" ><input type="checkbox" id='alleleRestrict_chkboxID' name="alleleRestrict_chkboxID_chkbox_htmlName">just these alleles?</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theAlleleListing = new LoadAllele();
              $isMultiple = true;
              $theAlleleListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label class="input-group-addon" ><input type="checkbox" id='geneName_chkboxID' checked name="geneName_chkbox_htmlName" >OR search</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theGeneListing = new LoadGene();
              $isMultiple = true;
              $theGeneListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label class="input-group-addon" ><input type="checkbox" id='balancerName_chkboxID' checked name="balancerName_chkbox_htmlName" >OR search</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theBalancerListing = new LoadBalancer();
              $isMultiple = true;
              $theBalancerListing->buildSelectTable($isMultiple);
            ?>
          </div>

        </div>

        <!-- everything transgene -->
        <div class='row'>
          <div class="col-md-3 mb-3">
            <label class="input-group-addon" ><input type="checkbox" id='transgeneName_chkboxID' checked name="transgeneName_chkbox_htmlName">OR search</label>
            <label class="input-group-addon px-2" ><input type="checkbox" id='transgeneRestrict_chkboxID' name="transgeneRestrict_chkboxID_chkbox_htmlName">just these transgenes?</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theTransGeneListing = new LoadTransGene();
              $isMultiple = true;
              $theTransGeneListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label for="select-coinjection_markers" class="hidden">load coinjection marker</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theCoInjectionMarkerListing = new LoadCoInjectionMarker();
              $isMultiple = false;
              $theCoInjectionMarkerListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <!-- <label class="input-group-addon"><input type="checkbox" id='transGenePlasmidsName_chkboxID' checked name="transGenePlasmids_chkbox_htmlName" >OR search</label> -->
            <?php
              require_once("../classes/classes_load_elements.php");
              $thePlasmidListing = new LoadPlasmid();
              $isMultiple = false;
              $thePlasmidListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label for="chromosomeOnTransGenesID" class="hidden">chromosome on transgenes</label>
            <!-- <input type='text' id='chromosomeOnTransGenesID' class='form-control' placeholder='chromosome for transgenes' name='chromosomeTransGenes_htmlName'> -->
            <?php
              echo "<select id='chromosomeOnTransGenesID' class='form-control' name='chromosomeTransGenes_htmlName' placeholder='transgene-associated chromosome...'>";
              echo "<option value=''>transgene-associated chromosome...</option>";
              $theChromosomeArray = array("I", "II", "III", "IV", "V", "X");
              foreach ($theChromosomeArray as $theChromosome) {
                echo "<option value=$theChromosome>$theChromosome</option>";
              }
              echo "</select>";
            ?>
          </div>

        </div>

<!-- comments/parent strains -->
        <div class='row'>
          <div class="col-md-6 mb-3">
            <label class="input-group-addon" style="padding-right: 5px; padding-bottom: 0;"><input type="checkbox" name="strainsOnly_chkbox_htmlName">limit comments search to strain comments</label>
            <label class="input-group-addon" style="padding-right: 5px; padding-bottom: 0;"><input type="checkbox" name="commentsPhraseSearch_chkbox_htmlName">search as a phrase</label>
            <label class="input-group-addon" style="padding-right: 5px; padding-bottom: 0;"><input type="checkbox" name="commentsANDeverythingelse_chkbox_htmlName">AND with the others</label>
            <textarea id="commentID" class="form-control rounded-0" rows="2" name="comment_htmlName" title="strainSpecificComments" placeholder="comments" style="width:100%"></textarea>
          </div>

          <div class="col-md-3 mb-3">
            <label for="select-parentStrains" class="input-group-addon" style="padding-bottom:-5px"><input type="checkbox" id='parentStrainName_chkboxID' checked name="parent_chkbox_htmlName">OR search</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theParentStrainListing = new LoadParentStrains();
              $isMultiple = true;
              $theParentStrainListing->buildSelectTable($isMultiple);
            ?>
          </div>

        </div>

        <div class='row'>
<!-- everything person related -->
          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theContributorListing = new LoadContributor();
              $isMultiple = false;
              $theContributorListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <?php
              $theContributorListing = new LoadAuthors();
              $isMultiple = false;
              $theContributorListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <?php
              $theContributorListing = new LoadEditors();
              $isMultiple = false;
              $theContributorListing->buildSelectTable($isMultiple);
            ?>
          </div>
        </div>

        <div class='row'>
<!-- freezer stuff -->
          <div class="col-md-3 mb-3">
            <label for="dateFrozenID" >dates frozen</label>
            <div class="input-group input-daterange">
              <input type="text" id=dateFrozenID name='dateFrozenBeginning_htmlName' class="form-control">
              <div class="input-group-addon" style="padding-top:5px;padding-left:3px;padding-right:3px">
                to
              </div>
              <input type="text" class="form-control" name='dateFrozenEnding_htmlName'>
            </div>
          </div>

          <div class="col-md-3 mb-3">
            <label for="select-freezer" class="hidden">freezer</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theFreezerListing = new LoadFreezer();
              $isMultiple = false;
              $theFreezerListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label for="select-nitrogen" class="hidden">nitrogen</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theNitrogenListing = new LoadNitrogen();
              $isMultiple = false;
              $theNitrogenListing->buildSelectTable($isMultiple);
            ?>
          </div>

        </div>

      <div class="row">
        <div class="col-md-7 mb-3">
        </div>
        <div class="col-md-2 mb-3">
          <label class="input-group-addon hidden" style="padding-bottom:-5px"><input type="checkbox" class="hidden"></label>
          <button type="button" id="cancel" class="form-control"  >Cancel</button>
        </div>
				<div class="col-md-3 mb-3">
          <label class="input-group-addon" style="padding-bottom:-5px"><input type="checkbox" id='all-strains' name="allStrains_chkbox_htmlName">return all strains</label>
          <input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Perform Search" alt="Perform Search"/>
				</div>
			</div>
    </div>

    <script>
      $( document ).ready(function() {
        var check;
        $("#all-strains").on("click", function(){
            check = $("#all-strains").is(":checked");
            if(check) {
                allStrainsButtonUpdate(true);
            } else {
                allStrainsButtonUpdate(false);
            }
        });


      });
    </script>
  </body>
