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


    <style>
      body {
        font-size: 12px !important;
      }
    </style>

  </head>
  <body class="bg-light">
    <div class="container-fluid">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
        <h2>KurshanLab Strain Database</h2>
      </div>
      
      <?php
        require_once("../classes/classes_search.php");
        require_once("../classes/classes_load_elements.php");
        require_once("../classes/classes_search_output.php");

        $theOutputString = $_GET['output'];
        
        $theOutputStringBrokenUp = explode("¶", $theOutputString);
        $strainID = $theOutputStringBrokenUp[1];
        $strainName = $theOutputStringBrokenUp[2];
        $genoType = $theOutputStringBrokenUp[3];
        $isolation = $theOutputStringBrokenUp[4];
        $strain_comments = $theOutputStringBrokenUp[5];
        $genoType_comments = $theOutputStringBrokenUp[6];
        $transgene_info = $theOutputStringBrokenUp[7];
        $parent_strains = $theOutputStringBrokenUp[8];
        $contributor = $theOutputStringBrokenUp[9];
        $freezer = $theOutputStringBrokenUp[10];
        $nitrogen = $theOutputStringBrokenUp[11];
        $allele_sequence = $theOutputStringBrokenUp[12];
        $handed = $theOutputStringBrokenUp[13];
        $frozen = $theOutputStringBrokenUp[14];
        $survived = $theOutputStringBrokenUp[15];
        $moved = $theOutputStringBrokenUp[16];
        $thawed = $theOutputStringBrokenUp[17];
        $authored = $theOutputStringBrokenUp[18];
        $edited = $theOutputStringBrokenUp[19];

        echo "<table class='table table-striped table-hover table-bordered'>";
        $theTableOutputClass = new TableOutputClass();
          echo "<tr class='table-primary'>";
            echo "<td>strain id</td>";
            echo "<td>$strainID</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>strain name</td>";
            echo "<td>$strainName</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>genotype</td>";
            echo "<td>$genoType</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>isolation name</td>";
            echo "<td>$isolation</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>strain comments</td>";
            echo "<td>$strain_comments</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>genotype comments</td>";
            echo "<td>$genoType_comments</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>transgene info</td>";
            echo "<td>$transgene_info</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>parent strains</td>";
            echo "<td>$parent_strains</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>contributor</td>";
            echo "<td>$contributor</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>freezer location</td>";
            echo "<td>$freezer</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>nitrogen location</td>";
            echo "<td>$nitrogen</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>allele sequence file</td>";
            echo "<td>$allele_sequence</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>handed off date</td>";
            echo "<td>$handed</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>frozen on</td>";
            echo "<td>$frozen</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>survived on</td>";
            echo "<td>$survived</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>moved to final dest on</td>";
            echo "<td>$moved</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>thawed on</td>";
            echo "<td>$thawed</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>authored by</td>";
            echo "<td>$authored</td>";
          echo "</tr>";
          echo "<tr class='table-primary'>";
            echo "<td>edited by</td>";
            echo "<td>$edited</td>";
          echo "</tr>";
        echo "</table>";

      ?>
  </body>
</html>
