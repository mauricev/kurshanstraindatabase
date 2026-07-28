<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo AppSettings::labName(); ?> Strain Database</title>

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
        <h2><?php echo AppSettings::labName(); ?> Strain Database</h2>
      </div>
      
      <?php
        require_once("../classes/classes_search.php");
        require_once("../classes/classes_load_elements.php");
        require_once("../classes/classes_search_output.php");

        $theOutputString = urldecode($_GET['output']);
        
        $theOutputStringBrokenUp = explode("¶", $theOutputString);
        $strainFields = [
          'strain id' => $theOutputStringBrokenUp[1] ?? '',
          'strain name' => $theOutputStringBrokenUp[2] ?? '',
          'genotype' => $theOutputStringBrokenUp[3] ?? '',
          'isolation name' => $theOutputStringBrokenUp[4] ?? '',
          'strain comments' => $theOutputStringBrokenUp[5] ?? '',
          'genotype comments' => $theOutputStringBrokenUp[6] ?? '',
          'transgene info' => $theOutputStringBrokenUp[7] ?? '',
          'parent strains' => $theOutputStringBrokenUp[8] ?? '',
          'contributor' => $theOutputStringBrokenUp[9] ?? '',
          'freezer location' => $theOutputStringBrokenUp[10] ?? '',
          'nitrogen location' => $theOutputStringBrokenUp[11] ?? '',
          'allele sequence file' => $theOutputStringBrokenUp[12] ?? '',
          'handed off date' => $theOutputStringBrokenUp[13] ?? '',
          'frozen on' => $theOutputStringBrokenUp[14] ?? '',
          'survived on' => $theOutputStringBrokenUp[15] ?? '',
          'moved to final dest on' => $theOutputStringBrokenUp[16] ?? '',
          'thawed on' => $theOutputStringBrokenUp[17] ?? '',
          'authored by' => $theOutputStringBrokenUp[18] ?? '',
          'edited by' => $theOutputStringBrokenUp[19] ?? '',
        ];

        echo "<table class='table table-striped table-hover table-bordered'>";
        foreach ($strainFields as $label => $value) {
          echo "<tr class='table-primary'>";
            echo "<td>" . h($label) . "</td>";
            echo "<td>" . h($value) . "</td>";
          echo "</tr>";
        }
        echo "</table>";

      ?>
  </body>
</html>
