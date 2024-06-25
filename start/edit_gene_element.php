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

    <script>
      $( document ).ready(function()
      {
        cancelButton();
      });
    </script>

  </head>
  <body class="bg-light opensans">
    <div class="container">
      <?php

        if(isset($_POST['submit_htmlName'])) {
          if (isset($_POST['whichList_htmlName'])) {
        			require_once("../classes/classes_load_elements.php");
      				$selectedElement = "";
        			switch($_POST["whichList_htmlName"]) {

                // the arrays like genesArray_htmlName come from the Load classes
                case 'genes_htmlValue':
      						if(isset($_POST['genesArray_htmlName'])){
                    require_once("../genes/edit_gene.php");
                  }
                  break;
                case 'alleles_htmlValue':
      						if(isset($_POST['allelesArray_htmlName'])){
                    require_once("../alleles/edit_allele.php");
                  }
                  break;
                case 'transgenes_htmlValue':
                  if(isset($_POST['transgeneArray_htmlName'])){
                    require_once("../transgenes/edit_transgene.php");
                  }
                  break;

                case 'strains_htmlValue':
                  if(isset($_POST['parentStrainsArray_htmlName'])){
                    require_once("../strains/edit_strain.php");
                  }
                  break;

                case 'plasmids_htmlValue':
                  if(isset($_POST['plasmidArray_htmlName'])){
                    require_once("../plasmids/edit_plasmid.php");
                  }
                  break;

                case 'contributors_htmlValue':
                  if(isset($_POST['contributorArray_htmlName'])){
                    require_once("../single-element/edit_singleElement.php");
                  }
                  break;

                case 'coinjection_markers_htmlValue':
                  if(isset($_POST['coinjectionMarkerArray_htmlName'])){
                    require_once("../single-element/edit_singleElement.php");
                  }
                  break;

                case 'antibiotics_htmlValue':
                  if(isset($_POST['antibioticArray_htmlName'])){
                    require_once("../single-element/edit_singleElement.php");
                  }
                  break;

                case 'fluorotags_btn_htmlValue':
                  if(isset($_POST['fluorotagArray_htmlName'])){
                    require_once("../single-element/edit_singleElement.php");
                  }
                  break;

                case 'promoters_btn_htmlValue':
                  if(isset($_POST['promoterArray_htmlName'])){
                    require_once("../promoters/edit_promoter.php");
                  }
                  break;

                case 'balancers_btn_htmlValue':
                  if(isset($_POST['balancersArray_htmlName'])){
                    require_once("../balancers/edit_balancer.php");
                  }
                  break;

                case 'high_value_stain_btn_htmlValue':
                  if(isset($_POST['highValueStrainArray_htmlName'])){
                    require_once("../single-element/edit_singleElement.php");
                  }
                  break;

                default:
                  echo  "<br>we shouldn't be here, whichList_htmlName ";
                  break;
              }
            }
      }
      ?>
    </div>
  </body>
</html>
