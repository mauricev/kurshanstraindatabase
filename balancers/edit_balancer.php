<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/selectize.js"></script>
    <script src="/js/common-functions.js"></script>
    <script src="/js/balancers-javascript.js"></script>

    <script>
      $( document ).ready(function()
      {
      	cancelButton();
        setSecondChromosomeState();
        setFirstChromosomeState();
        // need to set up the disabled items for whatever is initially set
        // note that this requires on change handler and is not compatible with addeventlistener
        $("#select-chromosome1").trigger("change");
        $("#select-chromosome2").trigger("change");
      });
    </script>

  </head>
  <body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" alt="" width="72" height="72">
        <h2>KurshanLab Strain Database</h2>
        <?php
          $isBalancerBeingEdited = false;
          if (isset($_POST['balancersArray_htmlName'])) {
            $isBalancerBeingEdited = true;
            $selectedElement = $_POST['balancersArray_htmlName'];
            $balancerElementObjectToEdit = new LoadBalancer();
            $balancerElementArrayToEdit = $balancerElementObjectToEdit->returnSpecificRecord($selectedElement[0]);

            $balancerName = htmlspecialchars($balancerElementArrayToEdit['balancerName_col'],ENT_QUOTES);

            echo "<p class='lead'>Edit Balancer $balancerName</p>";
          } else {
            echo "<p class='lead'>Add New Balancer</p>";
          }
        ?>
      </div>
      <?php
        if ($isBalancerBeingEdited) {

          $theOldChromosome1 = htmlspecialchars($balancerElementArrayToEdit['chromosomeName_col'],ENT_QUOTES);
          $theOldChromosome2 = htmlspecialchars($balancerElementArrayToEdit['chromosomeName2_col'],ENT_QUOTES);

          $_POST['chromosome1Name_colName_postvar'] = $theOldChromosome1;
          $_POST['chromosome2Name_colName_postvar'] = $theOldChromosome2;

          $theOldComment = htmlspecialchars($balancerElementArrayToEdit['comments_col'],ENT_QUOTES);

          $_POST['selectedElement_postvar'] = $selectedElement[0];
        }
      ?>
      <form class="needs-validation" novalidate action="../balancers/submit_edited_balancer.php" method="post">
        <div class="row">
          <div class="col-md-3">
            <label id='letters-label' class="tinylabel" for="balancerName_postvar">balancer name</label>
          </div>

          <div class="col-md-2">
            <label id='letters-label' class="tinylabel" for="chromosome1_postvar">first chromosome</label>
          </div>
          <div class="col-md-2">
            <label id='letters-label' class="tinylabel" for="chromosome2_postvar">second (optional) chromosome</label>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 mb-3">
            <?php
              if ($isBalancerBeingEdited) {
                echo "<input name=balancerName_postvar type=text class='form-control' value=\"$balancerName\" required autofocus>";
              } else {
                echo "<input name=balancerName_postvar type=text class='form-control' value='' required autofocus>";
              }
            ?>
          </div>

          <div class="col-md-2 mb-3">

            <?php
              echo "<select id='select-chromosome1' class='form-control' name='chromosome1_postvar' required placeholder=''>";

              // we have added "" to accommodate it when the second chromosome is empty and the user had accidently selected an option for it.
              $theChromosomeArray = array("I", "II", "III", "IV", "V", "X");
              foreach ($theChromosomeArray as $theChromosome) {
                if (($isBalancerBeingEdited) && ($theChromosome == $theOldChromosome1)) {
                  echo "<option value=$theChromosome selected>$theChromosome</option>";
                } else {
                  echo "<option value=$theChromosome>$theChromosome</option>";
                }
              }
              echo "</select>";
            ?>
            <div class="invalid-feedback">
              All balancers must be assigned at least one chromosome.
            </div>
          </div>

          <div class="col-md-2 mb-3">
            <?php
              echo "<select id='select-chromosome2' class='form-control' name='chromosome2_postvar' placeholder=''>";

              $theChromosomeArray = array("","I", "II", "III", "IV", "V", "X");
              foreach ($theChromosomeArray as $theChromosome) {
                if (($isBalancerBeingEdited) && ($theChromosome == $theOldChromosome2)) {
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
              if ($isBalancerBeingEdited) {
                echo "<textarea id='strainSpecificComments_ID' class='form-control rounded-0' rows='2' name='comments_postvar' title='strainSpecificComments' placeholder='comments about this balancer' style='width:100%'>$theOldComment</textarea>";
              } else {
                echo "<textarea id='strainSpecificComments_ID' class='form-control rounded-0' rows='2' name='comments_postvar' title='strainSpecificComments' placeholder='comments about this balancer' style='width:100%'></textarea>";

              }
              ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 mb-3">
            <?php
              echo "<input type='hidden' name='originalBalancerEdited' value=\"$isBalancerBeingEdited\">";
              if ($isBalancerBeingEdited) {
                echo "<input type='hidden' name='orginalChromosome1_htmlName' value=\"$theOldChromosome1\">";
                echo "<input type='hidden' name='orginalChromosome2_htmlName' value=\"$theOldChromosome2\">";

                echo "<input type='hidden' name='originalComment_htmlName' value=\"$theOldComment\">";
                echo "<input type='hidden' name='originalBalancerName_htmlName' value=$balancerName>";

                echo "<input type='hidden' name='originalBalancerElementID_htmlName' value=$selectedElement[0]>";
              }
            ?>
          </div>
          <div class="col-md-2 mb-3">
            <button type="button" id="cancel" class="form-control"  >Cancel</button>
          </div>
          <div class="col-md-3 mb-3">
            <?php
              if ($isBalancerBeingEdited) {
                echo "<button type=submit class='btn btn-primary btn-block'>Accept Balancer Edit</button>";
              }
              else {
                echo "<button type=submit class='btn btn-primary btn-block'>Accept Balancer Entry</button>";
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
