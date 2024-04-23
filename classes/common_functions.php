
    <?php
      function whatIsTheState ($letters_param, $manufacturedWhere_param) {
        $switchedState ="";
        if ($letters_param == 'kur') {
          if ($manufacturedWhere_param == "externally-sourced") {
            $switchedState = "toExternallySourced";
           } else {
            $switchedState = "stayingOnLabProduced";
          }
        } else {
          if ($manufacturedWhere_param == "lab-produced") {
            $switchedState = "toLabProduced";
          } else {
            $switchedState = "stayingOnExternallySourced";
          }
        }
        return $switchedState;
      }

      function addSemiColon ($theLine) {
    		if ($theLine != "") {
    			$theLine = $theLine . "; ";
    		}
    		return $theLine;
    	}

      function TestArrays($nameOfOriginalArray, $arrayToTest_param) {
        $originalArray = NULL;
        if (isset($_POST[$nameOfOriginalArray])) {
          $originalArray = $_POST[$nameOfOriginalArray];
        }
        //TRUE means we edited the array
        return ($originalArray != $arrayToTest_param);
      }

    	function createStrainPlaceHolder() {
    		$name="";
    		$isolationName="";
    		$dateFrozen=""; // changed in PHP 8.2
    		$dateThawed=""; // changed in PHP 8.2
    		$comments="";
    		$setOfParentStrains=NULL;
    		$setOfAlleles=NULL;
    		$setOfTransGenes=NULL;
    		$setOfBalancers=NULL;
    		$contributorID=""; // BUG should not be zero
    		$unsavedFreezerLocation="";
    		$unsavedNitrogenLocation="";
        $theNewIsLastVialState = "";
        $selectedLastVialer = "";

        $handOffDate = null;
        $frozenDate = null;
        $survivalDate = null;
        $movedDate = null;
        $isNitrogenFreezeDateBeingSet = 0;
        $nitrogenFreezeDate = null;


    	 	return new Strain($name, $isolationName, $dateFrozen, $dateThawed, $comments,$setOfParentStrains,$setOfAlleles,$setOfTransGenes,$setOfBalancers,$contributorID,$unsavedFreezerLocation,$unsavedNitrogenLocation,$theNewIsLastVialState,$selectedLastVialer,$handOffDate, $survivalDate, $movedDate, $isNitrogenFreezeDateBeingSet, $nitrogenFreezeDate);
    	 }

      function createTransGenePlaceHolder($transGeneLocation_param) {
    		$name="";
        $theChromosome="";
        $comments="";
        $parentTransGene=NULL;
        $coinjectionMarker=NULL;
        $plasmids=NULL;
        $contributor=""; // changed in PHP 8.2
    		return new TransGene($name,$theChromosome,$comments,$transGeneLocation_param,$parentTransGene,$coinjectionMarker,$plasmids,$contributor);
    	}

      function buildTransGeneHiddenField($locationString_param,$locationField_param) {
        $theTempTransGene = createTransGenePlaceHolder($locationString_param);
        $theTempTransGene->getNextName($theGeneCurrentCount,$theNextName);
			  $theNextName = preg_replace("/kur/","",$theNextName);
        echo "<input type='hidden' id=$locationField_param name='hidden-label' value=$theNextName>";
      }
    ?>
  </body>
</html>
