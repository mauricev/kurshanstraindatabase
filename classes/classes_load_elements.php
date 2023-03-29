<?php

	require_once('classes_database.php');
	require_once('common_functions.php');
	require_once('../classes/classes_gene_elements.php');

	class LoadGeneticElement extends Peri_Database {
		public $tableName_prop;
		protected $preparedSQLQuery_prop;
		protected $selectID_prop;
		protected $selectName_prop;
		protected $placeholder_prop; // BUG placeholder_param was being used instead of this (and multiple below)
		protected $placeholder_multiple_prop;
		protected $elementNameColumn_prop;
		protected $elementIDColumn_prop;

		protected $elementHTMLName;
		protected $elementString;
		protected $elementStringLC;

		protected Strain $strainPlaceholder_prop; // added in PHP 8.2


		public function __construct() {
			parent::__construct();
		}

		// LoadGeneticElement
		public function returnAll() {
			$this->preparedSQLQuery_prop = $this->sqlPrepare("SELECT * FROM $this->tableName_prop");
			$this->preparedSQLQuery_prop->execute();
			//we summarily return the entire associative array
			return ($this->preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC));
		}

		// LoadGeneticElement
		public function returnSpecificRecord($elementToReturn_param) {
			$this->preparedSQLQuery_prop = $this->sqlPrepare("SELECT * FROM $this->tableName_prop WHERE $this->elementIDColumn_prop = ?");

			$this->preparedSQLQuery_prop->execute([$elementToReturn_param]);

			$existingElement = $this->preparedSQLQuery_prop->fetch();
			//we summarily return the entire associative array
			return ($existingElement);
		}

		public function returnNamedSpecificRecord($elementToReturn_param) {
			if (($elementToReturn_param != 0) || ($elementToReturn_param != NULL)) {
				$this->preparedSQLQuery_prop = $this->sqlPrepare("SELECT $this->elementNameColumn_prop FROM $this->tableName_prop WHERE $this->elementIDColumn_prop = ?");
				$this->preparedSQLQuery_prop->execute([$elementToReturn_param]);
				$existingElement = $this->preparedSQLQuery_prop->fetch();
				//we summarily return the entire associative array
				return ($existingElement[$this->elementNameColumn_prop]);
			} else {
				$existingElement = "";
				return $existingElement;
			}
		}

		// LoadGeneticElement
		public function buildSelectTable ($isMultiple_param) {
			$theArray = $this->returnAll();
			if ($isMultiple_param){
					echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" placeholder=\"$this->placeholder_multiple_prop\"  multiple>";
					echo "<option value=''>$this->placeholder_multiple_prop</option>";
			} else {
					echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" placeholder=\"$this->placeholder_prop\">";
					echo "<option value=''>$this->placeholder_prop</option>";
			}
			foreach($theArray as $row) {
				$name = htmlspecialchars($row[$this->elementNameColumn_prop],ENT_QUOTES);;
				$id = $row[$this->elementIDColumn_prop];
				echo "<option value=\"$id\">$name</option>";
			}
			echo "</select>";
		}

		// LoadGeneticElement
		// this method is like buildselecttable but with a pre-selected entry to select
		// wonder if we could merge this with buildselectable by simply adding a parameter
		// and do that with
		public function buildSelectedTablesWithSingleEntry ($entryToSelect_param) {
			$theEntireArray = $this->returnAll();

		 	echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" single placeholder=\"$this->placeholder_prop\">";

			// NULL entry means nothing was selected; the line below prevents the default first value from showing
			if ($entryToSelect_param == NULL) {
			echo "<option value=''>\"$this->placeholder_prop\"</option>";
			}
			sort($theEntireArray);
			foreach ($theEntireArray as $theEntireArrayItem)
			{
				$name = htmlspecialchars($theEntireArrayItem[$this->elementNameColumn_prop],ENT_QUOTES);
				$id = $theEntireArrayItem[$this->elementIDColumn_prop];
				if ($entryToSelect_param == $id) {
					echo "<option value=\"$id\" selected='selected'>$name</option>";
				} else {
					echo "<option value=\"$id\">$name</option>";
				}
			}
			 echo "</select>";
		}

		// LoadGeneticElement
		public function buildSelectedTablesWithMultipleEntries($arrayToSelect_param) {
			$theEntireArray = $this->returnAll();
			echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\"  class='selectized' placeholder=\"$this->placeholder_multiple_prop\" multiple>";
			// speed up our searches
			sort($theEntireArray);
			sort($arrayToSelect_param);
			foreach ($theEntireArray as $theEntireArrayItem)
			{
				$name = htmlspecialchars($theEntireArrayItem[$this->elementNameColumn_prop],ENT_QUOTES);
				$id = $theEntireArrayItem[$this->elementIDColumn_prop];
				$selected = false;
				foreach ($arrayToSelect_param as $theMarkedArrayItem)
				{
					if ($theMarkedArrayItem == $id) {
						$selected = true;
						break;
					}
				}
				if ($selected) {
					echo "<option value=\"$id\" selected>$name</option>";
				} else {
					echo "<option value=\"$id\">$name</option>";
				}
			}
			echo "</select>";
		}

		public function searchRelatedToStrain($strainToSearchFor_param) {
			//abstract method
		}

		public function searchRelatedToPlasmid($plasmidToSearchFor_param) {
			//abstract method
		}

		public function returnElementString() {
			return $this->elementString;
		}

		public function returnElementLC() {
				return $this->elementStringLC;
		}

		public function returnHTMLName(){
			return $this->elementHTMLName;
		}

		public function returnElementNameColumn() {
			return $this->elementNameColumn_prop;
		}
}

	class LoadNitrogen extends LoadGeneticElement {
		public function __construct() {
			$this->selectID_prop = "select-nitrogen";
			$this->selectName_prop = "nitrogenArray_htmlName[]";
			$this->placeholder_prop = "nitrogen location...";
			$this->strainPlaceholder_prop = createStrainPlaceHolder();
		}

		public function buildSelectTable ($isMultiple_param) {
			$this->strainPlaceholder_prop->returnCurrentFreezerNitrogenNumbers($theMaxFrozenNumber,$theMaxFrozenLetters, $theMaxNitrogenNumber, $theMaxNitrogenLetters);
			$theMaxFullNitrogenNumber = "N-" . sprintf("%03d", $theMaxNitrogenNumber) . ", " . $theMaxNitrogenLetters;
			$this->strainPlaceholder_prop->returnFreezerNitrogenArrays($theFreezerArray,$theNitrogenArray);

			echo "<select id='$this->selectID_prop' name='$this->selectName_prop' placeholder='$this->placeholder_prop'>";
			echo "<option value=''>$this->placeholder_prop</option>";
			for ($theNitrogenIndex = 1; $theNitrogenIndex <= $theMaxNitrogenNumber; $theNitrogenIndex++) {
				foreach ($theNitrogenArray as $theNitrogenEntry) {
					$theFullNitrogenLocation = "N-" . sprintf("%03d", $theNitrogenIndex) . ", " . $theNitrogenEntry;
					if ($theFullNitrogenLocation == $theMaxFullNitrogenNumber) {
						break;
					}
					echo "<option value=\"$theFullNitrogenLocation\">$theFullNitrogenLocation</option>";
				}
			}
			echo "</select>";
		}
	}

	class LoadFreezer extends LoadGeneticElement {
		public function __construct() {
			$this->selectID_prop = "select-freezer";
			$this->selectName_prop = "freezerArray_htmlName[]";
			$this->placeholder_prop = "freezer location...";
			require_once('../classes/classes_gene_elements.php');
			$this->strainPlaceholder_prop = createStrainPlaceHolder();
		}

		public function buildSelectTable ($isMultiple_param) {
			$this->strainPlaceholder_prop->returnCurrentFreezerNitrogenNumbers($theMaxFrozenNumber,$theMaxFrozenLetters, $theMaxNitrogenNumber, $theMaxNitrogenLetters);
			$theMaxFullFreezerNumber = "F-" . sprintf("%04d", $theMaxFrozenNumber) . ", " . $theMaxFrozenLetters;
			$this->strainPlaceholder_prop->returnFreezerNitrogenArrays($theFreezerArray,$theNitrogenArray);
			echo "<select id='$this->selectID_prop' name='$this->selectName_prop' placeholder='$this->placeholder_prop'>";
			echo "<option value=''>$this->placeholder_prop</option>";
			for ($theFreezerIndex = 1; $theFreezerIndex <= $theMaxFrozenNumber; $theFreezerIndex++) {
				foreach ($theFreezerArray as $theFreezerEntry) {
					$theFullFreezerLocation = "F-" . sprintf("%04d", $theFreezerIndex) . ", " . $theFreezerEntry;
					//echo "theFullFreezerLocation ".$theFullFreezerLocation."<br>";
					echo "<option value=\"$theFullFreezerLocation\">$theFullFreezerLocation</option>";
					// break after we've entered the last value above
					if ($theFullFreezerLocation == $theMaxFullFreezerNumber) {
						break;
					}
				}
			}
			echo "</select>";
		}
	}

	class LoadContributor extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "contributor_table";
			$this->selectID_prop = "select-contributors";
			$this->selectName_prop = "contributorArray_htmlName[]";
			$this->placeholder_prop = "Select a contributor...";
			$this->placeholder_multiple_prop = "Select contributors...";
			$this->elementNameColumn_prop = "contributorName_col";
			$this->elementIDColumn_prop = "contributor_id";
			$this->elementHTMLName = 'newContributor_htmlName';
			$this->elementString = "Contributor";
			$this->elementStringLC = "contributor";
		}
	}

	class LoadAuthors extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "author_table";
			$this->selectID_prop = "select-author";
			$this->selectName_prop = "authorArray_htmlName[]";
			$this->placeholder_prop = "Select an author...";
			$this->placeholder_multiple_prop = "";
			$this->elementNameColumn_prop = "authorName_col";
			$this->elementIDColumn_prop = "author_id";
			$this->elementHTMLName = ""; //used?
			$this->elementString = "";
			$this->elementStringLC = "";
		}
	}

	class LoadEditors extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "author_table";
			$this->selectID_prop = "select-editor";
			$this->selectName_prop = "editorArray_htmlName[]";
			$this->placeholder_prop = "Select an editor...";
			$this->placeholder_multiple_prop = "";
			$this->elementNameColumn_prop = "authorName_col";
			$this->elementIDColumn_prop = "author_id";
			$this->elementHTMLName = ""; //used?
			$this->elementString = "";
			$this->elementStringLC = "";
		}
	}

	class LoadLastVialers extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "contributor_table";
			$this->selectID_prop = "select-lastvialers";
			$this->selectName_prop = "lastVialerArray_htmlName[]";
			$this->placeholder_prop = "Select who last vialed...";
			$this->placeholder_multiple_prop = "";
			$this->elementNameColumn_prop = "contributorName_col";
			$this->elementIDColumn_prop = "contributor_id";
			$this->elementHTMLName = ""; //used?
			$this->elementString = "";
			$this->elementStringLC = "";
		}

		// loadlastvialers
		public function buildSelectedTablesWithSingleEntry ($entryToSelect_param) {
			$theEntireArray = $this->returnAll();

		 	echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" required single placeholder=\"$this->placeholder_prop\">";

			// NULL entry means nothing was selected; the line below prevents the default first value from showing
			if ($entryToSelect_param == NULL) {
			echo "<option value=''>\"$this->placeholder_prop\"</option>";
			}
			sort($theEntireArray);
			foreach ($theEntireArray as $theEntireArrayItem)
			{
				$name = htmlspecialchars($theEntireArrayItem[$this->elementNameColumn_prop],ENT_QUOTES);
				$id = $theEntireArrayItem[$this->elementIDColumn_prop];
				if ($entryToSelect_param == $id) {
					echo "<option value=\"$id\" selected='selected'>$name</option>";
				} else {
					echo "<option value=\"$id\">$name</option>";
				}
			}
			echo "</select>";
			echo "<div class='invalid-feedback'>";
      	echo "You must enter who last vialed.";
      echo "</div>";
		}

		public function buildSelectTable ($isMultiple_param) {
			$theArray = $this->returnAll();
			echo "<select class='form-select' id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" required placeholder=\"$this->placeholder_prop\">";
			echo "<option value=''>$this->placeholder_prop</option>";

			foreach($theArray as $row) {
				$name = htmlspecialchars($row[$this->elementNameColumn_prop],ENT_QUOTES);;
				$id = $row[$this->elementIDColumn_prop];
				echo "<option value=\"$id\">$name</option>";
			}
			echo "</select>";
			echo "<div class='invalid-feedback'>";
      echo "You must enter who last vialed.";
      echo "</div>";
		}
	}

	class LoadCoInjectionMarker extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "coinjection_marker_table";
			$this->selectID_prop = "select-coinjection_markers";
			$this->selectName_prop = "coinjectionMarkerArray_htmlName[]";
			$this->placeholder_prop = "Select a coinjection marker";
			$this->placeholder_multiple_prop = "Select coinjection markers";
			$this->elementNameColumn_prop = "coInjectionMarkerName_col";
			$this->elementIDColumn_prop = "coInjectionMarker_id";

			$this->elementHTMLName = 'newCoInjection_htmlName';
			$this->elementString = "Co-injection Marker";
			$this->elementStringLC = "co-injection marker";
		}
	}

	class LoadAntibiotic extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "antibiotic_table";
			$this->selectID_prop = "select-antibiotics";
			$this->selectName_prop = "antibioticArray_htmlName[]";
			$this->placeholder_prop = "Select an antibiotic...";
			$this->placeholder_multiple_prop = "Select antibiotics...";
			$this->elementNameColumn_prop = "antibioticName_col";
			$this->elementIDColumn_prop = "antibiotic_id";

			$this->elementHTMLName = 'newAntibioticResistance_htmlName';
			$this->elementString = "Antibiotic";
			$this->elementStringLC = "antibiotic";
		}

		public function searchRelatedToPlasmid($strainToSearchFor_param) {

			$theSelectString = "SELECT antibioticName_col FROM antibiotic_table ";
			$theSelectString = $theSelectString . "INNER JOIN plasmid_to_antibiotic_table ON antibiotic_table.antibiotic_id = plasmid_to_antibiotic_table.antibiotic_fk ";
			$theSelectString = $theSelectString . "INNER JOIN plasmid_table ON plasmid_to_antibiotic_table.plasmid_fk = plasmid_table.plasmid_id ";
			$theSelectString = $theSelectString . "WHERE plasmid_table.plasmid_id = ?";

			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);

			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param]);
			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			return($existingElement);

		}
	}

	class LoadFluoroTag extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "fluoro_tag_table";
			$this->selectID_prop = "select-fluorotags";
			$this->selectName_prop = "fluorotagArray_htmlName[]";
			$this->placeholder_prop = "Select a fluor/tag...";
			$this->placeholder_multiple_prop = "Select fluor/tags...";
			$this->elementNameColumn_prop = "fluoroTagName_col";
			$this->elementIDColumn_prop = "fluoroTag_id";

			$this->elementHTMLName = 'newFluoro_htmlName';
			$this->elementString = "Fluor/tag";
			$this->elementStringLC = "fluor/tag";
		}

		public function searchRelatedToPlasmid($strainToSearchFor_param) {

			$theSelectString = "SELECT fluoroTagName_col FROM fluoro_tag_table ";
			$theSelectString = $theSelectString . "INNER JOIN plasmid_to_fluoro_tag_table ON fluoro_tag_table.fluoroTag_id = plasmid_to_fluoro_tag_table.fluoro_tag_fk ";
			$theSelectString = $theSelectString . "INNER JOIN plasmid_table ON plasmid_to_fluoro_tag_table.plasmid_fk = plasmid_table.plasmid_id ";
			$theSelectString = $theSelectString . "WHERE plasmid_table.plasmid_id = ?";
			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);

			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param]);
			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			return($existingElement);

		}

	}

	class LoadNFluoroTag extends LoadFluoroTag {
		public function __construct() {
			parent::__construct();
			$this->selectID_prop = "select-n-fluorotags";
			$this->selectName_prop = "fluoroNtagArray_htmlName[]";
			$this->placeholder_prop = "Select an N-tied fluor/tag...";
			$this->placeholder_multiple_prop = "Select N-tied fluor/tags...";
		}
	}

	class LoadCFluoroTag extends LoadFluoroTag {
		public function __construct() {
			parent::__construct();
			$this->selectID_prop = "select-c-fluorotags";
			$this->selectName_prop = "fluoroCtagArray_htmlName[]";
			$this->placeholder_prop = "Select an C-tied fluor/tag...";
			$this->placeholder_multiple_prop = "Select C-tied fluor/tags...";
		}
	}

	class LoadInternalFluoroTag extends LoadFluoroTag {
		public function __construct() {
			parent::__construct();
			$this->selectID_prop = "select-internal-fluorotags";
			$this->selectName_prop = "fluoroInternaltagArray_htmlName[]";
			$this->placeholder_prop = "Select an internal-tied fluor/tag...";
			$this->placeholder_multiple_prop = "Select internal-tied fluor/tags...";
		}
	}

	class LoadGene extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "gene_table";
			$this->selectID_prop = "select-gene";
			$this->selectName_prop = "genesArray_htmlName[]";
			$this->placeholder_prop = "Select a gene...";
			$this->placeholder_multiple_prop = "Select genes...";
			$this->elementNameColumn_prop = "geneName_col";
			$this->elementIDColumn_prop = "gene_id";
		}
	}

	class LoadPromoter extends LoadGeneticElement {
		protected $elementIsPromoterColumn_prop = "isPromoter_col";

		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "gene_table";
			$this->selectID_prop = "select-promoter";
			$this->selectName_prop = "promoterArray_htmlName[]";
			$this->placeholder_prop = "Select a promoter...";
			$this->placeholder_multiple_prop = "";
			$this->elementNameColumn_prop = "geneName_col";
			$this->elementIDColumn_prop = "gene_id";
		}

		// promoter has its own buildselecttable because not every gene is a promoter
		public function buildSelectTable ($isMultiple_param) {
			// $isMultiple_param is not used; we select only one promoter at a time
			$theArray = $this->returnAll();
			echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" placeholder=\"$this->placeholder_prop\">";
			echo "<option value=''>$this->placeholder_prop</option>";
			foreach($theArray as $row) {
				$name = $row[$this->elementNameColumn_prop];
				// promoters get prepended with a P
				$name = "P" . $name;
				$id = $row[$this->elementIDColumn_prop];
				echo "<option value=\"$id\">$name</option>";
			}
			echo "</select>";
		}
		//loadpromoter
		public function buildSelectedTablesWithSingleEntry ($entryToSelect_param) {
			$theEntireArray = $this->returnAll();

		 	echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" single placeholder=\"$this->placeholder_prop\">";

			// NULL entry means nothing was selected; the line below prevents the default first value from showing
			if ($entryToSelect_param == NULL) {
				echo "<option value=''>\"$this->placeholder_prop\"</option>";
			}
			sort($theEntireArray);
			foreach ($theEntireArray as $theEntireArrayItem)
			{
				// Promoters get marked with a P
				$name = $theEntireArrayItem[$this->elementNameColumn_prop];
				// promoters get prepended with a P
				$name = "P" . $name;
				$id = $theEntireArrayItem[$this->elementIDColumn_prop];
				if ($entryToSelect_param == $id) {
					echo "<option value=\"$id\" selected='selected'>$name</option>";
				} else {
					echo "<option value=\"$id\">$name</option>";
				}
			}
			 echo "</select>";
		}

	}

	class LoadTransGene extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "transgene_table";
			$this->selectID_prop = "select-transgene";
			$this->selectName_prop = "transgeneArray_htmlName[]";
			$this->placeholder_prop = "Select a transgene...";
			$this->placeholder_multiple_prop = "Select transgenes...";
			$this->elementNameColumn_prop = "transgeneName_col";
			$this->elementIDColumn_prop = "transgene_id";
		}

		// searches for transgenes related to a given strains
		//
		public function searchRelatedToStrain($strainToSearchFor_param) {

			// left join because not all transgenes will have plasmids and coinjection markers
			$theSelectString = "SELECT DISTINCT transgeneName_col,transgene_table.transgene_id,transgene_table.parent_transgene_col, chromosomeName_col,transgene_table.comments_col,coInjectionMarker_fk FROM transgene_table ";
			$theSelectString = $theSelectString . "LEFT JOIN coinjection_marker_table ON transgene_table.coInjectionMarker_fk = coinjection_marker_table.coInjectionMarker_id ";
			$theSelectString = $theSelectString . "LEFT JOIN transgene_to_plasmids_table ON transgene_table.transgene_id = transgene_to_plasmids_table.transgene_fk ";
			$theSelectString = $theSelectString . "LEFT JOIN plasmid_table ON transgene_to_plasmids_table.plasmid_fk = plasmid_table.plasmid_id ";
			$theSelectString = $theSelectString . "INNER JOIN strain_to_transgene_table ON transgene_table.transgene_id = strain_to_transgene_table.transgene_fk ";
			$theSelectString = $theSelectString . "INNER JOIN strain_table ON strain_to_transgene_table.strain_fk = strain_table.strain_id ";
			$theSelectString = $theSelectString . "WHERE strain_table.strain_id = ?";

			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);
			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();

			return($existingElement);
		}
	}

	class LoadAllele extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "allele_table";
			$this->selectID_prop = "select-allele";
			$this->selectName_prop = "allelesArray_htmlName[]";
			$this->placeholder_prop = "Select an allele...";
			$this->placeholder_multiple_prop = "Select genes (alleles)...";
			$this->elementNameColumn_prop = "alleleName_col";
			$this->elementIDColumn_prop = "allele_id";
		}

		// building select table with alleles
		// will need to look up gene id and then get gene name and then populate list with gene name (allele name)

		public function buildSelectTable ($isMultiple_param) {
			$theArray = $this->returnAll();

			if ($isMultiple_param){
					echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" placeholder=\"$this->placeholder_multiple_prop\"  multiple>";
			} else {
					echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\" placeholder=\"$this->placeholder_prop\">";
			}
			echo "<option value=''>$this->placeholder_prop</option>";
			foreach($theArray as $row) {
					$name = $row[$this->elementNameColumn_prop];
					$id = $row[$this->elementIDColumn_prop];

					$theAlleleObject = new LoadAllele();
					$alleleElementArrayToEdit = $theAlleleObject->returnSpecificRecord($id);

					$theGeneObject = new LoadGene();
					$geneElementArrayToEdit = $theGeneObject->returnSpecificRecord($alleleElementArrayToEdit['gene_fk']);

					$geneToAdd = $geneElementArrayToEdit['geneName_col'];
					echo "<option value=\"$id\">$geneToAdd ($name) </option>";
			}
			echo "</select>";
		}

		// needs to display both the allele and the associated gene
		public function buildSelectedTablesWithMultipleEntries($arrayToSelect_param) {
			$theEntireArray = $this->returnAll();
			echo "<select id=\"$this->selectID_prop\" name=\"$this->selectName_prop\"  class='selectized' placeholder=\"$this->placeholder_multiple_prop\" multiple>";
			// speed up our searches
			sort($theEntireArray);
			sort($arrayToSelect_param);
			foreach ($theEntireArray as $theEntireArrayItem)
			{
				$name = htmlspecialchars($theEntireArrayItem[$this->elementNameColumn_prop],ENT_QUOTES);
				$id = $theEntireArrayItem[$this->elementIDColumn_prop];
				$selected = false;
				foreach ($arrayToSelect_param as $theMarkedArrayItem)
				{
					if ($theMarkedArrayItem == $id) {
						$selected = true;
						break;
					}
				}

				// previously, this method just populated the alleles without the corresponding gene
				$theAlleleObject = new LoadAllele();
				$alleleElementArrayToEdit = $theAlleleObject->returnSpecificRecord($id);

				$theGeneObject = new LoadGene();
				$geneElementArrayToEdit = $theGeneObject->returnSpecificRecord($alleleElementArrayToEdit['gene_fk']);
				$geneToAdd = $geneElementArrayToEdit['geneName_col'];

				if ($selected) {
					echo "<option value=\"$id\" selected>$geneToAdd ($name)</option>";
				} else {
					echo "<option value=\"$id\">$geneToAdd ($name)</option>";
				}
			}
			echo "</select>";
		}

		// allele related search
		public function searchRelatedToStrain($strainToSearchFor_param) {

			$theSelectString = "SELECT allele_table.alleleName_col,allele_table.comments_col as allele_comments,gene_table.geneName_col,gene_table.chromosomeName_col,gene_table.comments_col as gene_comments FROM allele_table ";
			// not every allele has an asociated gene, so it needs to be left JOIN
			$theSelectString = $theSelectString . "LEFT JOIN gene_table ON allele_table.gene_fk = gene_table.gene_id ";
    	$theSelectString = $theSelectString . "INNER JOIN strain_to_allele_table ON allele_table.allele_id = strain_to_allele_table.allele_fk ";
			$theSelectString = $theSelectString . "INNER JOIN strain_table ON strain_to_allele_table.strain_fk = strain_table.strain_id ";
			$theSelectString = $theSelectString . "WHERE strain_table.strain_id = ?";

			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);
			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			return($existingElement);
		}

		public function searchRelatedToStrainForSequenceData($strainToSearchFor_param) {

			$theSelectString = "SELECT sequenceDataName_col,sequence_data_col  FROM allele_table ";
			// not every allele has an asociated gene, so it needs to be left JOIN
			$theSelectString = $theSelectString . "INNER JOIN strain_to_allele_table ON allele_table.allele_id = strain_to_allele_table.allele_fk ";
			$theSelectString = $theSelectString . "INNER JOIN strain_table ON strain_to_allele_table.strain_fk = strain_table.strain_id ";
			$theSelectString = $theSelectString . "WHERE strain_table.strain_id = ? AND allele_table.sequenceDataName_col != ?"; // not equal

			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);
			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param,""]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			return($existingElement);
		}
	}



	class LoadBalancer extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "balancer_table";
			$this->selectID_prop = "select-balancers";
			$this->selectName_prop = "balancersArray_htmlName[]";
			$this->placeholder_prop = "Select a balancer...";
			$this->placeholder_multiple_prop = "Select balancers...";
			$this->elementNameColumn_prop = "balancerName_col";
			$this->elementIDColumn_prop = "balancer_id";
		}

		// search related strains
		// can we make this funcion return balancers with
		public function searchRelatedToStrain($strainToSearchFor_param) {

			$theSelectString = "SELECT balancer_table.balancerName_col,balancer_table.comments_col,balancer_table.chromosomeName_col,balancer_table.chromosomeName2_col FROM balancer_table ";
			$theSelectString = $theSelectString . "INNER JOIN strain_to_balancer_table ON balancer_table.balancer_id = strain_to_balancer_table.balancer_fk ";
			$theSelectString = $theSelectString . "INNER JOIN strain_table ON strain_to_balancer_table.strain_fk = strain_table.strain_id ";
			$theSelectString = $theSelectString . "WHERE strain_table.strain_id = ?";

			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);
			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			return($existingElement);
		}

	}

	class LoadParentStrains extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "strain_table";
			$this->selectID_prop = "select-parentStrains";
			$this->selectName_prop = "parentStrainsArray_htmlName[]";
			$this->placeholder_prop = "Select a parent strain...";
			$this->placeholder_multiple_prop = "Select parent strains...";
			$this->elementNameColumn_prop = "strainName_col";
			$this->elementIDColumn_prop = "strain_id";
		}

		// search related strains
		public function searchRelatedToStrain($strainToSearchFor_param) {
			$theSelectString = "SELECT strain_table.strainName_col FROM strain_table as truestrain_table ";
			$theSelectString = $theSelectString . "INNER JOIN strain_to_parent_strain_table ON truestrain_table.strain_ID = strain_to_parent_strain_table.strain_fk ";
			$theSelectString = $theSelectString . "INNER JOIN strain_table ON strain_to_parent_strain_table.parent_strain_fk = strain_table.strain_ID ";
			$theSelectString = $theSelectString . "WHERE truestrain_table.strain_id = ?";

			$this->preparedSQLQuery_prop = $this->sqlPrepare($theSelectString);
			$this->preparedSQLQuery_prop->execute([$strainToSearchFor_param]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			return($existingElement);
		}

	}

	class LoadTrueStrains extends LoadParentStrains {
		public function __construct() {
			parent::__construct();
			$this->selectID_prop = "select-trueStrains";
			$this->selectName_prop = "trueStrainsArray_htmlName[]";
			$this->placeholder_prop = "Select a strain...";
			$this->placeholder_multiple_prop = "Select strains...";
		}
	}

	class LoadPlasmid extends LoadGeneticElement {
		public function __construct() {
			parent::__construct();
			$this->tableName_prop = "plasmid_table";
			$this->selectID_prop = "select-plasmid";
			$this->selectName_prop = "plasmidArray_htmlName[]";
			$this->placeholder_prop = "Select a plasmid...";
			$this->placeholder_multiple_prop = "Select plasmids...";
			$this->elementNameColumn_prop = "plasmidName_col";
			$this->elementIDColumn_prop = "plasmid_id";
		}
	}

	class LoadManyToManyTables extends LoadGeneticElement {
		protected $selectOnThisID_prop;
		protected $itemToBeSelected_prop;
		protected $hiddenArrayName_prop;
		protected $arrayOfItems_prop;

		//added in PHP 8.2
		protected String $itemToBeReturned_prop;

		public function __construct($selectOnThisID_param) {
			parent::__construct();
			$this->selectOnThisID_prop = $selectOnThisID_param;
		}

		public function ReturnMarkedGeneElements() {
			// $this->elementIDColumn_prop is the strain id
			// $this->tableName_prop is the intermediate table that ties the two together
			// itemToBeReturned_prop is the allele id
			// select allele_id from strain_to_allele_table where strain_id = selectOnThisID_prop (passed in value of a particular strain)
			$this->preparedSQLQuery_prop = $this->sqlPrepare("SELECT $this->itemToBeReturned_prop FROM $this->tableName_prop WHERE $this->elementIDColumn_prop = ?");

			$this->preparedSQLQuery_prop->execute([$this->selectOnThisID_prop]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			// what does this actually return? A two dimensional array of items. Yikes!
			// we use array_column to pull out the relevant key
			$arrayToReturn = array_column($existingElement, "$this->itemToBeReturned_prop");

			return ($arrayToReturn);
		}

		public function PopulateHiddenArray() {
			$arrayOfItems = $this->ReturnMarkedGeneElements();
			foreach($arrayOfItems as $theItem) {
	      echo "<input type=\"hidden\" name=\"$this->hiddenArrayName_prop[]\" value=\"$theItem\">";
	    }
		}
	}

	class LoadParentStrainsToStrain extends LoadManyToManyTables {
		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'strain_to_parent_strain_table';
			$this->elementIDColumn_prop = 'strain_fk';
			$this->itemToBeReturned_prop = 'parent_strain_fk';
			$this->hiddenArrayName_prop = "originalParentStrainArray_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadAllelesToStrain extends LoadManyToManyTables {
		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'strain_to_allele_table';
			$this->elementIDColumn_prop = 'strain_fk';
			$this->itemToBeReturned_prop = 'allele_fk';
			$this->hiddenArrayName_prop = "originalAlleleArray_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadBalancersToStrain extends LoadManyToManyTables {
		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'strain_to_balancer_table';
			$this->elementIDColumn_prop = 'strain_fk';
			$this->itemToBeReturned_prop = 'balancer_fk';
			$this->hiddenArrayName_prop = "originalBalancerArray_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadTransGenesToStrain extends LoadManyToManyTables {
		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'strain_to_transgene_table';
			$this->elementIDColumn_prop = 'strain_fk';
			$this->itemToBeReturned_prop = 'transgene_fk';
			$this->hiddenArrayName_prop = "originalTransGenesArray_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadPlasmidsToTransGenes extends LoadManyToManyTables {
		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'transgene_to_plasmids_table';
			$this->elementIDColumn_prop = 'transgene_fk';
			$this->itemToBeReturned_prop = 'plasmid_fk';
			$this->hiddenArrayName_prop = "originalPlasmidsArray_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadAntibioticsToPlasmid extends LoadManyToManyTables {
		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'plasmid_to_antibiotic_table';
			$this->elementIDColumn_prop = 'plasmid_fk';
			$this->itemToBeReturned_prop = 'antibiotic_fk';
			$this->hiddenArrayName_prop = "originalAntibioticsArray_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadFluoroTagsToPlasmid extends LoadManyToManyTables {
		// must be assigned by subclass
		protected $in_c_internal_actualValue_prop = "";

		//added in 8.2
		protected String $in_c_internal_prop;

		public function __construct($selectOnThisID_param) {
			$this->tableName_prop = 'plasmid_to_fluoro_tag_table';
			$this->elementIDColumn_prop = 'plasmid_fk';
			$this->itemToBeReturned_prop = 'fluoro_tag_fk';
			$this->in_c_internal_prop = 'n_c_internal_col';
			// must be assigned by subclass
			$this->hiddenArrayName_prop = "";
			parent::__construct($selectOnThisID_param);
		}

		public function ReturnMarkedGeneElements() {
			// select allele_id from strain_to_allele_table where strain_id = selectOnThisID_prop
			$this->preparedSQLQuery_prop = $this->sqlPrepare("SELECT $this->itemToBeReturned_prop FROM $this->tableName_prop WHERE $this->elementIDColumn_prop = ? AND $this->in_c_internal_prop = ?");

			$this->preparedSQLQuery_prop->execute([$this->selectOnThisID_prop,$this->in_c_internal_actualValue_prop]);

			$existingElement = $this->preparedSQLQuery_prop->fetchAll();
			// what does this actually return? A two dimensional array of items. Yikes!
			// we use array_column to pull out the relevant key
			$arrayToReturn = array_column($existingElement, "$this->itemToBeReturned_prop");
			return ($arrayToReturn);
		}
	}

	class LoadFluoroNTagsToPlasmid extends LoadFluoroTagsToPlasmid {
		public function __construct($selectOnThisID_param) {
			$this->in_c_internal_actualValue_prop = 'N';
			$this->hiddenArrayName_prop = "originalNFluoroTags_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadFluoroCTagsToPlasmid extends LoadFluoroTagsToPlasmid {
		public function __construct($selectOnThisID_param) {
			$this->in_c_internal_actualValue_prop = 'C';
			$this->hiddenArrayName_prop = "originalCFluoroTags_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class LoadFluoroITagsToPlasmid extends LoadFluoroTagsToPlasmid {
		public function __construct($selectOnThisID_param) {
			$this->in_c_internal_actualValue_prop = 'I';
			$this->hiddenArrayName_prop = "originalIFluoroTags_postVar";
			parent::__construct($selectOnThisID_param);
		}
	}

	class GenotypeObject extends Peri_Database {
		protected $objectName_prop = "";
		protected $objectChromosome_prop;
		protected $commentArray_prop = array();

		public function __construct($genotypeObject_param) {
			$this->objectChromosome_prop = $genotypeObject_param['chromosomeName_col'];
		}

		public function FetchName() {
			return $this->objectName_prop;
		}

		public function FetchChromosome() {
			return $this->objectChromosome_prop;
		}

		public function FetchChromosomeDelimeter() {
			return ', ';
		}

		public function FetchComment(&$outCommmentArray_param) {
			$outCommmentArray_param = $this->commentArray_prop;
			return count($outCommmentArray_param); // this is an array and may be one or two items
		}

		public function AddToObjectArray(&$outObjectArray_param) {
			array_push($outObjectArray_param, $this);
		}

		protected function AddCommentToCommentArray ($inComment_param) {
			$comment = $inComment_param;
			if ($comment == NULL) {
				$comment = "";
			}
			array_push($this->commentArray_prop, htmlspecialchars($comment,ENT_QUOTES));
		}
	}

	class AlleleGenotypeObject extends GenotypeObject {
		public function __construct($genotypeObject_param) {
			$geneName = $genotypeObject_param['geneName_col'];
			if ($geneName == NULL) {
				$geneName = "";
			}
			$alleleName = $genotypeObject_param['alleleName_col'];
			if ($alleleName == NULL) {
				$alleleName = "";
			}
			$this->objectName_prop = htmlspecialchars($geneName,ENT_QUOTES) . "(" . htmlspecialchars($alleleName,ENT_QUOTES) . ")";
			$this->AddCommentToCommentArray($genotypeObject_param['allele_comments']);
			$this->AddCommentToCommentArray($genotypeObject_param['gene_comments']);
			//alleles have genes and genes have chromosomes
			parent::__construct($genotypeObject_param);
		}
	}

	class TransGeneGenotypeObject extends GenotypeObject {
		public function __construct($genotypeObject_param) {
			$this->objectName_prop = htmlspecialchars($genotypeObject_param['transgeneName_col'],ENT_QUOTES);
			$this->AddCommentToCommentArray($genotypeObject_param['comments_col']);
			parent::__construct($genotypeObject_param);
		}
	}

	class BalancerGenotypeObject extends GenotypeObject {
		protected $object2ndChromosome_prop;

		public function __construct($genotypeObject_param) {
			$this->objectName_prop = htmlspecialchars($genotypeObject_param['balancerName_col'],ENT_QUOTES);
			$this->AddCommentToCommentArray($genotypeObject_param['comments_col']);
			parent::__construct($genotypeObject_param);
			$this->object2ndChromosome_prop = $genotypeObject_param['chromosomeName2_col'];
		}

		public function AddToObjectArray(&$outObjectArray_param) {
			parent::AddToObjectArray($outObjectArray_param);
			// we create a second balancer object with the second chromosome assigned to the lone chromosome field
			// what happens to balancer comment; we don’t current display this anywhere and that might be a problem
			if ($this->object2ndChromosome_prop != ""){
				$thisBalancerObjectWithSecondChromosome['balancerName_col'] = $this->objectName_prop;
				$thisBalancerObjectWithSecondChromosome['chromosomeName_col'] = $this->object2ndChromosome_prop;
				// the constructor expects something here
				$thisBalancerObjectWithSecondChromosome['chromosomeName2_col'] = "";
				$thisBalancerObjectWithSecondChromosome['comments_col'] = "";
				$theGenotypeObject = new BalancerGenotypeObject($thisBalancerObjectWithSecondChromosome);
				array_push($outObjectArray_param, $theGenotypeObject);
			}
		}

		public function FetchChromosomeDelimeter() {
			return '/';
		}
	}

?>
