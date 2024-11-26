<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php

      $isGeneBeingEdited = $_POST['originalGeneEdited'];
      if ($isGeneBeingEdited) {
        $theOldChromosome = $_POST['orginalChromosome_htmlName'];
        $theOldGeneName = $_POST['originalGeneLetters_htmlName'] . "-" . $_POST['originalGeneNumbers_htmlName'];
        $theOldComment = $_POST['originalComment_htmlName'];
        $theOriginalGeneID = $_POST['originalgeneElementID_htmlName'];
      }
      $theNewGeneName = $_POST['geneLetters_postvar'] . "-" . $_POST['geneNumbers_postvar'];

      $theNewChromosome = $_POST['chromosome_postvar'];
      $theNewComment = htmlspecialchars($_POST['comments_postvar'],ENT_QUOTES);

      $theCustomNameCheckbox = 0;
      if (isset($_POST['geneLetters_postvar']) && isset($_POST['geneNumbers_postvar'])) {
        $theNewGeneName = $_POST['geneLetters_postvar'] . "-" . $_POST['geneNumbers_postvar'];
      } else {
        $theNewGeneName = $_POST['alternateGeneInput'];
        $theCustomNameCheckbox = 1;
      }

      error_log("the gene name is ." . $theNewGeneName);

      require_once('../classes/classes_gene_elements.php');

      // use RealGene because we need the custom name
      $checkThisGene = new RealGene($theNewGeneName,$theNewChromosome,$theNewComment,$theCustomNameCheckbox);
      // if the names don't match, it was edited. Is this new name an already existing gene?
        if ($isGeneBeingEdited) {
          if ($theOldGeneName != $theNewGeneName){
            // name was changed
            if(!$checkThisGene->doesItAlreadyExist()){
              // the gene doesn't exist, so the new name (it has a new name!) can be saved
              $checkThisGene->updateOurEntry($theOriginalGeneID);
              header("location: ../start/start.php");
            }
          } else if ($theNewChromosome != $theOldChromosome) {
            $checkThisGene->updateOurEntry($theOriginalGeneID);
            header("location: ../start/start.php");
          } else if ($theNewComment != $theOldComment) {
              $checkThisGene->updateOurEntry($theOriginalGeneID);
              header("location: ../start/start.php");
          }
        } else {  // new gene entry
          if (!($checkThisGene->doesItAlreadyExist())) {
              $checkThisGene->insertOurEntry();
              header("location: ../start/start.php");
          }
        }
    ?>
  </body>
</html>
