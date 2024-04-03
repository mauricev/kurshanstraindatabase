<?php

  require_once('classes_database.php');
  require_once('../classes/logger.php');

  class NewElement extends Peri_Database {
    protected $elementKind_prop;
    protected $actualElementName_prop;
    protected $actualElementID_prop;
    protected $actualLoggingObject;

    protected $classTable_prop;
    protected $classTableName_prop;

    public function __construct($newElement_param) {
      parent::__construct();

      $this->actualElementName_prop = $newElement_param;

      $this->actualLoggingObject = new Logger();
    }

    public function doesItAlreadyExist() {

			echo "3<br>";
      // how many records have this particular gene name?
      $preparedSQLQuery = $this->sqlPrepare("SELECT COUNT(*) FROM $this->classTable_prop WHERE $this->classTableName_prop = ?");

			$theArray = array($this->actualElementName_prop);

			$preparedSQLQuery->execute($theArray);

			$existingElement = $preparedSQLQuery->fetch();

			$existingState = (!($existingElement["COUNT(*)"] == 0)); //BUG not "", but must be 0.
			if ($existingState) {
        echo "I am in the wrong place<br>";
				$this->actualLoggingObject->appendToLog("The item you just submitted, "  . $this->actualElementName_prop . ", was already in the database");
				// it may be possible if need be to get the ID of the affected item and reload it, but let's see how it plays out before doing that

        header("location: ../start/start.php");
			}
			return $existingState;
		}

    public function insertOurEntry () {
			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->classTable_prop ($this->classTableName_prop) VALUES (?)");
			$itemstoInsert = array($this->actualElementName_prop);
			$preparedSQLInsert->execute($itemstoInsert);

      $this->actualLoggingObject->appendToLog("created " . $this->elementKind_prop . ": " . $this->actualElementName_prop);
		}

    public function updateOurEntry ($existingGeneElementID_param) {
      $preparedSQLQuery = $this->sqlPrepare("UPDATE $this->classTable_prop SET $this->classTableName_prop = ? WHERE $this->actualElementID_prop = ?");
      $preparedSQLQuery->execute([$this->actualElementName_prop,$existingGeneElementID_param]);

      $this->actualLoggingObject->appendToLog("updated " . $this->elementKind_prop . ": " .  $this->actualElementName_prop);
    }
	}

  class NewContributor extends NewElement {
    protected $classOutsideContributor_prop;
    protected $actualOutsideContributor_prop;

    public function __construct($newElement_param) {
      parent::__construct($newElement_param);
      error_log("__construct");

      $this->classTable_prop = 'contributor_table';
      $this->classTableName_prop = 'contributorName_col';
      $this->actualElementID_prop = 'contributor_id';
      $this->elementKind_prop = 'contributor';
      $this->classOutsideContributor_prop = "outside_contributor_col";
    }

    public function setOutsideContributorState($contributor_State) {
      error_log("setOutsideContributorState");
      $this->actualOutsideContributor_prop = $contributor_State;
    }

    public function insertOurEntry () {
      $preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->classTable_prop ($this->classTableName_prop, $this->classOutsideContributor_prop) VALUES (?,?)");
      $itemstoInsert = array($this->actualElementName_prop,$this->actualOutsideContributor_prop);
      $preparedSQLInsert->execute($itemstoInsert);

      $this->actualLoggingObject->appendToLog("created " . $this->elementKind_prop . ": " . $this->actualElementName_prop);
    }

    public function updateOurEntry ($existingGeneElementID_param) {
      error_log("updateOurEntry");

      $preparedSQLQuery = $this->sqlPrepare("UPDATE $this->classTable_prop SET $this->classTableName_prop = ?, $this->classOutsideContributor_prop = ? WHERE $this->actualElementID_prop = ?");
      $preparedSQLQuery->execute([$this->actualElementName_prop,$this->actualOutsideContributor_prop, $existingGeneElementID_param]);

      $this->actualLoggingObject->appendToLog("updated " . $this->elementKind_prop . ": " .  $this->actualElementName_prop);
    }
  }

  class NewCoInjectionMarker extends NewElement {

    public function __construct($newElement_param) {
      parent::__construct($newElement_param);

      $this->classTable_prop = 'coinjection_marker_table';
      $this->classTableName_prop = 'coInjectionMarkerName_col';
      $this->actualElementID_prop = 'coInjectionMarker_id';
      $this->elementKind_prop = 'coinjection marker';
    }
  }

  class NewAntibiotic extends NewElement {

    public function __construct($newElement_param) {
      parent::__construct($newElement_param);

      $this->classTable_prop = 'antibiotic_table';
      $this->classTableName_prop = 'antibioticName_col';
      $this->actualElementID_prop = 'antibiotic_id';
      $this->elementKind_prop = 'antibiotic';
    }
  }

  class NewFluoro extends NewElement {

    public function __construct($newElement_param) {
      parent::__construct($newElement_param);

      $this->classTable_prop = 'fluoro_tag_table';
      $this->classTableName_prop = 'fluoroTagName_col';
      $this->actualElementID_prop = 'fluoroTag_id';
      $this->elementKind_prop = 'fluor/tag';
    }
  }






?>
