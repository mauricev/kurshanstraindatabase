<?php

	require_once('classes_database.php');
	require_once('../classes/logger.php');
	require_once('common_functions.php');
	require_once('../classes/classes_load_elements.php');

	class GeneticElement extends Peri_Database {
		protected $actualElementName_prop;
		protected $columnNameForChromosome_prop = "chromosomeName_col";
		protected $actualChromosomeName_prop = "";

		protected $actualComments_prop = "";
		protected $columnWithCommentName_prop = "comments_col";
		protected $actualLoggingObject;

		public function __construct($name_param,$chromosome_param,$comments_param) {
			parent::__construct();
			$this->actualElementName_prop = $name_param;
			$this->actualChromosomeName_prop = $chromosome_param;
			$this->actualComments_prop = $comments_param;

			// every element will have a logger class
			$this->actualLoggingObject = new Logger();
		}

		// if it does exist, can we echo to the screen
		// a button to go back to the original editor?
		// for each element
		// we are editing a transgene. we enter a duplicate name
		// now we send the user back to the transgene editor
		// will it have the array selection from the initial edit?
		// and will it even have the edited or original data?
		public function doesItAlreadyExist() {

				// how many records have this particular gene name?
			$preparedSQLQuery = $this->sqlPrepare("SELECT COUNT(*) FROM $this->tableName_prop WHERE $this->columnNameForElement_prop = ?");

			$theArray = array($this->actualElementName_prop);

			$preparedSQLQuery->execute($theArray);

			$existingElement = $preparedSQLQuery->fetch();

			// if ($existingElement["COUNT(*)"] == "") element doesn't exist
			$existingState = (!($existingElement["COUNT(*)"] == 0)); //BUG PHP 8 requires a number here, not ""
			if ($existingState) {
				$this->actualLoggingObject->appendToLog("The item you just submitted, "  . $this->actualElementName_prop . ", was already in the database");
				// it may be possible if need be to get the ID of the affected item and reload it, but let's see how it plays out before doing that
				header("location: ../start/start.php");
			}
			return $existingState;
		}
	}

	class Gene extends GeneticElement {
		public function __construct($name_param,$chromosome_param,$comments_param) {
			parent::__construct($name_param,$chromosome_param,$comments_param);

			$this->tableName_prop = "gene_table";
			$this->columnNameForElement_prop = "geneName_col";
		}

		protected function fillCommentString(&$comment_param) {
			if ($this->actualComments_prop != "") {
				$comment_param = "; with comment '" . $this->actualComments_prop . "'";
			} else {
				$comment_param = "; with no comment given ";
			}
		}

		protected function fillChromosomeString(&$chromosome_param) {
			if ($this->actualChromosomeName_prop != "") {
				$chromosome_param = "; on chromosome " . $this->actualChromosomeName_prop;
			} else {
				$chromosome_param = "; no chromosome given ";
			}
		}

		// in gene
		public function insertOurEntry () {
			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop,$this->columnWithCommentName_prop, chromosomeName_col) VALUES (?,?,?)");
			$itemstoInsert = array($this->actualElementName_prop,$this->actualComments_prop,$this->actualChromosomeName_prop);
			$preparedSQLInsert->execute($itemstoInsert);

			$this->fillCommentString($theComment);
			$this->fillChromosomeString($theChromosomeString);
			$this->actualLoggingObject->appendToLog("added gene: " . $this->actualElementName_prop . $theComment . $theChromosomeString);
		}

		// updates an existing entry based on passed id (the class doesn't know about ids because it's ordinarily used to create new records
		//currently only genes can be edited!
		//gene
		public function updateOurEntry ($existingGeneElementID_param) {

			$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, $this->columnNameForChromosome_prop = ? WHERE gene_id = ?");
			$preparedSQLQuery->execute([$this->actualElementName_prop,$this->actualComments_prop,$this->actualChromosomeName_prop, $existingGeneElementID_param]);

			$this->fillCommentString($theComment);
			$this->fillChromosomeString($theChromosomeString);
			$this->actualLoggingObject->appendToLog("updated gene: " . $this->actualElementName_prop . $theComment . $theChromosomeString);
		}
	}

	// not clear yet on how to derive this class
	// we are the same as a gene but 1) name is one piece and 2) we might have a second chromosome
	class Balancer extends Gene {
		protected $columnNameForChromosome2_prop = "chromosomeName2_col";
		protected $actualChromosomeName2_prop = "";

		public function __construct($name_param,$chromosome_param,$chromosome2_param, $comments_param) {
			parent::__construct($name_param,$chromosome_param,$comments_param);

			$this->actualChromosomeName2_prop = $chromosome2_param;
			$this->tableName_prop = "balancer_table";
			$this->columnNameForElement_prop = "balancerName_col";

		}

		protected function fillChromosome2String(&$chromosome_param) {
			if ($this->actualChromosomeName2_prop != "") {
				$chromosome_param = "; and on chromosome " . $this->actualChromosomeName2_prop;
			} else {
				$chromosome_param = "; no secondary chromosome given ";
			}
		}

		// in balancer
		public function insertOurEntry () {
			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop,$this->columnWithCommentName_prop, chromosomeName_col, chromosomeName2_col) VALUES (?,?,?,?)");
			$itemstoInsert = array($this->actualElementName_prop,$this->actualComments_prop,$this->actualChromosomeName_prop,$this->actualChromosomeName2_prop);
			$preparedSQLInsert->execute($itemstoInsert);

			$this->fillCommentString($theComment);
			$this->fillChromosomeString($theChromosomeString);
			$this->fillChromosome2String($theChromosome2String);
			$this->actualLoggingObject->appendToLog("added balancer: " . $this->actualElementName_prop . $theComment . $theChromosomeString . $theChromosome2String);
		}

		public function updateOurEntry ($existingGeneElementID_param) {

			$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, $this->columnNameForChromosome_prop = ? WHERE balancer_id = ?");
			$preparedSQLQuery->execute([$this->actualElementName_prop,$this->actualComments_prop,$this->actualChromosomeName_prop, $existingGeneElementID_param]);

			$this->fillCommentString($theComment);
			$this->fillChromosomeString($theChromosomeString);
			$this->fillChromosome2String($theChromosome2String);
			$this->actualLoggingObject->appendToLog("updated balancer: " . $this->actualElementName_prop . $theComment . $theChromosomeString . $theChromosome2String);
		}
	}

	// we extend it from gene so it inherits the chromosome value
	// the subgene constructor calls

	class SubGene extends Gene {
		protected $columnNameForGene_prop;
		protected $prefixForNamingSubGenes_prop;

		public function __construct($name_param, $theChromosome_param, $comments_param,$location_param) {
			// to be fair, subgene should probably not inherit here
			// rather there should probably be an intervening class and then gene should inherit from it and allele, transgene inherit from the superclass
			parent::__construct($name_param,$theChromosome_param,$comments_param);

			$this->integratedOrExString_prop = $location_param;
			$this->prefixForNamingSubGenes_prop = "";

			$preparedSQLQuery = $this->sqlPrepare("SELECT COUNT(*) FROM counter_table");
			$preparedSQLQuery->execute();
			$existingElement = $preparedSQLQuery->fetch();

			// in theory, only gets executed once per the life of the database
			// what if this should be zero and not ""?
			if($existingElement["COUNT(*)"] == 0) {

				$preparedSQLInsert = $this->sqlPrepare("INSERT INTO counter_table (alleleCounter_col, transgeneExCounter_col,transgeneIsCounter_col,transGeneSiCounter_col,strainCounter_col, freezerNumber_col, nitrogenNumber_col) VALUES (?,?,?,?,?,?,?)");
				//insert 0's into the sole record of this table; will be incremented by 1 with each new locally-produced transgene
				// freezernumber and nitrogennumber should be 1, not zero
				$preparedSQLInsert->execute([0,0,0,0,0,1,1]);
			}
		}

		// declared only here, in subgene class
		// SubGene
		public function getNewCount () {

			$preparedSQLQuery = $this->sqlPrepare("SELECT $this->columnNameForCounterTable_prop FROM counter_table WHERE counter_id = ?");
			$preparedSQLQuery->execute([1]);
			$existingCounter = $preparedSQLQuery->fetch();

			$theSubGeneCurrentCount = $existingCounter["$this->columnNameForCounterTable_prop"];

			//increment the counter for the new allele or transgene
			$theSubGeneCurrentCount = $theSubGeneCurrentCount + 1;

			return $theSubGeneCurrentCount;
		}

		public function getNextName(&$subGeneCurrentCount_param,&$theSubGeneName_param) {
			$subGeneCurrentCount_param = $this->getNewCount();
			// $this->integratedOrExString_prop will either have an empty string or an Ex or an Is or an Si. transgene class will get something passed to it.
			// we may utlimately have two clases, but a problem occurs in the strain when we want to collect them all for one list.
			// transgenes also contain ones that aren't so indexed. so do alleles
			$theSubGeneName_param = $this->prefixForNamingSubGenes_prop . $this->integratedOrExString_prop . $subGeneCurrentCount_param;
		}

		// SubGene
		public function insertEntryAfterCounterTableUpdate($theSubGeneName) {
			//abstract, must be filled out for allele and transgene subclasses
			//we pass the name here that is constructed based on counter tables
			// parent method insertOurEntryWithCounterTableUpdate handles the transaction
		}

		// SubGene
		// we handle the transaction
		public function insertOurEntryWithCounterTableUpdate() {

			$this->getNextName($theSubGeneCurrentCount,$theSubGeneName);

			try {

				$this->beginTransaction();
				// we are putting a number into $this->columnNameForCounterTable_prop: the updated number of records for this column
				$preparedSQLQuery = $this->sqlPrepare("UPDATE counter_table SET $this->columnNameForCounterTable_prop = ? WHERE counter_id = ?");
				$preparedSQLQuery->execute([$theSubGeneCurrentCount,"1"]);

				$this->insertEntryAfterCounterTableUpdate($theSubGeneName);

				$this->commit();

			}
			catch(Exception $e) {
				$this->actualLoggingObject->appendToLog($e->getMessage());
				$this->rollback();
			}
		}

	// SubGene
	// parent method insertOurEntryWithCounterTableUpdate handles the transaction
	public function updateOurEntryWithCounterTableUpdate($existingAlleleID_param) {

		$this->getNextName($theSubGeneCurrentCount,$theSubGeneName);

		try {

			$this->beginTransaction();

			// we are putting a number into $this->columnNameForCounterTable_prop: the updated number of records for this column
			$preparedSQLQuery = $this->sqlPrepare("UPDATE counter_table SET $this->columnNameForCounterTable_prop = ? WHERE counter_id = ?");
			$preparedSQLQuery->execute([$theSubGeneCurrentCount,"1"]);

			$this->updateEntryAfterCounterTableUpdate($theSubGeneName, $existingAlleleID_param);

			$this->commit();

		}
		catch(Exception $e) {
			$this->actualLoggingObject->appendToLog($e->getMessage());
			$this->rollback();
		}
	}

} // ends class for subgenes

	class TransGene extends SubGene {
		protected $actualParentTransGeneID_prop;
		protected $actualCoInjectionMarker_prop;
		protected $actualPlasmidArray_prop;

		public function __construct($name_param, $theChromosome_param, $comments_param,$location_param,$parentTransGene_param, $coinjectionMarker_param,$plasmids_param, $contributor_param) {
			parent::__construct($name_param,$theChromosome_param,$comments_param,$location_param);

			$this->tableName_prop = "transgene_table";
			$this->columnNameForElement_prop = "transgeneName_col";
			$this->prefixForNamingSubGenes_prop = "kur";

			// $this->integratedOrExString_prop was assigned in the parent class via $location_param
			$this->columnNameForCounterTable_prop = "transGeneIsCounter_col";
			if ($this->integratedOrExString_prop == "Ex") {
				$this->columnNameForCounterTable_prop = "transGeneExCounter_col";
			} else if ($this->integratedOrExString_prop == "Si") {
				$this->columnNameForCounterTable_prop = "transGeneSiCounter_col";
			}
			// NULL for everything but locally produced integrated transgenes
			$this->actualParentTransGeneID_prop = $parentTransGene_param;

			$this->actualCoInjectionMarker_prop = $coinjectionMarker_param;

			$this->actualPlasmidArray_prop = $plasmids_param;

			$this->actualContributorID_prop = $contributor_param;
		}

		//transGenes
		public function insertTransGenePlasmids($lastInsertID_param, &$plasmidsLogString_param) {

			$plasmidsLogString_param = "; and plasmids: ";
			if ($this->actualPlasmidArray_prop != NULL) {
				$theCounter = 1;
				foreach ($this->actualPlasmidArray_prop as $row) {

					$preparedSQLInsert = $this->sqlPrepare("INSERT INTO transgene_to_plasmids_table (transgene_fk,plasmid_fk) VALUES (?,?)");
					$itemstoInsert = array($lastInsertID_param,$row);
					$preparedSQLInsert->execute($itemstoInsert);

					$thePlasmidObject = new LoadPlasmid();
					$thePlasmidName = $thePlasmidObject->returnNamedSpecificRecord($row);
					if($theCounter == 1) {
						$plasmidsLogString_param = $plasmidsLogString_param . $thePlasmidName;
					} else {
						$plasmidsLogString_param = $plasmidsLogString_param . ", " . $thePlasmidName;
					}
					$theCounter = $theCounter + 1;
				}
			} else {
				$plasmidsLogString_param = $plasmidsLogString_param . "none";
			}
		}

		// TransGene
		public function insertOurEntry () {
			try {

				$this->beginTransaction();

				$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop, $this->columnWithCommentName_prop, chromosomeName_col, parent_transgene_col, coInjectionMarker_fk, contributor_fk, author_fk) VALUES (?,?,?,?,?,?,?)");

				$itemstoInsert = array($this->actualElementName_prop,$this->actualComments_prop,$this->actualChromosomeName_prop,$this->actualParentTransGeneID_prop,$this->actualCoInjectionMarker_prop,$this->actualContributorID_prop,$_SESSION['user']);

				$preparedSQLInsert->execute($itemstoInsert);

				$this->insertTransGenePlasmids($this->lastInsertId(), $plasmidsLogString);

				$this->returnLoggingForParentTransGeneCoinjectionMarker($parentTransGeneCoinjectionString);

				$this->fillCommentString($theComment);
				$this->fillChromosomeString($theChromosomeString);
				$this->actualLoggingObject->appendToLog("added transgene: " . $this->actualElementName_prop . $theComment . $theChromosomeString . $parentTransGeneCoinjectionString . $plasmidsLogString);

				$this->commit();
			}
			catch(Exception $e) {
				$this->actualLoggingObject->appendToLog($e->getMessage());
				$this->rollback();
			}
		}

		protected function returnLoggingForParentTransGeneCoinjectionMarker (&$parentTransGeneCoinjectionString_param) {

			$parentTransGeneCoinjectionString_param = "; parent transgene: ";
			$parentTransGeneObject = new LoadTransGene();
			$theParentTransGeneName = $parentTransGeneObject->returnNamedSpecificRecord($this->actualParentTransGeneID_prop);
			if ($theParentTransGeneName == "") {
				$theParentTransGeneName = "none";
			}
			$parentTransGeneCoinjectionString_param = $parentTransGeneCoinjectionString_param . $theParentTransGeneName;

			$coInjectionMarkerObject = new LoadCoInjectionMarker();
			$coInjectionMarkerName = $coInjectionMarkerObject->returnNamedSpecificRecord($this->actualCoInjectionMarker_prop);
			if ($coInjectionMarkerName == "") {
				$coInjectionMarkerName = "none";
			}
			$parentTransGeneCoinjectionString_param = $parentTransGeneCoinjectionString_param . "; coinjectionMarker: ";
			$parentTransGeneCoinjectionString_param = $parentTransGeneCoinjectionString_param . $coInjectionMarkerName;
		}

		// TransGene
		public function updateOurEntry ($existingGeneElementID_param) {
			try {
				$this->beginTransaction();

				$preparedSQLQuery = $this->sqlPrepare("DELETE FROM transgene_to_plasmids_table WHERE transgene_fk = ?");
				$preparedSQLQuery->execute([$existingGeneElementID_param]);

				$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, parent_transgene_col = ?,coInjectionMarker_fk = ?, chromosomeName_col = ?, contributor_fk = ?, editor_fk = ? WHERE transgene_id = ?");

			// $this->actualElementName_prop = actual transgene name
			// $this->actualComments_prop = actual transgene comment
			// $this->actualParentTransGeneID_prop = parent transgene
			// $existingGeneElementID_param = transgene id

				$preparedSQLQuery->execute([$this->actualElementName_prop,$this->actualComments_prop,$this->actualParentTransGeneID_prop,$this->actualCoInjectionMarker_prop,$this->actualChromosomeName_prop,$this->actualContributorID_prop,$_SESSION['user'],$existingGeneElementID_param]);

				$this->insertTransGenePlasmids($existingGeneElementID_param,$plasmidsLogString);

				$this->returnLoggingForParentTransGeneCoinjectionMarker($parentTransGeneCoinjectionString);
				$this->fillCommentString($theComment);
				$this->fillChromosomeString($theChromosomeString);
				$this->actualLoggingObject->appendToLog("updated transgene: " . $this->actualElementName_prop . $theComment . $theChromosomeString  . $parentTransGeneCoinjectionString . $plasmidsLogString);

				$this->commit();
			}
			catch(Exception $e) {
				$this->actualLoggingObject->appendToLog($e->getMessage());
				$this->rollback();
			}

		}

		// TransGene
		// this method will be abstract in the subgene class and declared in both the transgene, allele and strain classes
		// the name has been specially prepared and overrides the saved name; we pass it here as the $theSubGeneName
		public function insertEntryAfterCounterTableUpdate($theSubGeneName) {

			// I am confused, but chromosomes are entered only if the transgene is integrated and alleles don't get chromosomes
			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO transgene_table (transgeneName_col, $this->columnWithCommentName_prop, chromosomeName_col, parent_transgene_col, coInjectionMarker_fk, contributor_fk, author_fk) VALUES (?,?,?,?,?,?,?)");

			$preparedSQLInsert->execute([$theSubGeneName,$this->actualComments_prop,$this->actualChromosomeName_prop,$this->actualParentTransGeneID_prop,$this->actualCoInjectionMarker_prop,$this->actualContributorID_prop,$_SESSION['user']]);

			$this->insertTransGenePlasmids($this->lastInsertId(),$plasmidsLogString);

			$this->returnLoggingForParentTransGeneCoinjectionMarker($parentTransGeneCoinjectionString);

			$this->fillCommentString($theComment);
			$this->fillChromosomeString($theChromosomeString);
			$this->actualLoggingObject->appendToLog("added transgene: " . $theSubGeneName . $theComment . $theChromosomeString . $parentTransGeneCoinjectionString . $plasmidsLogString);
			return true;
		}

		// TransGene
		// this method updates an allele entry that has be moved from
		public function updateEntryAfterCounterTableUpdate($theSubGeneName,$existingGeneElementID_param) {

			// delete the existing plasmids
			$preparedSQLQuery = $this->sqlPrepare("DELETE FROM transgene_to_plasmids_table WHERE transgene_fk = ?");
			$preparedSQLQuery->execute([$existingGeneElementID_param]);

			// I am confused, but chromosomes are entered only if the transgene is integrated and alleles don't get chromosomes
			$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, parent_transgene_col = ?, coInjectionMarker_fk = ?, chromosomeName_col = ?, contributor_fk = ?, editor_fk = ? WHERE transgene_id = ?");

			// $this->tableName_prop = transgene_table
			// $theSubGeneName  = columnNameForElement_prop = transgene_name_col
			// $this->actualComments_prop = comments
			// $existingGeneElementID_param = transgene_id
			// don't forget to add any new plasmids

			$this->insertTransGenePlasmids($existingGeneElementID_param, $plasmidsLogString);

			$updateResult = $preparedSQLQuery->execute([$theSubGeneName,$this->actualComments_prop,$this->actualParentTransGeneID_prop,$this->actualCoInjectionMarker_prop,$this->actualChromosomeName_prop, $this->actualContributorID_prop, $_SESSION['user'], $existingGeneElementID_param]);

			$this->returnLoggingForParentTransGeneCoinjectionMarker($parentTransGeneCoinjectionString);
			$this->fillCommentString($theComment);
			$this->fillChromosomeString($theChromosomeString);
			// note that the name $theSubGeneName is used because it's internally-produced
			$this->actualLoggingObject->appendToLog("updated transgene: " . $theSubGeneName . $theComment . $theChromosomeString . $parentTransGeneCoinjectionString . $plasmidsLogString);

			return $updateResult;
		}

	}

	class Allele extends SubGene {
		protected $correspondingGene_prop;
		protected $actualSequenceDataFileName_prop;
		protected $actualSequenceData;

		public function __construct($name_param, $comments_param,$correspondingGene_param,$sequenceDataFileName_param,$selectedSequenceData) {
			parent::__construct($name_param,"",$comments_param,"");

			$this->tableName_prop = "allele_table";
			$this->columnNameForElement_prop = "alleleName_col";
			$this->prefixForNamingSubGenes_prop = "kur";

			$this->columnNameForCounterTable_prop = "alleleCounter_col";

			// apparently this column is being misused. The only time it's used to insert the gene_fk into the allele table
			$this->columnNameForGene_prop = "gene_fk";
			$this->correspondingGene_prop = $correspondingGene_param;

			$this->actualSequenceData = $selectedSequenceData;
			$this->actualSequenceDataFileName_prop = $sequenceDataFileName_param;
		}

		protected function returnLoggingForAlleleGene( &$geneForAlleleLogString_param) {
			$theGeneObject = new LoadGene();
			$geneForAlleleLogString_param = $theGeneObject->returnNamedSpecificRecord($this->correspondingGene_prop);
			if ($geneForAlleleLogString_param == "") {
				$geneForAlleleLogString_param = "; no gene specified ";
			} else {
				$geneForAlleleLogString_param = "; and gene " . $geneForAlleleLogString_param;
			}
		}

		// Allele
		// there is no common method for updateourentry because the fields are always different
		// technically, allele shouldn't be a direct descendent of subgene because its parent gene has a chromosome component and alleles do not
		public function updateOurEntry ($existingGeneElementID_param) {
			$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, $this->columnNameForGene_prop = ?, sequenceDataName_col = ?, sequence_data_col = ? WHERE allele_id = ?");

			$preparedSQLQuery->execute([$this->actualElementName_prop,$this->actualComments_prop,$this->correspondingGene_prop,$this->actualSequenceDataFileName_prop, $this->actualSequenceData,$existingGeneElementID_param]);

			$this->returnLoggingForAlleleGene($geneForAlleleLogString);
			$this->fillCommentString($theComment);
			$this->actualLoggingObject->appendToLog("updated allele: " . $this->actualElementName_prop . $theComment . $geneForAlleleLogString . "; sequenceFile: " . $this->actualSequenceDataFileName_prop);
		}

		// allele
		public function insertOurEntry () {

			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop,$this->columnWithCommentName_prop,$this->columnNameForGene_prop,sequenceDataName_col,sequence_data_col) VALUES (?,?,?,?,?)");

			$itemstoInsert = array($this->actualElementName_prop,$this->actualComments_prop,$this->correspondingGene_prop,$this->actualSequenceDataFileName_prop,$this->actualSequenceData);
			$preparedSQLInsert->execute($itemstoInsert);

			$this->returnLoggingForAlleleGene($geneForAlleleLogString);
			$this->fillCommentString($theComment);
			$this->actualLoggingObject->appendToLog("added allele: " . $this->actualElementName_prop . $theComment . $geneForAlleleLogString . "; sequenceFile: " . $this->actualSequenceDataFileName_prop);
		}

		// Allele
		// this method will be abstract in the subgene class and declared in both the transgene and allele classes
		public function insertEntryAfterCounterTableUpdate($theSubGeneName) {

			// I am confused, but chromosomes are entered only if the transgene is integrated and alleles don't get chromosomes

			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop, $this->columnWithCommentName_prop, $this->columnNameForGene_prop,sequenceDataName_col,sequence_data_col) VALUES (?,?,?,?,?)");

			$sqlInsertResult = $preparedSQLInsert->execute([$theSubGeneName,$this->actualComments_prop, $this->correspondingGene_prop,$this->actualSequenceDataFileName_prop,$this->actualSequenceData]);

			$geneForAlleleLogString  ="";

			$this->returnLoggingForAlleleGene($geneForAlleleLogString);
			$this->fillCommentString($theComment);
			$this->actualLoggingObject->appendToLog("added allele: " . $theSubGeneName . $theComment . $geneForAlleleLogString . "; sequenceFile: " . $this->actualSequenceDataFileName_prop);

			return $sqlInsertResult;
		}

		// Allele
		// this method updates an allele entry that has be moved from
		public function updateEntryAfterCounterTableUpdate($theSubGeneName,$existingGeneElementID_param) {
			// I am confused, but chromosomes are entered only if the transgene is integrated and alleles don't get chromosomes

			$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, $this->columnNameForGene_prop = ?, sequenceDataName_col = ?, sequence_data_col = ? WHERE allele_id = ?");
			$preparedSQLQuery->execute([$theSubGeneName,$this->actualComments_prop,$this->correspondingGene_prop,$this->actualSequenceDataFileName_prop,$this->actualSequenceData, $existingGeneElementID_param]);

			$this->returnLoggingForAlleleGene($geneForAlleleLogString);
			$this->fillCommentString($theComment);
			$this->actualLoggingObject->appendToLog("updated allele: " . $theSubGeneName . $theComment . $geneForAlleleLogString . "; sequenceFile: " . $this->actualSequenceDataFileName_prop);
		}
	}

	class Strain extends SubGene {
		//protected $actualSetOfParentStrains_prop;
		protected $actualSetOfParentStrains_prop;
		protected $actualSetOfAlleles_prop;
		protected $actualSetOfTransgenes_prop;
		protected $actualSetOfBalancers_prop;
		protected $isolationName_prop;
		protected $dateFrozen_prop;
		protected $dateThawed_prop;
		protected $freezerSpots_prop;
		protected $nitrogenSpots_prop;
		protected $actualFullFreezerNumber;
		protected $actualFullNitrogenNumber;
		protected $actualContributorID_prop;
		protected $actualIsLastVial_prop;
		protected $actualLastVialer_prop;

		public function __construct($name_param, $isolationName_param, $dateFrozen_param, $dateThawed_param, $comments_param,$setOfParentStrains_param,$setOfAlleles_param,$setOfTransGenes_param, $setOfBalancers_param,$contributorID_param,$unsavedFreezerLocation_param,$unsavedNitrogenLocation_param,$isLastVial_param,$lastVialer_param) {
			// pass empty string for location param. That's used to build the strain name.
			parent::__construct($name_param,"",$comments_param,"");
			$this->tableName_prop = "strain_table";
			$this->columnNameForElement_prop = "strainName_col";
			$this->prefixForNamingSubGenes_prop = "PTK";

			// $this->integratedOrExString_prop was assigned in the parent class via $location_param
			$this->columnNameForCounterTable_prop = "strainCounter_col";

			$this->columnNameForGene_prop = "strain_id";

			$this->columnIsolationName_prop = "isolationName_col";
			$this->actualIsolationName_prop = $isolationName_param;

			$this->columnDateFrozen_prop = "dateFrozen_col";
			$this->actualDateFrozen_prop = $dateFrozen_param;

			$this->columnDateThawed_prop = "dateThawed_col";
			$this->actualDateThawed_prop = $dateThawed_param;

			$this->actualSetOfParentStrains_prop = $setOfParentStrains_param;
			$this->actualSetOfAlleles_prop = $setOfAlleles_param;
			$this->actualSetOfTransgenes_prop = $setOfTransGenes_param;
			$this->actualSetOfBalancers_prop = $setOfBalancers_param;

			$theDocumentRoot = $_SERVER['DOCUMENT_ROOT'];
			$this->freezerSpots_prop = explode("\n", file_get_contents("$theDocumentRoot/freezer-nitrogen/freezer-spots.txt"));
			$this->nitrogenSpots_prop = explode("\n", file_get_contents("$theDocumentRoot/freezer-nitrogen/nitrogen-spots.txt"));

			// this array should contain only one item
			$this->actualContributorID_prop = $contributorID_param;

			$this->actualFullFreezerNumber = $unsavedFreezerLocation_param;
			$this->actualFullNitrogenNumber = $unsavedNitrogenLocation_param;
			$this->actualIsLastVial_prop = $isLastVial_param;
			$this->actualLastVialer_prop = $lastVialer_param;

		}

		public function returnFreezerNitrogenArrays(&$outFreezerArray,&$outNitrogenArray) {
			$outFreezerArray = $this->freezerSpots_prop;
			$outNitrogenArray = $this->nitrogenSpots_prop;
		}

		public function returnCurrentFreezerNitrogenNumbers(&$outCurrentFreezerFNumber_param, &$outCurrentFreezerIndex_param, &$outCurrentNitrogenNNumber_param, &$outCurrentNitrogenIndex_param) {
			//$theCurrentFreezerFNumber_param, freezerNumber_col, is F-000 number
			//$theCurrentNitrogenNNumber_param, nitrogenNumber_col, is N-000 number
			$preparedSQLQuery = $this->sqlPrepare("SELECT freezerNumber_col,freezerLetter_col,nitrogenNumber_col,nitrogenLetter_col FROM counter_table WHERE counter_id = ?");
			$preparedSQLQuery->execute([1]);
			$existingElement = $preparedSQLQuery->fetch();
			$outCurrentFreezerFNumber_param = $existingElement['freezerNumber_col'];
			$outCurrentFreezerIndex_param = $existingElement['freezerLetter_col'];
			$outCurrentNitrogenNNumber_param = $existingElement['nitrogenNumber_col'];
			$outCurrentNitrogenIndex_param = $existingElement['nitrogenLetter_col'];
		}

		public function prepareNextFreezerNitrogenNumber(&$theNewFreezerFNumber_param, &$theNewFreezerIndex_param, &$theNewNitrogenNNumber_param, &$theNewNitrogenIndex_param) {

			$this->returnCurrentFreezerNitrogenNumbers($theFreezerFNumber, $theFreezerLetters, $theNitrogenNNumber, $theNitrogenLetters);

			// on the first go around, we assume $theFreezerFNumber is null AND the others are also null
			// this may execute only one time ever or not at all if we manually enter the value in phpmyadmin
			if ($theFreezerLetters == "") {
				$theFreezerFNumber = "1";	// will be stored as F-0001 in actual strain
				$theFreezerLetters = "A1-3";
				$theNitrogenNNumber = "1";// will be stored as N-001 in actual strain
				$theNitrogenLetters = "A1";

				// we need to know what to put in the strain, but we need to get these values now so that we can failure/success of the execute function

				// PROBLEM A1-3 is number we should be using,NOT the next one over!
				$this->actualFullFreezerNumber = "F-" . sprintf("%04d", $theFreezerFNumber) . ", " . $theFreezerLetters;
				//serious bug here, had freezer number instead of nitrogen number
				$this->actualFullNitrogenNumber = "N-" . sprintf("%03d", $theNitrogenNNumber) . ", " . $theNitrogenLetters;

				$theNewFreezerFNumber_param = $theFreezerFNumber;
				$theNewFreezerIndex_param = $theFreezerLetters;
				$theNewNitrogenNNumber_param = $theNitrogenNNumber;
				$theNewNitrogenIndex_param = $theNitrogenLetters;

			} else {
				//$theFreezerLetters is the current freezer designation in the counter table
				//freezerSpots_prop is the file of possible freezer locations
				$theFreezerIndex = array_search($theFreezerLetters,$this->freezerSpots_prop, true);

				//$theNitrogenLetters is the current nitrogen designation in the counter table
				//nitrogenSpots_prop is the file of possible nitrogen locations
				$theNitrogenIndex = array_search($theNitrogenLetters,$this->nitrogenSpots_prop, true);

				// if the letters are currently I7-9, we need to up the F number and reset the index.
				// setting to zero (-1 + 1 = 0), since we always increment
				if ($this->freezerSpots_prop[$theFreezerIndex] == "I7-9") {
					$theFreezerIndex = -1;
					$theFreezerFNumber = $theFreezerFNumber + 1;
				}
				if ($this->nitrogenSpots_prop[$theNitrogenIndex] == "I9") {
					$theNitrogenIndex = -1;
					$theNitrogenNNumber = $theNitrogenNNumber + 1;
				}
				// only use that in the strain
				//$theFullFreezerFNumber = "F-" . sprintf("%03d", $theFreezerFNumber)

				// we need to know what to put in the strain, but we need to get these values now so that we can failure/success of the execute function
				// the value %03d inserts
				$this->actualFullFreezerNumber = "F-" . sprintf("%04d", $theFreezerFNumber) . ", " . $this->freezerSpots_prop[$theFreezerIndex + 1];
				$this->actualFullNitrogenNumber = "N-" . sprintf("%03d", $theNitrogenNNumber) . ", " . $this->nitrogenSpots_prop[$theNitrogenIndex + 1];

				$theNewFreezerFNumber_param = $theFreezerFNumber;
				$theNewFreezerIndex_param = $this->freezerSpots_prop[$theFreezerIndex + 1];
				$theNewNitrogenNNumber_param = $theNitrogenNNumber;
				$theNewNitrogenIndex_param = $this->nitrogenSpots_prop[$theNitrogenIndex + 1];
			}
		}

		public function returnFreezerNitrogenNumbers(&$theFreezerNumber_param,&$theNitrogenNumber_param) {
				$theFreezerNumber_param = $this->actualFullFreezerNumber;
				$theNitrogenNumber_param = $this->actualFullNitrogenNumber;
		}

		// strain
		public function updateFreezerTable() {

				$theNewFreezerFNumber_param = "";
				$theNewFreezerIndex_param = "";
				$theNewNitrogenNNumber_param = "";
				$theNewNitrogenIndex_param = "";
				$this->prepareNextFreezerNitrogenNumber($theNewFreezerFNumber_param, $theNewFreezerIndex_param, $theNewNitrogenNNumber_param, $theNewNitrogenIndex_param);

				$preparedSQLQuery = $this->sqlPrepare("UPDATE counter_table SET freezerNumber_col = ?, freezerLetter_col = ?, nitrogenNumber_col = ? , nitrogenLetter_col = ? WHERE counter_id = ?");

				return ($preparedSQLQuery->execute([$theNewFreezerFNumber_param,$theNewFreezerIndex_param,$theNewNitrogenNNumber_param,$theNewNitrogenIndex_param,"1"]));
		}

		protected function fillIsolationNameString(&$isolationName_param) {
			if ($this->actualIsolationName_prop != "") {
				$isolationName_param = "; with isolation name '" . $this->actualIsolationName_prop . "'";
			} else {
				$isolationName_param = "; with no isolation name given ";
			}
		}

		protected function fillLastVialerString(&$lastVialString_param) {
			if ($this->actualIsLastVial_prop != 0) {
				$lastVialString_param = "; this is the last vial";
			} else {
				$lastVialString_param = "";
			}
		}

		// strain
		protected function actualInsertStrainRelatedTables ($lastInsertID_param,$actualRelatedTable_param,&$allelesAndTransGenesForStrainString_param,$relatedString_param,$strain_related_table,$foreign_key_param,$loadClassName_param) {
			if ($actualRelatedTable_param != NULL) {

				$allelesAndTransGenesForStrainString_param = $allelesAndTransGenesForStrainString_param . " with " . $relatedString_param . " ";
				$theArraySize = count($actualRelatedTable_param);
				$theCount = 1;
				foreach ($actualRelatedTable_param as $row) {

					$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $strain_related_table (strain_fk,$foreign_key_param) VALUES (?,?)");
					$itemstoInsert = array($lastInsertID_param,$row);
					$preparedSQLInsert->execute($itemstoInsert);

					$theRelatedStrainObject = new $loadClassName_param;
					$theRelatedStrainName = $theRelatedStrainObject->returnNamedSpecificRecord($row);
					if($theCount == $theArraySize) {
						$allelesAndTransGenesForStrainString_param = $allelesAndTransGenesForStrainString_param . $theRelatedStrainName;
					} else {
						$allelesAndTransGenesForStrainString_param = $allelesAndTransGenesForStrainString_param . $theRelatedStrainName . ", ";
					}
					$theCount = $theCount + 1;
				}
			}
		}

		public function insertStrainAlleles($lastInsertID_param, &$allelesAndTransGenesForStrainString_param) {
			$allelesAndTransGenesForStrainString_param = "";
			$this->actualInsertStrainRelatedTables ($lastInsertID_param,$this->actualSetOfParentStrains_prop,$allelesAndTransGenesForStrainString_param,"parent strains","strain_to_parent_strain_table","parent_strain_fk","LoadParentStrains");

			$this->actualInsertStrainRelatedTables ($lastInsertID_param,$this->actualSetOfAlleles_prop,$allelesAndTransGenesForStrainString_param,"alleles","strain_to_allele_table","allele_fk","LoadAllele");

			$this->actualInsertStrainRelatedTables ($lastInsertID_param,$this->actualSetOfTransgenes_prop,$allelesAndTransGenesForStrainString_param,"transgenes","strain_to_transgene_table","transgene_fk","LoadTransGene");

			$this->actualInsertStrainRelatedTables ($lastInsertID_param,$this->actualSetOfBalancers_prop,$allelesAndTransGenesForStrainString_param,"balancers","strain_to_balancer_table","balancer_fk","LoadBalancer");

			return true;
		}

		// strain
		public function insertOurEntry () {
			try {

				$this->beginTransaction();

				$this->updateFreezerTable();

				$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop,$this->columnIsolationName_prop, $this->columnDateFrozen_prop, $this->columnDateThawed_prop, $this->columnWithCommentName_prop,fullFreezer_col,fullNitrogen_col, contributor_fk, author_fk,isLastVial_col,lastVialContributor_fk) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

				$itemstoInsert = array($this->actualElementName_prop,$this->actualIsolationName_prop, $this->actualDateFrozen_prop, $this->actualDateThawed_prop,$this->actualComments_prop,$this->actualFullFreezerNumber,$this->actualFullNitrogenNumber,$this->actualContributorID_prop, $_SESSION['user'],$this->actualIsLastVial_prop,$this->actualLastVialer_prop);
				$preparedSQLInsert->execute($itemstoInsert);

				$this->insertStrainAlleles($this->lastInsertId(), $allelesAndTransGenesForStrainString);
				$this->fillCommentString($theComment);
				$this->fillIsolationNameString($theIsolationName);
				$this->fillLastVialerString($theLastVialString);
				$theStrainLog = "added strain: " . $this->actualElementName_prop . $theIsolationName . $theComment . "'; frozen on " . $this->actualDateFrozen_prop . "; thawed on " . $this->actualDateThawed_prop . "; located at " . $this->actualFullFreezerNumber . ", " . $this->actualFullNitrogenNumber . $allelesAndTransGenesForStrainString . $theLastVialString;
				$this->actualLoggingObject->appendToLog($theStrainLog);

				$this->commit();

		}
		catch(Exception $e) {
			$this->actualLoggingObject->appendToLog($e->getMessage());
			$this->rollback();
		}
	}

	// strain
	public function insertEntryAfterCounterTableUpdate($theSubGeneName) {

		if($this->updateFreezerTable()) {
			// I am confused, but chromosomes are entered only if the transgene is integrated and alleles don't get chromosomes
			$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop, $this->columnIsolationName_prop, $this->columnDateFrozen_prop, $this->columnDateThawed_prop,$this->columnWithCommentName_prop,fullFreezer_col,fullNitrogen_col,contributor_fk, author_fk,isLastVial_col,lastVialContributor_fk) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

			$itemstoInsert = array($theSubGeneName,$this->actualIsolationName_prop, $this->actualDateFrozen_prop, $this->actualDateThawed_prop,$this->actualComments_prop,$this->actualFullFreezerNumber,$this->actualFullNitrogenNumber,$this->actualContributorID_prop,$_SESSION['user'],$this->actualIsLastVial_prop,$this->actualLastVialer_prop);
			if ($preparedSQLInsert->execute($itemstoInsert)) {

				$this->insertStrainAlleles($this->lastInsertId(), $allelesAndTransGenesForStrainString);
				$this->fillCommentString($theComment);
				$this->fillIsolationNameString($theIsolationName);
				$this->fillLastVialerString($theLastVialString);
				$theStrainLog = "added strain: " . $theSubGeneName . $theIsolationName . $theComment . "', frozen on " . $this->actualDateFrozen_prop . "; thawed on " . $this->actualDateThawed_prop . "; located at " . $this->actualFullFreezerNumber . ", " . $this->actualFullNitrogenNumber . $allelesAndTransGenesForStrainString . $theLastVialString;
				$this->actualLoggingObject->appendToLog($theStrainLog);

				return true;
			} else return 0;
		} else return 0;
	}

		// strain
		protected function deleteStrainRelated($relatedTable_param, $existingGeneElementID_param) {
			$preparedSQLQuery = $this->sqlPrepare("DELETE FROM $relatedTable_param WHERE strain_fk = ?");
			$preparedSQLQuery->execute([$existingGeneElementID_param]);
		}
		// strain
		public function updateOurEntry ($existingGeneElementID_param) {

			try {
				$this->beginTransaction();

				$this->deleteStrainRelated("strain_to_parent_strain_table", $existingGeneElementID_param);
				$this->deleteStrainRelated("strain_to_allele_table", $existingGeneElementID_param);
				$this->deleteStrainRelated("strain_to_transgene_table", $existingGeneElementID_param);
				$this->deleteStrainRelated("strain_to_balancer_table", $existingGeneElementID_param);

				$preparedSQLUpdate = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?,$this->columnIsolationName_prop = ?, $this->columnDateFrozen_prop = ?, $this->columnDateThawed_prop = ?, $this->columnWithCommentName_prop = ?, contributor_fk = ?, editor_fk = ?, isLastVial_col = ?, lastVialContributor_fk = ? WHERE strain_id = ?");

				$itemstoInsert = array($this->actualElementName_prop,$this->actualIsolationName_prop, $this->actualDateFrozen_prop, $this->actualDateThawed_prop,$this->actualComments_prop,$this->actualContributorID_prop, $_SESSION['user'],$this->actualIsLastVial_prop,$this->actualLastVialer_prop, $existingGeneElementID_param,);

				$preparedSQLUpdate->execute($itemstoInsert);

				$this->insertStrainAlleles($existingGeneElementID_param, $allelesAndTransGenesForStrainString);
				$this->fillCommentString($theComment);
				$this->fillIsolationNameString($theIsolationName);
				$this->fillLastVialerString($theLastVialString);

				$theStrainLog = "updated strain: " . $this->actualElementName_prop . $theIsolationName . $theComment . "; frozen on " . $this->actualDateFrozen_prop . "; thawed on " . $this->actualDateThawed_prop . "; located at " . $this->actualFullFreezerNumber . ", " . $this->actualFullNitrogenNumber . $allelesAndTransGenesForStrainString . $theLastVialString;
				$this->actualLoggingObject->appendToLog($theStrainLog);

				$this->commit();
			}
			catch(Exception $e) {
				$this->actualLoggingObject->appendToLog($e->getMessage());
    		$this->rollback();
			}
		}

		//strain
		public function updateEntryAfterCounterTableUpdate($theSubGeneName,$existingGeneElementID_param) {

				// blow away all the intermediate entries for this strain
				// then add the new ones (if any) back in
				$preparedSQLQuery = $this->sqlPrepare("DELETE FROM strain_to_parent_strain_table WHERE strain_fk = ?");
				$preparedSQLQuery->execute([$existingGeneElementID_param]);

				$preparedSQLQuery = $this->sqlPrepare("DELETE FROM strain_to_allele_table WHERE strain_fk = ?");
				$preparedSQLQuery->execute([$existingGeneElementID_param]);

				$preparedSQLQuery = $this->sqlPrepare("DELETE FROM strain_to_transgene_table WHERE strain_fk = ?");
				$preparedSQLQuery->execute([$existingGeneElementID_param]);

				$preparedSQLUpdate = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?,$this->columnIsolationName_prop = ?, $this->columnDateFrozen_prop = ?, $this->columnDateThawed_prop = ?, $this->columnWithCommentName_prop = ?, contributor_fk = ?, editor_fk = ?, isLastVial_col = ?, lastVialContributor_fk = ?WHERE strain_id = ?");

				$itemstoInsert = array($theSubGeneName,$this->actualIsolationName_prop, $this->actualDateFrozen_prop, $this->actualDateThawed_prop,$this->actualComments_prop,$this->actualContributorID_prop, $_SESSION['user'], $this->actualIsLastVial_prop, $this->actualLastVialer_prop, $existingGeneElementID_param);

				// $this->actualElementName_prop = actual strain name
				// $this->actualComments_prop = actual strain comment
				// $this->actualParentTransGeneID_prop = parent transgene
				// $existingGeneElementID_param = strain id
				$preparedSQLUpdate->execute($itemstoInsert);

				$this->insertStrainAlleles($existingGeneElementID_param, $allelesAndTransGenesForStrainString);

				// $this->actualElementName_prop doesn't have an entry for lab-produced strains; we used what's passed
				$theStrainLog = "updated strain: " . $theSubGeneName . " with comment " . $this->actualComments_prop . "; frozen on " . $this->actualDateFrozen_prop . "; thawed on " . $this->actualDateThawed_prop . "; located at " . $this->actualFullFreezerNumber . ", " . $this->actualFullNitrogenNumber . $allelesAndTransGenesForStrainString;
				$this->actualLoggingObject->appendToLog($theStrainLog);
			}
		}

		class Plasmid extends Gene {
			//protected $actualSetOfParentStrains_prop;
			protected $actualAntibiotics_prop;
			protected $actualLocation_prop;
			protected $actualSetOfNTags_prop;
			protected $actualSetOfCTags_prop;
			protected $actualSetOfInternalTags_prop;
			protected $actualPromoter_prop;
			protected $actualGene_prop;
			protected $actualOthercDNA_prop;
			protected $actualContributorID_prop;
			protected $actualSequenceDataFileName_prop;
			protected $actualSequenceData;

			public function __construct($name_param, $othercDNA_param, $location_param, $comments_param,$setOfAntibiotics_param,$setOfNTags_param,$setOfCTags_param,$setOfInternalTags_param, $promoter_param,$gene_param,$contributorID_param,$sequenceDataFileName_param,$selectedSequenceData) {
				// pass empty string for chromosome.
				parent::__construct($name_param,"",$comments_param);

				$this->tableName_prop = "plasmid_table";
				$this->columnNameForElement_prop = "plasmidName_col";

				$this->columnNameForGene_prop = "plasmid_id";

				$this->actualOthercDNA_prop = $othercDNA_param;

				$this->actualLocation_prop = $location_param;

				$this->actualAntibiotics_prop = $setOfAntibiotics_param;

				$this->actualPromoter_prop = $promoter_param;

				$this->actualGene_prop = $gene_param;

				$this->actualSetOfNTags_prop = $setOfNTags_param;
				$this->actualSetOfCTags_prop = $setOfCTags_param;
				$this->actualSetOfInternalTags_prop = $setOfInternalTags_param;

				// this array should contain only one item
				$this->actualContributorID_prop = $contributorID_param;

				$this->actualSequenceDataFileName_prop = $sequenceDataFileName_param;

				$this->actualSequenceData = $selectedSequenceData;

			}

			// plasmid
			protected function insertAPlasmidIntermediateTable($theActualTag_param,$lastInsertID_param,$theTagString_param,&$theTagArray_param) {
				if ($theActualTag_param != NULL) {
					foreach ($theActualTag_param as $row) {
						$preparedSQLInsert = $this->sqlPrepare("INSERT INTO plasmid_to_fluoro_tag_table (plasmid_fk,fluoro_tag_fk,n_c_internal_col) VALUES (?,?,?)");
						$itemstoInsert = array($lastInsertID_param,$row,$theTagString_param);
						$preparedSQLInsert->execute($itemstoInsert);
						$theFluoroObject = new LoadFluoroTag();
						$theFluoroTagName = $theFluoroObject->returnNamedSpecificRecord($row);
						array_push($theTagArray_param,$theFluoroTagName);
					}
				}
			}
			// plasmid
			public function insertPlasmidIntermediateTables($lastInsertID_param, &$plasmidAccessoryEntriesString_param) {

					$plasmidAccessoryEntriesString_param = "";
					if ($this->actualAntibiotics_prop != NULL) {

						$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . "; with antibiotic: ";
						$theArraySize = count($this->actualAntibiotics_prop);
						$theCount = 1;

						foreach ($this->actualAntibiotics_prop as $row) {
							$preparedSQLInsert = $this->sqlPrepare("INSERT INTO plasmid_to_antibiotic_table (plasmid_fk,antibiotic_fk) VALUES (?,?)");
							$itemstoInsert = array($lastInsertID_param,$row);
							$preparedSQLInsert->execute($itemstoInsert);

							$theAntibioticObject = new LoadAntibiotic();

							$theAntibioticName = $theAntibioticObject->returnNamedSpecificRecord($row);

							if($theCount == $theArraySize) {
								$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . $theAntibioticName;
							} else {
								$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . $theAntibioticName . ", ";
							}
							$theCount = $theCount + 1;
						}
					}
// needs to be converted to a function, subroutine, dry
					$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . "; with fluorotags: ";
					$plasmid_FluoroTagArray = array();
					$this->insertAPlasmidIntermediateTable($this->actualSetOfNTags_prop,$lastInsertID_param,'N',$plasmid_FluoroTagArray);
					$this->insertAPlasmidIntermediateTable($this->actualSetOfCTags_prop,$lastInsertID_param,'C',$plasmid_FluoroTagArray);
					$this->insertAPlasmidIntermediateTable($this->actualSetOfInternalTags_prop,$lastInsertID_param,'I',$plasmid_FluoroTagArray);

					if (count($plasmid_FluoroTagArray) == 0) {
						$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . " none";
					} else {
						$theArraySize = count($plasmid_FluoroTagArray);
						$theCount = 1;
						foreach($plasmid_FluoroTagArray as $row) {
							if($theCount == $theArraySize) {
								$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . $row;
							} else {
								$plasmidAccessoryEntriesString_param = $plasmidAccessoryEntriesString_param . $row . ", ";
							}
							$theCount = $theCount + 1;
						}
					}
			}

			//plasmid
			public function insertOurEntry () {
				try {

					$this->beginTransaction();

					$preparedSQLInsert = $this->sqlPrepare("INSERT INTO $this->tableName_prop ($this->columnNameForElement_prop,$this->columnWithCommentName_prop, contributor_fk, other_cDNA_col,plasmidLocation_col, promotorGene_fk,gene_fk,sequenceDataName_col,sequence_data_col, author_fk) VALUES (?,?,?,?,?,?,?,?,?,?)");

					$itemstoInsert = array($this->actualElementName_prop,$this->actualComments_prop,$this->actualContributorID_prop,$this->actualOthercDNA_prop,$this->actualLocation_prop,$this->actualPromoter_prop,$this->actualGene_prop, $this->actualSequenceDataFileName_prop, $this->actualSequenceData, $_SESSION['user']);
					$preparedSQLInsert->execute($itemstoInsert);

					$this->insertPlasmidIntermediateTables($this->lastInsertId(), $plasmidAccessoryEntriesString_param);

					$thePromotorGeneObject = new LoadGene();
					$thePromotorName = "P" . $thePromotorGeneObject->returnNamedSpecificRecord($this->actualPromoter_prop);
					$theGeneObject = new LoadGene();
					$theGeneName = $theGeneObject->returnNamedSpecificRecord($this->actualGene_prop);

					$this->fillCommentString($theComment);
					$this->actualLoggingObject->appendToLog("added plasmid: " . $this->actualElementName_prop . $theComment . "; cDNA: " . $this->actualOthercDNA_prop . "; location: " . $this->actualLocation_prop . "; promoter: " . $thePromotorName . "; gene: " . $theGeneName . $plasmidAccessoryEntriesString_param . "; sequenceFile: " . $this->actualSequenceDataFileName_prop);

					$this->commit();
			}
			catch(Exception $e) {
				$this->actualLoggingObject->appendToLog($e->getMessage());
				$this->rollback();
			}

			}

			// updates an existing entry based on passed id (the class doesn't know about ids because it's ordinarily used to create new records

			// in plasmid
			public function updateOurEntry ($existingGeneElementID_param) {

				try {
					$this->beginTransaction();

					$preparedSQLQuery = $this->sqlPrepare("UPDATE $this->tableName_prop SET $this->columnNameForElement_prop = ?, $this->columnWithCommentName_prop = ?, contributor_fk = ?, other_cDNA_col = ? , plasmidLocation_col = ?,promotorGene_fk = ? ,gene_fk = ?, sequenceDataName_col = ?, sequence_data_col = ?, editor_fk = ? WHERE plasmid_id = ?");
					$preparedSQLQuery->execute([$this->actualElementName_prop,$this->actualComments_prop,$this->actualContributorID_prop,$this->actualOthercDNA_prop,$this->actualLocation_prop,$this->actualPromoter_prop,$this->actualGene_prop,$this->actualSequenceDataFileName_prop, $this->actualSequenceData,  $_SESSION['user'], $existingGeneElementID_param]);

					$preparedSQLQuery = $this->sqlPrepare("DELETE FROM plasmid_to_antibiotic_table WHERE plasmid_fk = ?");
					$preparedSQLQuery->execute([$existingGeneElementID_param]);

					$preparedSQLQuery = $this->sqlPrepare("DELETE FROM plasmid_to_fluoro_tag_table WHERE plasmid_fk = ?");
					$preparedSQLQuery->execute([$existingGeneElementID_param]);

					$this->insertPlasmidIntermediateTables($existingGeneElementID_param, $plasmidAccessoryEntriesString_param);

					$thePromotorGeneObject = new LoadPromoter(); // was loadgene, bug
					$thePromotorName = "P" . $thePromotorGeneObject->returnNamedSpecificRecord($this->actualPromoter_prop);

					$theGeneObject = new LoadGene();
					$theGeneName = $theGeneObject->returnNamedSpecificRecord($this->actualGene_prop);

					$this->fillCommentString($theComment);
					$this->actualLoggingObject->appendToLog("updated plasmid: " . $this->actualElementName_prop . $theComment . "; cDNA: " . $this->actualOthercDNA_prop . "; location: " . $this->actualLocation_prop . "; promoter: " . $thePromotorName . "; gene: " . $theGeneName . $plasmidAccessoryEntriesString_param . "; sequenceFile: " . $this->actualSequenceDataFileName_prop);

					$this->commit();

				}
				catch(Exception $e) {
					$this->actualLoggingObject->appendToLog($e->getMessage());
					$this->rollback();
				}
			}
		}
?>
