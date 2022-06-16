<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php

      $isBalancerBeingEdited = $_POST['originalBalancerEdited'];
      if ($isBalancerBeingEdited) {

        $theOldChromosome1 = $_POST['orginalChromosome1_htmlName'];
        $theOldChromosome2 = $_POST['orginalChromosome2_htmlName'];

        $theOldBalancerName = $_POST['originalBalancerName_htmlName'];

        $theOldComment = $_POST['originalComment_htmlName'];

        $theOriginalBalancerID = $_POST['originalBalancerElementID_htmlName'];
      }
      $theNewBalancerName = $_POST['balancerName_postvar'];

      $theNewChromosome1 = $_POST['chromosome1_postvar'];
      $theNewChromosome2 = $_POST['chromosome2_postvar'];
      $theNewComment = htmlspecialchars($_POST['comments_postvar'],ENT_QUOTES);

      //$theNewComment = $_POST['comments_postvar'];

      require_once('../classes/classes_gene_elements.php');

      $checkThisBalancer = new Balancer($theNewBalancerName,$theNewChromosome1,$theNewChromosome2,$theNewComment);
      // if the names don't match, it was edited. Is this new name an already existing gene?
        if ($isBalancerBeingEdited) {
          if ($theOldBalancerName != $theNewBalancerName){
            // name was changed
            if(!$checkThisBalancer->doesItAlreadyExist()){
              // the gene doesn't exist, so the new name (it has a new name!) can be saved
              $checkThisBalancer->updateOurEntry($theOriginalBalancerID);
              header("location: ../start/start.php");
            }
          } else if ( ($theNewChromosome1 != $theOldChromosome1) || ($theNewChromosome2 != $theOldChromosome2) ) {
            $checkThisBalancer->updateOurEntry($theOriginalBalancerID);
            header("location: ../start/start.php");
          } else if ($theNewComment != $theOldComment) {
            $checkThisBalancer->updateOurEntry($theOriginalBalancerID);
            header("location: ../start/start.php");
          }
        } else {  // new gene entry
          if (!($checkThisBalancer->doesItAlreadyExist())) {
            $checkThisBalancer->insertOurEntry();
            header("location: ../start/start.php");
          }
        }
    ?>
  </body>
</html>
