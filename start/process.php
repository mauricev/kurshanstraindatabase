<?php
  include_once('../classes/session.php');
  include_once('../classes/classes_database.php');
  include_once("../classes/classes_load_elements.php");

  $searchDatabase = new Peri_Database();
  $theSelectString = "SELECT truestrain_table.strain_id FROM strain_table as truestrain_table";
  $preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);
  $preparedSQLQuery_prop->execute();
  $theStrainList = $preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC);

  $theStrainClass = new LoadParentStrains();

  foreach ($theStrainList as $theStrain) {
    $theStrainArray = $theStrainClass->returnSpecificRecord($theStrain['strain_id']);

    $theFrozenDate = $theStrainArray['dateFrozen_col'];

    $handOffDate = $theFrozenDate;
    $survivalDate = $theFrozenDate;
    $movedDate = $theFrozenDate;

    error_log($theStrainArray['strainName_col'] . " " . $theFrozenDate);

    $isolationNameNotUsed = "";
    $frozenDate = "";
    $thawedDateNotUsed = "";
    $commentNotUsed = "";
    $parentStrainsNotUsed = "";
    $allelesNotUsed = "";
    $transGenesNotUsed = "";
    $balancersNotUsed = "";
    $contributorNotUsed[0] = "";
    $frozenLocationNotUsed = "";
    $nitrogenNotUsed = "";
    $lastVialStateNotUsed = "";
    $lastVialer[0] = "";

    $strainObject = new Strain($theStrainArray['strainName_col'], $isolationNameNotUsed, $frozenDate, $thawedDateNotUsed, $commentNotUsed,$parentStrainsNotUsed,$allelesNotUsed,$transGenesNotUsed,$balancersNotUsed,$contributorNotUsed[0],$frozenLocationNotUsed,$nitrogenNotUsed,$lastVialStateNotUsed,$lastVialer[0],$handOffDate, $survivalDate, $movedDate);

    $strainObject->updateDates($theStrainArray['strain_id']); 
  }

?>
