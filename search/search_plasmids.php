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
      var plasmidNameBtnState = "empty";

      $( document ).ready(function()
      {
        $('#select-plasmid').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          onChange: function(value) {
            if (value != "" ) {
              plasmidNameBtnState = "filled";
            } else {
              plasmidNameBtnState = "empty";
            }
            allPlasmidsButtonUpdate(false);
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

        $('#select-fluorotags').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

        if($("#all-plasmids").is(":checked")) {
            allPlasmidsButtonUpdate(true);
        } else {
            allPlasmidsButtonUpdate(false);
        }

        cancelButton();

      });
    </script>
  </head>
  <body class="bg-light">
  	<div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
        <p class="lead">Search Plasmids</p>
      </div>
      <form class="form-horizontal" action="submit_plasmids_search.php" method="post">

        <div class='row'>
          <div class="col-md-3 mb-3">

            <label class="input-group-addon hidden" style="padding-bottom:-5px"><input type="checkbox" class="hidden"></label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $thePlasmidListing = new LoadPlasmid();
              $thePlasmidListing->buildSelectTable(false);
            ?>
          </div>
        </div>

        <div class='row'>
          <div class="col-md-6 mb-3">
            <textarea id="commentID" class="form-control rounded-0" rows="2" name="comment_htmlName" title="plasmidComment" placeholder="comment on the plasmid" style="width:100%"></textarea>
          </div>
        </div>

        <div class='row'>

          <div class="col-md-3 mb-3">
            <div class="input-group">
              <input type="text" id=cDNAID name='cDNA_htmlName' class="form-control" placeholder="cDNA">
            </div>
          </div>

        </div>

        <div class='row'>

          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theContributorListing = new LoadContributor();
              $theContributorListing->buildSelectTable( false);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theContributorListing = new LoadAuthors();
              $theContributorListing->buildSelectTable( false);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <?php
              require_once("../classes/classes_load_elements.php");
              $theContributorListing = new LoadEditors();
              $theContributorListing->buildSelectTable( false);
            ?>
          </div>

        </div>

        <div class='row'>

          <div class="col-md-3 mb-3">
            <label for="select-promoter" class="hidden">load promoter</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $thePromoterListing = new LoadPromoter();
              $isMultiple = false;
              $thePromoterListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label for="select-gene" class="hidden">load gene</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theGeneListing = new LoadGene();
              $isMultiple = false;
              $theGeneListing->buildSelectTable($isMultiple);
            ?>
          </div>

          <div class="col-md-3 mb-3">
            <label class="input-group-addon" ><input type="checkbox" checked disabled name="antibiotic_chkbox_htmlName">OR search</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theAntibioticListing = new LoadAntibiotic();
              $isMultiple = true;
              $theAntibioticListing->buildSelectTable($isMultiple);
            ?>
          </div>

        </div>

        <div class='row'>
          <div class="col-md-3 mb-3">
            <label class="input-group-addon" ><input type="checkbox" checked disabled name="fluoroTag_chkbox_htmlName">OR search</label>
            <?php
              require_once("../classes/classes_load_elements.php");
              $theFluoroTagListing = new LoadFluoroTag();
              $isMultiple = true;
              $theFluoroTagListing->buildSelectTable($isMultiple);
            ?>
          </div>

      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
        </div>
        <div class="col-md-2 mb-3">
          <label class="input-group-addon hidden" style="padding-bottom:-5px"><input type="checkbox" class="hidden"></label>
          <button type="button" id="cancel" class="form-control">Cancel</button>
        </div>
				<div class="col-md-3 mb-3">
          <label class="input-group-addon" style="padding-bottom:-5px"><input type="checkbox" id='all-plasmids' name="allPlasmids_chkbox_htmlName">return all plasmids</label>
          <input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Perform Plasmid Search" alt="Perform PlasmidSearch"/>
				</div>
			</div>
    </div>

    <script>
      $( document ).ready(function() {
        var check;
        $("#all-plasmids").on("click", function(){
            check = $("#all-plasmids").is(":checked");
            if(check) {
                allPlasmidsButtonUpdate(true);
            } else {
                allPlasmidsButtonUpdate(false);
            }
        });


      });
    </script>
  </body>
