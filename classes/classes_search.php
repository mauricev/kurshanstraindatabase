<?php

	require_once('classes_database.php');

	class Joiner {
		protected $joinCatalog_prop = array ();
		protected $joinKey_prop = array ();

		// I believe this works as follows. When we search, we need to build a join string of all the relevant intermediary tables

		public function __construct() {
			// do we even need this?
		}

		public function addToJoinString ($keyedItemToJoin_param) {
				$this->joinKey_prop[$keyedItemToJoin_param] = $this->joinCatalog_prop[$keyedItemToJoin_param];
		}

		public function getJoinString() {
			$joinString_prop = "";
			foreach($this->joinKey_prop as $joinItem) {
				$joinString_prop = $joinString_prop . $joinItem;
			}
			return $joinString_prop;
		}
	}

	class JoinerForStrains extends Joiner {
		public function __construct() {
			$this->joinKey_prop['contributor'] ='';
			$this->joinKey_prop['parent_strain_top'] ='';
			$this->joinKey_prop['parent_strain_bottom'] ='';
			$this->joinKey_prop['allele_top']  ='';
			$this->joinKey_prop['allele_bottom'] ='';
			$this->joinKey_prop['balancer_top']  ='';
			$this->joinKey_prop['balancer_bottom'] ='';
			$this->joinKey_prop['gene'] ='';
			$this->joinKey_prop['transgene_top'] ='';
			$this->joinKey_prop['transgene_bottom'] ='';
			$this->joinKey_prop['plasmids_top'] ='';
			$this->joinKey_prop['plasmids_bottom'] ='';
			$this->joinKey_prop['coinjection'] ='';
			$this->joinKey_prop['author'] ='';
			$this->joinKey_prop['editor'] ='';
		}
	}

	class JoinerForPlasmids extends Joiner {
		public function __construct() {
			$this->joinKey_prop['contributor'] ='';
			$this->joinKey_prop['gene'] ='';
			$this->joinKey_prop['promoter'] ='';
			$this->joinKey_prop['antibiotic_top'] ='';
			$this->joinKey_prop['antibiotic_bottom'] ='';
			$this->joinKey_prop['plasmid_to_fluoro__top'] ='';
			$this->joinKey_prop['plasmid_to_fluoro__bottom'] ='';
			$this->joinKey_prop['author'] ='';
			$this->joinKey_prop['editor'] ='';
		}
	}

	class InnerJoinerForStrains extends JoinerForStrains {
		public function __construct() {

			// this is a list of every possible inner join
			// when we have many to many intermediary tables, there are two joins, one for each "side" of intermediary table, hence the top and bottom

			//since we are searcing for strains with strains, a self-referential join if you will, we need to alias one of them, so mysql knows
			// which one is which. I call the parent strain truestrain. (Search this page for "as truestrain_table" to see where the alias is created)
			$this->joinCatalog_prop['author'] =' INNER JOIN author_table ON truestrain_table.author_fk = author_table.author_id ';
			$this->joinCatalog_prop['editor'] =' INNER JOIN author_table ON truestrain_table.editor_fk = author_table.author_id ';

			$this->joinCatalog_prop['contributor'] =' INNER JOIN contributor_table ON truestrain_table.contributor_fk = contributor_table.contributor_id ';

			$this->joinCatalog_prop['parent_strain_top'] = ' INNER JOIN strain_to_parent_strain_table ON truestrain_table.strain_ID = strain_to_parent_strain_table.strain_fk ';
			$this->joinCatalog_prop['parent_strain_bottom'] = ' INNER JOIN strain_table ON strain_to_parent_strain_table.parent_strain_fk = strain_table.strain_ID ';

			// as an example, we take the intermediary table and match its strain id to that id of the strain table (truestrain because we are dealing with parent strains elsewhere)
			// we then take allele id in this intermediary table and match to the id of the allele table
			$this->joinCatalog_prop['allele_top'] =' INNER JOIN strain_to_allele_table ON truestrain_table.strain_ID = strain_to_allele_table.strain_fk ';
			$this->joinCatalog_prop['allele_bottom'] =' INNER JOIN allele_table ON strain_to_allele_table.allele_fk = allele_table.allele_id ';

			$this->joinCatalog_prop['balancer_top'] =' INNER JOIN strain_to_balancer_table ON truestrain_table.strain_ID = strain_to_balancer_table.strain_fk ';
			$this->joinCatalog_prop['balancer_bottom'] =' INNER JOIN balancer_table ON strain_to_balancer_table.balancer_fk = balancer_table.balancer_id ';

			$this->joinCatalog_prop['gene'] = ' INNER JOIN gene_table ON allele_table.gene_fk = gene_table.gene_ID ';

			$this->joinCatalog_prop['transgene_top'] = ' INNER JOIN strain_to_transgene_table ON truestrain_table.strain_ID = strain_to_transgene_table.strain_fk ';
			$this->joinCatalog_prop['transgene_bottom'] =' INNER JOIN transgene_table ON strain_to_transgene_table.transgene_fk = transgene_table.transgene_id ';

			$this->joinCatalog_prop['plasmids_top'] = ' INNER JOIN transgene_to_plasmids_table ON transgene_table.transgene_id = transgene_to_plasmids_table.transgene_fk';
			$this->joinCatalog_prop['plasmids_bottom'] = ' INNER JOIN plasmid_table ON transgene_to_plasmids_table.plasmid_fk = plasmid_table.plasmid_id ';

			$this->joinCatalog_prop['coinjection'] = ' INNER JOIN coinjection_marker_table ON transgene_table.coInjectionMarker_fk = coinjection_marker_table.coInjectionMarker_id';

			parent::__construct();
		}
	}

	class InnerJoinerForPlasmids extends JoinerForPlasmids {
		public function __construct() {

			$this->joinCatalog_prop['contributor'] =' INNER JOIN contributor_table ON plasmid_table.contributor_fk = contributor_table.contributor_id ';

			$this->joinCatalog_prop['author'] =' INNER JOIN author_table ON plasmid_table.author_fk = author_table.author_id ';
			$this->joinCatalog_prop['editor'] =' INNER JOIN author_table ON plasmid_table.editor_fk = author_table.author_id ';


			$this->joinCatalog_prop['gene'] = ' INNER JOIN gene_table ON plasmid_table.gene_fk = gene_table.gene_ID ';
			// gene table can't be referenced again if it's included as part of gene in the line above, so we alias the table name
			$this->joinCatalog_prop['promoter'] = ' INNER JOIN gene_table as gene ON plasmid_table.promotorGene_fk = gene.gene_ID ';

			$this->joinCatalog_prop['antibiotic_top'] = ' INNER JOIN plasmid_to_antibiotic_table ON plasmid_table.plasmid_id = plasmid_to_antibiotic_table.plasmid_fk  ';
			$this->joinCatalog_prop['antibiotic_bottom'] = ' INNER JOIN antibiotic_table ON plasmid_to_antibiotic_table.antibiotic_fk = antibiotic_table.antibiotic_id ';

			$this->joinCatalog_prop['plasmid_to_fluoro__top'] =' INNER JOIN plasmid_to_fluoro_tag_table ON plasmid_table.plasmid_id = plasmid_to_fluoro_tag_table.plasmid_fk ';
			$this->joinCatalog_prop['plasmid_to_fluoro__bottom'] =' INNER JOIN fluoro_tag_table ON plasmid_to_fluoro_tag_table.fluoro_tag_fk = fluoro_tag_table.fluoroTag_id ';

			parent::__construct();
		}
	}
	// why is there is no leftjoiner for plasmids. // not even sure at this point why there is an inner joiner for plamids at all.

	class LeftJoinerForStrains extends JoinerForStrains {
		public function __construct() {

			// a list of every possible left join. we need to do left joins because not every strain will have all these features and we
			// don't want to exclude any that are missing stuff
			$this->joinCatalog_prop['allele_top'] =' LEFT JOIN strain_to_allele_table ON truestrain_table.strain_ID = strain_to_allele_table.strain_fk ';
			$this->joinCatalog_prop['allele_bottom'] =' LEFT JOIN allele_table ON strain_to_allele_table.allele_fk = allele_table.allele_id ';
			$this->joinCatalog_prop['gene'] = ' LEFT JOIN gene_table ON allele_table.gene_fk = gene_table.gene_ID ';
			$this->joinCatalog_prop['transgene_top'] = ' LEFT JOIN strain_to_transgene_table ON truestrain_table.strain_ID = strain_to_transgene_table.strain_fk ';
			$this->joinCatalog_prop['transgene_bottom'] =' LEFT JOIN transgene_table ON strain_to_transgene_table.transgene_fk = transgene_table.transgene_id ';
			$this->joinCatalog_prop['balancer_top'] =' LEFT JOIN strain_to_balancer_table ON truestrain_table.strain_ID = strain_to_balancer_table.strain_fk ';
			$this->joinCatalog_prop['balancer_bottom'] =' LEFT JOIN balancer_table ON strain_to_balancer_table.balancer_fk = balancer_table.balancer_id ';

			parent::__construct();
		}
	}

// we create a new GeneMultipleElementSearch that inherits from GeneElementSearch.

	class GeneElementSearch {
		protected $theWhereClauseString_prop = "";

		protected $arrayToBuildFrom_prop;
		protected $searchParameter_prop;
		// for now this is hard-coded
		protected $IsItORSearch_prop = true;

		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			$this->buildElementWhereClause($theBuddingQueryArray_param, $theHavingCountArray);
		}

		// this is for SINGLE element search only
		public function buildElementWhereClause(&$theBuddingQueryArray_param, &$theHavingCountArray) {
			$theArraySize = count ($this->arrayToBuildFrom_prop) - 1;
			// if the array has one element
			if ($theArraySize == 0) {
				$this->theWhereClauseString_prop = $this->searchParameter_prop;
				array_push($theBuddingQueryArray_param, $this->arrayToBuildFrom_prop[0]);
			} else  {

				$this->theWhereClauseString_prop = "( ";
				
				for ($theIndex = 0 ;  $theIndex <= $theArraySize; $theIndex++) {
					if ($theIndex == 0) {
						 $this->theWhereClauseString_prop = $this->theWhereClauseString_prop . $this->searchParameter_prop; // $this->searchParameter_prop is here
					} else {
						if ($this->IsItORSearch_prop) {
							$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . ' OR ' . $this->searchParameter_prop;
						}
					}
					array_push($theBuddingQueryArray_param, $this->arrayToBuildFrom_prop[$theIndex]);

				}
				// outside loop to close off the parentheses
				$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . " ) ";

			}
			//echo "<br>theWhereClauseString_prop: " . $this->theWhereClauseString_prop . "<br>";
		}

		// I believe this method combines multiple where clauses into one ever growing where clause (the primary where clause)
		// the clause defaults to AND. That means if say you search for an allele and a transgene in a strain
		// it will return only those strains with BOTH.
		// $inConjunction_param is the conjunction parameter
		// this is our ever growing where string: $thePrimaryWhereClause_param and
		// $this->theWhereClauseString_prop is the string for this object

		public function concatElementWhereClauseToMasterWhereClauseInternal($thePrimaryWhereClause_param, $inConjunction_param) {

				$incomingWhereString1 = $thePrimaryWhereClause_param;

				//echo "<br>thePrimaryWhereClause_param: " . $thePrimaryWhereClause_param . "<br>";

				$incomingWhereString2 = $this->theWhereClauseString_prop; // this object’s where clause built comes from the above method

				//echo "<br>theWhereClauseString_prop: " . $this->theWhereClauseString_prop . "<br>";

				$theOutGoingWhereString = "";
				if (($incomingWhereString1 != "" ) && ($incomingWhereString2 != "" )) {
					$theOutGoingWhereString = $incomingWhereString1 . $inConjunction_param . $incomingWhereString2;
				} else if ($incomingWhereString1 != "" ) {
					$theOutGoingWhereString = $incomingWhereString1;
				} else {
					$theOutGoingWhereString = $incomingWhereString2;
				}
				
				//echo "<br>theOutGoingWhereString: " . $theOutGoingWhereString . "<br>";
				
				return $theOutGoingWhereString;
		}

		public function concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause_param) {
				return $this->concatElementWhereClauseToMasterWhereClauseInternal($thePrimaryWhereClause_param,' AND ');
		}
	}

// this class is for elements that can have multiple selections
// these can be OR or AND searches and we need to handle the AND cases special for the MySQL query
	class GeneMultipleElementSearch  extends GeneElementSearch {
		protected $theWhereClauseString_prop = "";

		protected $arrayToBuildFrom_prop;
		protected $searchParameter_prop;
		// for now this is hard-coded
		protected $IsItORSearch_prop = true;

		protected $foreignKeyTable_prop;
		protected $foreignKey_prop;

		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
		}

		public function buildElementWhereClause(&$theBuddingQueryArray_param, &$theHavingCountArray) {
			$theArraySize = count ($this->arrayToBuildFrom_prop) - 1;
			// if the array has one element
			if ($theArraySize == 0) {
				$this->theWhereClauseString_prop = $this->searchParameter_prop;
				array_push($theBuddingQueryArray_param, $this->arrayToBuildFrom_prop[0]);
			} else  {
				// we are building the elements of an individual where clause, so if I search for two alleles or two transgenes
				// this code will build that search and add each element to the where clause and to the query array
				// how would we do an AND search?
				// imagine we are doing
				// transgene_table.transgene_id in (?,?)

				// the first problem is that my parentheses are in the wrong place
				// i need to have the
				// if $this->IsItORSearch_prop at the top and bottom to put their parentheses in the right place
				// here’s the new loop

				// we need a having array. it needs a search parameter element and a count of items here
				// so it needs to be a two dimensional array
				// we could add the having array to genelementssearch. actually plasmids do have it

				// need to populate the having clause; we use arraycount and append an array with searchParameter_prop and arraycount


				if ($this->IsItORSearch_prop) {  // or search
					$this->theWhereClauseString_prop = "( ";
				}
				else {	// and search
					$this->theWhereClauseString_prop = $this->searchParameter_prop . 'in (';
				}
				for ($theIndex = 0 ; $theIndex <= $theArraySize; $theIndex++) {
					if ($theIndex == 0 && $this->IsItORSearch_prop) {
						 $this->theWhereClauseString_prop = $this->theWhereClauseString_prop . $this->searchParameter_prop;
					} else {
						if ($this->IsItORSearch_prop) {
							$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . ' OR ' . $this->searchParameter_prop;
				 		} else {
				 			if ($theIndex < $theArraySize) {	// we are not at the last entry yet
								$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . '?, '; // the comma is here!
							} else {
								$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . '?'; // at the last entry, no comma; last line below will append paren
							}
							// i think right here we need to add elements to the having array and add to the count of it
							// we add $this->searchParameter_prop at the beginning
							// we need to create an array consisting of two elements, $this->searchParameter_prop and the count
							// and append it to having array here
							// at the end we will see if having has any elements and add the group by
							// that's it!

							$theHavingCountArray["$this->searchParameter_prop"] = $theArraySize + 1; // +1 because arraysize is assuming a count starting at zero
							// at the end, we loop through this array
							// if it’s > 1
							// theHavingCountArray is an actual count of the elements
						}
					}
					array_push($theBuddingQueryArray_param, $this->arrayToBuildFrom_prop[$theIndex]);
				}
				// outside loop to close off the parentheses when search is
				$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . " ) ";
			}
		}

		// I believe this method combines multiple where clauses into one ever growing where clause (the primary where clause)
		// the clause defaults to AND. That means if say you search for an allele and a transgene in a strain
		// it will return only those strains with BOTH.
		// $inConjunction_param is the conjunction parameter
		// this is our ever growing where string: $thePrimaryWhereClause_param and
		// $this->theWhereClauseString_prop is the string for this object

		//protected $foreignKeyTable_prop;
		//protected $foreignKey_prop;

		/*
		AND strain_table.strain_id NOT IN (
    SELECT strain_fk 
    FROM strain_to_transgene_table 
    WHERE transgene_fk != 5

    or 
    WHERE transgene_fk NOT IN (5, 8)
)
*/
		public function restrictSearchClause(&$restrictClause_param, &$theBuddingQueryArray_param) {
			// output is different for a single element versus multiple elements
			$theArraySize = count ($this->arrayToBuildFrom_prop) - 1;
			if($theArraySize >= 0) {
				$restrictClause_param = $restrictClause_param . " AND truestrain_table.strain_id NOT IN ( SELECT strain_fk FROM " . $this->foreignKeyTable_prop . " WHERE " . $this->foreignKey_prop;
				// if the array has one element
				if ($theArraySize == 0) {
					$restrictClause_param = $restrictClause_param . " != ?";
					array_push($theBuddingQueryArray_param, $this->arrayToBuildFrom_prop[0]);
				} else  {
					$restrictClause_param = $restrictClause_param . " NOT IN (";
					for ($theIndex = 0 ; $theIndex <= $theArraySize; $theIndex++) {
						if ($theIndex < $theArraySize) {	// we are not at the last entry yet
							$restrictClause_param = $restrictClause_param . '?, '; // the comma is here!
						} else {
							$restrictClause_param = $restrictClause_param . '?'; // at the last entry, no comma; last line below will append closing parenthesis
						}
						array_push($theBuddingQueryArray_param, $this->arrayToBuildFrom_prop[$theIndex]);
					}
					$restrictClause_param = $restrictClause_param . " ) ";
				}
				$restrictClause_param = $restrictClause_param . " )";
			}
		}
		
	}

	class StrainsSearchForStrains extends GeneElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			// constructor is not called
			$this->searchParameter_prop = 'truestrain_table.strain_ID  = ?';

			$theWhereClauseArray = array();

			// because we search for parent strains on the same page we are searching for strains, we need strains
			// to be a different class so they don't conflict
			// this is an array of max one item

			if ($_POST['trueStrainsArray_htmlName'][0] != "") {
				// we used to search for name, but now we are searching for id because we are using a select structure
				array_push($theWhereClauseArray, 'truestrain_table.strain_id = ?');
      	array_push($theBuddingQueryArray_param, $_POST['trueStrainsArray_htmlName'][0]);
      }

			if ( (isset($_POST['contributorArray_htmlName'])) && ($_POST['contributorArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('contributor');

				array_push($theWhereClauseArray, 'contributor_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['contributorArray_htmlName'][0]);
      }

			// the more I look at this, the more I think we should have the search do a left join.
			if ( (isset($_POST['authorArray_htmlName'])) && ($_POST['authorArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('author');

				array_push($theWhereClauseArray, 'author_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['authorArray_htmlName'][0]);
      }

			if ( (isset($_POST['editorArray_htmlName'])) && ($_POST['editorArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('editor');

				array_push($theWhereClauseArray, 'editor_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['editorArray_htmlName'][0]);
      }

			if ( (isset($_POST['freezerArray_htmlName'])) && ($_POST['freezerArray_htmlName'][0] != "") ) {
				array_push($theWhereClauseArray, 'fullFreezer_col = ?');
      	array_push($theBuddingQueryArray_param, $_POST['freezerArray_htmlName'][0]);
      }

			if ( (isset($_POST['nitrogenArray_htmlName'])) && ($_POST['nitrogenArray_htmlName'][0] != "") ) {
				array_push($theWhereClauseArray, 'fullNitrogen_col = ?');
      	array_push($theBuddingQueryArray_param, $_POST['nitrogenArray_htmlName'][0]);
      }

      if (isset($_POST['dateFrozenBeginning_htmlName'])) {
					if (($_POST['dateFrozenBeginning_htmlName'] != "") && ($_POST['dateFrozenEnding_htmlName'] != "") ) {
	        array_push($theWhereClauseArray, 'dateFrozen_col BETWEEN ? and ?');
					// this will cause the query array to have one more item than the where clause array
					$beginningDate = DateTime::createFromFormat("m/d/Y", $_POST['dateFrozenBeginning_htmlName']);
					$beginningDateTimeStamp = $beginningDate->getTimestamp();
					$beginningMySQLDate = date("Y-m-d",$beginningDateTimeStamp);

					$endingDate = DateTime::createFromFormat("m/d/Y", $_POST['dateFrozenEnding_htmlName']);
					$endingDateTimeStamp = $endingDate->getTimestamp();
					$endingMySQLDate = date("Y-m-d",$endingDateTimeStamp);

	    		array_push($theBuddingQueryArray_param, $beginningMySQLDate);
	    		array_push($theBuddingQueryArray_param, $endingMySQLDate);
	      }
			}

			if (isset($theWhereClauseArray)) {
				$theArraySize = count($theWhereClauseArray) - 1;
				if ($theArraySize == 0) {
					// there is one item
					$this->theWhereClauseString_prop = $theWhereClauseArray[0];
				} else {
					for ($theIndex = 0; $theIndex <= $theArraySize; $theIndex++) {
						if ($theIndex == 0) {
							$this->theWhereClauseString_prop = $theWhereClauseArray[0];
						} else {
							$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . ' AND ' . $theWhereClauseArray[$theIndex];
						}
					}
				}
			}
		}

	}

	class PlasmidSearchForPlasmids extends GeneElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			// constructor is not called
			$this->searchParameter_prop = 'plasmids_table.plasmid_ID  = ?';

			$theWhereClauseArray = array();

			// because we search for parent strains on the same page we are searching for strains, we need strains
			// to be a different class so they don't conflict
			// this is an array of max one item

			// plasmid name
			if ($_POST['plasmidArray_htmlName'][0] != "") {
				// we used to search for name, but now we are searching for id because we are using a select structure
				array_push($theWhereClauseArray, 'plasmid_table.plasmid_id = ?');
      	array_push($theBuddingQueryArray_param, $_POST['plasmidArray_htmlName'][0]);
      }

			// cDNA
			if ((isset($_POST['cDNA_htmlName'])) && ($_POST['cDNA_htmlName'] != "") ) {
				array_push($theWhereClauseArray, 'other_cDNA_col = ?');
				array_push($theBuddingQueryArray_param, $_POST['cDNA_htmlName']);
			}

			// contributor name
			// notice we add it to the join string, because we are looking it up!
			if ( (isset($_POST['contributorArray_htmlName'])) && ($_POST['contributorArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('contributor');

				array_push($theWhereClauseArray, 'contributor_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['contributorArray_htmlName'][0]);
      }

			if ( (isset($_POST['authorArray_htmlName'])) && ($_POST['authorArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('author');

				array_push($theWhereClauseArray, 'author_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['authorArray_htmlName'][0]);
      }

			if ( (isset($_POST['editorArray_htmlName'])) && ($_POST['editorArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('editor');

				array_push($theWhereClauseArray, 'editor_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['editorArray_htmlName'][0]);
      }

			// searching for a gene
			if ( (isset($_POST['genesArray_htmlName'])) && ($_POST['genesArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('gene');

				array_push($theWhereClauseArray, 'gene_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['genesArray_htmlName'][0]);
      }

			// searching for a promoter
			if ( (isset($_POST['promoterArray_htmlName'])) && ($_POST['promoterArray_htmlName'][0] != "") ) {

				$joinObject_param->addToJoinString('promoter');

				array_push($theWhereClauseArray, 'promotorGene_fk = ?');
      	array_push($theBuddingQueryArray_param, $_POST['promoterArray_htmlName'][0]);
      }

			if (isset($theWhereClauseArray)) {
				$theArraySize = count($theWhereClauseArray) - 1;
				if ($theArraySize == 0) {
					// there is one item
					$this->theWhereClauseString_prop = $theWhereClauseArray[0];
				} else {
					for ($theIndex = 0; $theIndex <= $theArraySize; $theIndex++) {
						if ($theIndex == 0) {
							$this->theWhereClauseString_prop = $theWhereClauseArray[0];
						} else {
							$this->theWhereClauseString_prop = $this->theWhereClauseString_prop . ' AND ' . $theWhereClauseArray[$theIndex];
						}
					}
				}
			}
		}

	}

	// ideal way to construct inheritance is to have a new class called GeneMultipleElementSearch
	// all those classes that have multiple elements get the isitorsearch parameter
	// check plain strains, does it inherit the same code as geneelement search?

	class ParentStrainsSearchForStrainsForStrains extends GeneMultipleElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if (isset($_POST['parentStrainsArray_htmlName'])) {
        $this->arrayToBuildFrom_prop = $_POST['parentStrainsArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
        if ($theArrayCount > 0) {
					$this->searchParameter_prop = 'strain_table.strain_ID  = ?';

					$joinObject_param->addToJoinString('parent_strain_top');
					$joinObject_param->addToJoinString('parent_strain_bottom');

					$this->IsItORSearch_prop = true;
					if (!isset($_POST['parent_chkbox_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = false;
					}
					if ($this->IsItORSearch_prop == false ) {
						$this->searchParameter_prop = 'strain_table.strain_ID '; // remove the = ?
					}
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
	}

	class GenesSearchForStrains extends GeneMultipleElementSearch {
		protected $foreignKeyTable_prop = 'allele_table';
		protected $foreignKey_prop = 'gene_fk';

		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			if(isset($_POST['genesArray_htmlName'])) {
				$this->arrayToBuildFrom_prop = $_POST['genesArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
				if ( $theArrayCount > 0) {
					$this->searchParameter_prop = 'gene_table.gene_ID  = ?';

					$joinObject_param->addToJoinString('allele_top');
					$joinObject_param->addToJoinString('allele_bottom');
					$joinObject_param->addToJoinString('gene');

					$this->IsItORSearch_prop = true;
					if (!isset($_POST['geneName_chkbox_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = false;
					}
					if ($this->IsItORSearch_prop == false ) {
						$this->searchParameter_prop = 'gene_table.gene_ID '; // removes the = ? for an AND search
					}
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
		// doesn't current allow for restricted search
	}

	class AllelesSearchForStrains extends GeneMultipleElementSearch {
		protected $foreignKeyTable_prop = 'strain_to_allele_table';
		protected $foreignKey_prop = 'allele_fk';

		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if (isset($_POST['allelesArray_htmlName'])) {
        $this->arrayToBuildFrom_prop = $_POST['allelesArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
        if ($theArrayCount > 0) {
					$this->searchParameter_prop = 'allele_table.allele_ID  = ?';

					$joinObject_param->addToJoinString('allele_top');
					$joinObject_param->addToJoinString('allele_bottom');

					$this->IsItORSearch_prop = true;
					// we need to ensure AND searches are done only when there is more than 1 item
					if (!isset($_POST['alleleName_chkbox_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = false;
					}
					if ($this->IsItORSearch_prop == false) { // we don’t want an AND
						$this->searchParameter_prop = 'allele_table.allele_ID '; // removes the = ? for an AND search
					}
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}

		public function restrictSearchClause(&$restrictClause_param, &$theBuddingQueryArray_param) {
			if(isset($_POST['alleleRestrict_chkboxID_chkbox_htmlName'])) {
				parent::restrictSearchClause($restrictClause_param, $theBuddingQueryArray_param);
			}
		}
	}

	class BalancersSearchForStrains extends GeneMultipleElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if (isset($_POST['balancersArray_htmlName'])) {
        $this->arrayToBuildFrom_prop = $_POST['balancersArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
        if ($theArrayCount > 0) {
					$this->searchParameter_prop = 'balancer_table.balancer_ID  = ?';

					$joinObject_param->addToJoinString('balancer_top');
					$joinObject_param->addToJoinString('balancer_bottom');

					$this->IsItORSearch_prop = true;
					if (!isset($_POST['balancersArray_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = false;
					}
					if ($this->IsItORSearch_prop == false ) {
						$this->searchParameter_prop = 'balancer_table.balancer_ID '; // remove the = ?
					}
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
	}

	class PlasmidsSearchForStrains extends GeneElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			if(isset($_POST['plasmidArray_htmlName'])) {
        $this->arrayToBuildFrom_prop = $_POST['plasmidArray_htmlName'];
        if ((count($this->arrayToBuildFrom_prop) > 0) && ($_POST['plasmidArray_htmlName'][0] != "")) {
					$this->searchParameter_prop = 'plasmid_table.plasmid_id  = ?';

					$joinObject_param->addToJoinString('transgene_top');
					$joinObject_param->addToJoinString('transgene_bottom');
					$joinObject_param->addToJoinString('plasmids_top');
					$joinObject_param->addToJoinString('plasmids_bottom');

					// $this->IsItORSearch_prop = false;
					// if (isset($_POST['transGenePlasmids_chkbox_htmlName'])) {
					// 	$this->IsItORSearch_prop = $_POST['transGenePlasmids_chkbox_htmlName'];
					// }
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
	}

	class CoInjectionMarkerSearchForStrains extends GeneElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			if(isset($_POST['coinjectionMarkerArray_htmlName'])) {
				$this->arrayToBuildFrom_prop = $_POST['coinjectionMarkerArray_htmlName'];
				if ((count($this->arrayToBuildFrom_prop) > 0) && ($_POST['coinjectionMarkerArray_htmlName'][0] != "")) {
					$this->searchParameter_prop = 'coinjection_marker_table.coInjectionMarker_id  = ?';

					$joinObject_param->addToJoinString('transgene_top');
					$joinObject_param->addToJoinString('transgene_bottom');
					$joinObject_param->addToJoinString('coinjection');

					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
	}

	// each class has a search parameter property for its search, here it's 'transgene_table.chromosomeName_col  = ?'
	// we assign it to the whereclause string and push its value, such as $_POST['chromosomeTransGenes_htmlName'], to the budding query array

	class TransGenesChromosomesSearchForStrains extends GeneElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if ((isset($_POST['chromosomeTransGenes_htmlName'])) && ($_POST['chromosomeTransGenes_htmlName'] != "")) {
				$this->searchParameter_prop = 'transgene_table.chromosomeName_col  = ?';

				$joinObject_param->addToJoinString('transgene_top');
				$joinObject_param->addToJoinString('transgene_bottom');

				$this->theWhereClauseString_prop = $this->searchParameter_prop;

				array_push($theBuddingQueryArray_param, $_POST['chromosomeTransGenes_htmlName']);

				// parent constructor is not called here, why?
				// this might because it's array of 1 item and it's easier to just simply call array_push
				// the where clause is also simply assigned
			}
		}
	}

	class TransGenesSearchForStrains extends GeneMultipleElementSearch {
		protected $foreignKeyTable_prop = 'strain_to_transgene_table';
		protected $foreignKey_prop = 'transgene_fk';

		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			if(isset($_POST['transgeneArray_htmlName'])) {
				// this is the array of transgenes we are searching for
				$this->arrayToBuildFrom_prop = $_POST['transgeneArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
				if ($theArrayCount > 0) {
					$this->searchParameter_prop = 'transgene_table.transgene_id  = ?';

					$joinObject_param->addToJoinString('transgene_top');
					$joinObject_param->addToJoinString('transgene_bottom');

					$this->IsItORSearch_prop = true;
					if (!isset($_POST['transgeneName_chkbox_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = false;
					}
					if ($this->IsItORSearch_prop == false ) {
						$this->searchParameter_prop = 'transgene_table.transgene_id '; // remove the = ?
					}
					// why doesn't this call the array push?
					// constructor does this
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}

		public function restrictSearchClause(&$restrictClause_param, &$theBuddingQueryArray_param) {
			if(isset($_POST['transgeneRestrict_chkboxID_chkbox_htmlName'])) {
				parent::restrictSearchClause($restrictClause_param, $theBuddingQueryArray_param);
			}
		}
	}

	class CommentsSearch extends GeneElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if ((isset($_POST['comment_htmlName'])) && ($_POST['comment_htmlName'] != "")) {

				// we are taking the words of the comment and turning into a an itemized array from
				$this->arrayToBuildFrom_prop = explode(" ",$_POST['comment_htmlName']);

				// buildtheWHEREclause is overriden, so it just does an "OR search and it appends the likes"
				parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
			}
		}

		public function buildElementWhereClause(&$theBuddingQueryArray_param, &$theHavingCountArray) {
			$theArraySize = count ($this->arrayToBuildFrom_prop) - 1;
		  // if the array has one element
		  if ($theArraySize == 0) {

		    $this->theWhereClauseString_prop = $this->searchParameter_prop;

		    // double slash b along the REGEXP_LIKE for searching for words
				array_push($theBuddingQueryArray_param, "\\b". $this->arrayToBuildFrom_prop[0] . "\\b");

		  } else  {
				$this->theWhereClauseString_prop = "( ";
		    for ($theIndex = 0 ;  $theIndex <= $theArraySize; $theIndex++) {
		      if ($theIndex == 0) {
		         $this->theWhereClauseString_prop = $this->theWhereClauseString_prop . $this->searchParameter_prop;
		      } else {
		        $this->theWhereClauseString_prop = $this->theWhereClauseString_prop . ' OR ' . $this->searchParameter_prop;
		      }
					// double slash b along the REGEXP_LIKE for searching for words
		      array_push($theBuddingQueryArray_param, "\\b". $this->arrayToBuildFrom_prop[$theIndex] . "\\b");
		    }
		    // outside the loop, close it off
		    $this->theWhereClauseString_prop = $this->theWhereClauseString_prop . " ) ";
		  }
		}

		public function concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause_param) {
				return $this->concatElementWhereClauseToMasterWhereClauseInternal($thePrimaryWhereClause_param,' OR ');
		}
	}

	class StrainCommentsSearchForStrains extends CommentsSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if ((isset($_POST['comment_htmlName'])) && ($_POST['comment_htmlName'] != "")) {
				$this->searchParameter_prop = 'REGEXP_LIKE(truestrain_table.comments_col, ?)';

				// buildtheWHEREclause is overriden, so it just does an "OR search and it appends the likes"
				parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
			}
		}
	}

	// is comments_col correct?; notice this search is via plasmids and not strains
	class PlasmidCommentsSearchForPlasmid extends CommentsSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if ((isset($_POST['comment_htmlName'])) && ($_POST['comment_htmlName'] != "")) {
				$this->searchParameter_prop = 'REGEXP_LIKE(comments_col, ?)';

				// buildtheWHEREclause is overriden, so it just does an "OR search and it appends the likes"
				parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
			}
		}
	}

	class GeneCommentsSearchForStrains extends CommentsSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			//if (isset($_POST['parent_chkbox_htmlName'])) { // why do we check this?
			if ( (isset($_POST['comment_htmlName'])) && ($_POST['comment_htmlName'] != "") && (!(isset($_POST['strainsOnly_chkbox_htmlName'])))) {
				$this->searchParameter_prop = 'REGEXP_LIKE(gene_table.comments_col, ?)';

				$joinObject_param->addToJoinString('allele_top');
      	$joinObject_param->addToJoinString('allele_bottom');
      	$joinObject_param->addToJoinString('gene');

				// buildtheWHEREclause is overriden, so it just does an "OR search and it appends the likes"
				parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
			}
		//	}
		}
	}

	class AlleleCommentsSearchForStrains extends CommentsSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if ((isset($_POST['comment_htmlName'])) && ($_POST['comment_htmlName'] != "") && (!(isset($_POST['strainsOnly_chkbox_htmlName'])))) {
				$this->searchParameter_prop = 'REGEXP_LIKE(allele_table.comments_col, ?)';

				$joinObject_param->addToJoinString('allele_top');
      	$joinObject_param->addToJoinString('allele_bottom');

				// buildtheWHEREclause is overriden, so it just does an "OR search and it appends the likes"
				parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
			}
		}
	}

	class TransgeneCommentsSearchForStrainsForStrains extends CommentsSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {

			if ((isset($_POST['comment_htmlName'])) && ($_POST['comment_htmlName'] != "") && (!(isset($_POST['strainsOnly_chkbox_htmlName'])))) {
				$this->searchParameter_prop = 'REGEXP_LIKE(transgene_table.comments_col, ?)';

				$joinObject_param->addToJoinString('transgene_top');
				$joinObject_param->addToJoinString('transgene_bottom');

				// buildtheWHEREclause is overriden, so it just does an "OR search and it appends the likes"
				parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
			}
		}
	}

	class AntibioticsSearchForPlasmids extends GeneMultipleElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			if(isset($_POST['antibioticArray_htmlName'])) {
				$this->arrayToBuildFrom_prop = $_POST['antibioticArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
				if ( $theArrayCount> 0) {
					$this->searchParameter_prop = 'antibiotic_table.antibiotic_id  = ?';

					$joinObject_param->addToJoinString('antibiotic_top');
					$joinObject_param->addToJoinString('antibiotic_bottom');

					$this->IsItORSearch_prop = true;
					if (!isset($_POST['antibioticArray_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = $_POST['antibioticArray_htmlName'];
					}
					if ($this->IsItORSearch_prop == false ) {
						$this->searchParameter_prop = 'antibiotic_table.antibiotic_id '; // remove the = ?
					}
					// why doesn't this call the array push
					// constructor does this
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
	}

	class FluoroTagSearchForPlasmids extends GeneMultipleElementSearch {
		public function __construct(&$joinObject_param, &$theBuddingQueryArray_param, &$theHavingCountArray) {
			if(isset($_POST['fluorotagArray_htmlName'])) {
				$this->arrayToBuildFrom_prop = $_POST['fluorotagArray_htmlName'];
				$theArrayCount = count($this->arrayToBuildFrom_prop);
				if ($theArrayCount > 0) {
					$this->searchParameter_prop = 'fluoro_tag_table.fluoroTag_id  = ?';

					$joinObject_param->addToJoinString('plasmid_to_fluoro__top');
					$joinObject_param->addToJoinString('plasmid_to_fluoro__bottom');

					$this->IsItORSearch_prop = true;
					if (!isset($_POST['fluoroTag_chkbox_htmlName']) && $theArrayCount > 1) {
						$this->IsItORSearch_prop = $_POST['fluoroTag_chkbox_htmlName'];
					}
					if ($this->IsItORSearch_prop == false ) {
						$this->searchParameter_prop = 'fluoro_tag_table.fluoroTag_id '; // remove the = ?
					}
					// why doesn't this call the array push
					// constructor does this
					parent::__construct($joinObject_param, $theBuddingQueryArray_param, $theHavingCountArray);
				}
			}
		}
	}

	function buildGroupByHavingClause($inHavingCountArray,&$theBuddingQueryArray_param, &$outGroupByHavingClause) {
		$outGroupByHavingClause = "";
		$theArraySize = count ($inHavingCountArray) - 1;
		// if $theArraySize > 1 then we having at least one having clause
		if ($theArraySize > - 1) { // if array size is 1, theArraySize is 0. 0 and above should trigger the following code
			$outGroupByHavingClause = "GROUP BY truestrain_table.strain_id HAVING "; //plasmids don't have an AND search; do we just turn on the OR search for them?
			$theHavingCountArrayKeys = array_keys($inHavingCountArray);
			// assumes keys are indexed the same as the actual values
			//echo "<br>inHavingCountArray size is ". $theArraySize ."<br>";
			for ($theIndex = 0; $theIndex <= $theArraySize; $theIndex++) {

				$outGroupByHavingClause = $outGroupByHavingClause . "(COUNT(DISTINCT " . $theHavingCountArrayKeys[$theIndex]. ") = ?)";
				$theHavingCountArrays_Keys = array_keys($inHavingCountArray);
				// will this work in plural? is the order preserved?
				array_push($theBuddingQueryArray_param, $inHavingCountArray[$theHavingCountArrays_Keys[$theIndex]]);

				// if there are more elements, then we need an AND
				if ($theIndex < $theArraySize) {
					$outGroupByHavingClause = $outGroupByHavingClause . " AND ";
				}
			}
		}
	}

	function searchDatabaseForStrains() {

		if (isset($_POST['allStrains_chkbox_htmlName']) && $_POST['allStrains_chkbox_htmlName']) {
			// display all strains
			$searchDatabase = new Peri_Database();
			// here's where the parent strain is being aliased
			$theSelectString = "SELECT truestrain_table.strain_id FROM strain_table as truestrain_table";
			$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);
			$preparedSQLQuery_prop->execute();
			return ($preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC));
		} else {

			$theSelectString = "";
			$thePrimaryJoinClause = "";
			$thePrimaryWhereClause = "";
			$theCommentJoinClause = "";
			$theCommentWhereClause = "";
			$theBuddingQueryArray = array();
			$theHavingCountArray = array(); //how do we make this a 2d array?

			// what we are trying to do here is collect all the elements associated with strains
			// and then start building a where clause, starting with the comments
			// we start with a left join because not every strain will have every element and we don't want
			// to miss any. That is, if a strain has an allele associated with it, but not a transgene and
			// we inner join with transgene, we won’t get all the other strains with that allele.
			// we only need to do this for comments because comments can be found in genes, alleles and transgenes

			// left joins brings in the various associated tables for comments in the strain itself, the genes, alleles and transgenes
			// we use left join and not inner join in case some strains don’t have all these elements.
			// also notice that the search is building up thewhereclause

			//constructors take care of building the query array which are the values of the where clause
			// concatElementWhereClauseToMasterWhereClause takes care of building the where query string (note there are no values here;
			// they appear in the query array)

			$leftJoinObject = new LeftJoinerForStrains();

			$theCommentSearchObject = new StrainCommentsSearchForStrains($leftJoinObject, $theBuddingQueryArray, $theHavingCountArray);

			$theCommentWhereClause = $theCommentSearchObject->concatElementWhereClauseToMasterWhereClause($theCommentWhereClause);

			if (!isset($_POST['strainsOnly_chkbox_htmlName'])) {
				$theCommentSearchObject = new GeneCommentsSearchForStrains($leftJoinObject, $theBuddingQueryArray, $theHavingCountArray);
				$theCommentWhereClause = $theCommentSearchObject->concatElementWhereClauseToMasterWhereClause($theCommentWhereClause);

				$theCommentSearchObject = new AlleleCommentsSearchForStrains($leftJoinObject, $theBuddingQueryArray, $theHavingCountArray);
				$theCommentWhereClause = $theCommentSearchObject->concatElementWhereClauseToMasterWhereClause($theCommentWhereClause);

				$theCommentSearchObject = new TransgeneCommentsSearchForStrainsForStrains($leftJoinObject, $theBuddingQueryArray, $theHavingCountArray);
				$theCommentWhereClause = $theCommentSearchObject->concatElementWhereClauseToMasterWhereClause($theCommentWhereClause);

				//I just noticed there is no balancer search here for balancer comments
			}
			// builds a join clause to include comment elements from the various elements that might be
			// associated with a strain.

			// $theCommentJoinClause has no entries when we're searching for just strains, so $theCommentJoinClause will always be blank!

			$theCommentJoinClause = $leftJoinObject->getJoinString();

			// if $theCommentJoinClause has something in it, set up a dedicated search for it.
			// This comment may be a part of a gene, allele, or transgene that is associated with some strains
			if ($theCommentJoinClause != "") {
				$theSelectString = $theSelectString . "SELECT DISTINCT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . $theCommentJoinClause . " WHERE " . $theCommentWhereClause;
			} else {
				if (isset($_POST['strainsOnly_chkbox_htmlName'])) {
					$theSelectString = $theSelectString . "SELECT DISTINCT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . " WHERE " . $theCommentWhereClause;
				}
			}

			// now we are doing a straight up AND search, so we can do an inner join
			$innerJoinObject = new InnerJoinerForStrains();

			$theStrainSearchObject = new StrainsSearchForStrains($innerJoinObject,$theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theStrainSearchObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$theParentStrainsObject = new ParentStrainsSearchForStrainsForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theParentStrainsObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			// gene search moved down below

			$theAllelesObject = new AllelesSearchForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theAllelesObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			
			$theBalancersObject = new BalancersSearchForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theBalancersObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$thePlasmidsObject = new PlasmidsSearchForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $thePlasmidsObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$theCoInjectionMarkerObject = new CoInjectionMarkerSearchForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theCoInjectionMarkerObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$theTransGeneChromosomeObject = new TransGenesChromosomesSearchForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theTransGeneChromosomeObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			
			// here is where the action is for transgenes
			$theTransGeneObject = new TransGenesSearchForStrains($innerJoinObject, $theBuddingQueryArray, $theHavingCountArray);
			$thePrimaryWhereClause = $theTransGeneObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$thePrimaryJoinClause = $innerJoinObject->getJoinString();


			// searching for an allele will generally exclude any search for a gene
			// so we search for genes separately and intersect it with any prior search
			// if we are searching only for genes, then this prior search will be blank.

			// problem code above won't see any entries for a gene search since it's down herre
			// buddingqueryaray is also here
			// we need to move this search up there 
			// and we need genequeryarray
			// to which we append to the query array 

			$geneJoinObject = new InnerJoinerForStrains();
			$theHavingCountArrayForGenes = array();
			$outGroupByHavingClauseForGenes = "";
			$theGeneQueryArray = array();
			$theGenesWhereClause = "";
			$theGenesObject = new GenesSearchForStrains($geneJoinObject, $theGeneQueryArray, $theHavingCountArrayForGenes);
			$theGenesWhereClause = $theGenesObject->concatElementWhereClauseToMasterWhereClause($theGenesWhereClause);
			$theGeneJoinClause = $geneJoinObject->getJoinString();
			buildGroupByHavingClause($theHavingCountArrayForGenes,$theGeneQueryArray, $outGroupByHavingClauseForGenes);


			$searchState = 'searchForNothing';
			//$theSelectString not empty means there is a comments search in play
			// a not empty $thePrimaryJoinClause  means there is a search for something else also in play
			// a not empty $thePrimaryWhereClause means we are searching for something in the strain itself ($thePrimaryJoinClause is empty)
			if ( ($theSelectString != "") && ($thePrimaryJoinClause != "") ) {
				$searchState = 'searchForEverything';
			} else if ($theSelectString != "") {
				$searchState = 'searchForCommentsOnly';
			} else if ($thePrimaryJoinClause != "") {
				$searchState = 'searchForSomethingElse';
			} else if ($thePrimaryWhereClause != "") {
				$searchState = 'searchForJustStrains';
			}

			if ($searchState == 'searchForNothing') {
				// search is still search for nothing, check if there's a gene's search
				if ($theGenesWhereClause != "") {
					$searchState = 'searchForSomethingElse';
				}
			}

			switch($searchState) {
				case 'searchForJustStrains':
					// here we are searching for JUST strain parameters
					// that is there is no join clause

					$theSelectString = "SELECT DISTINCT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . " WHERE " . $thePrimaryWhereClause . " ORDER BY truestrain_table.strainName_col";
					break;
				case 'searchForCommentsOnly':
					// $theSelectString already has the search string built for us, so nothing else to do here; this is also true for when limiting search to strain comments
					break;
				case 'searchForSomethingElse':

					// here we will add buildNotInClause
					// how we have three restrictSearchClauses here,
					$restrictSearchClause = "";
					// one for alleles and transgenes
					$theAllelesObject->restrictSearchClause($restrictSearchClause,$theBuddingQueryArray);
			
					$theTransGeneObject->restrictSearchClause($restrictSearchClause,$theBuddingQueryArray);

					// adds key to group by and values for ? to buddingqueryarray
					buildGroupByHavingClause($theHavingCountArray,$theBuddingQueryArray, $outGroupByHavingClause);

					// if we are searching genes too, then we are conducting an intersecting search and order by can’t be used in the first portion of the select
					$orderBy = "";
					if (mb_strlen($theGenesWhereClause) == 0) {
						$orderBy = " ORDER BY truestrain_table.strainName_col";
					}

					// main search excluding genes
					$theSelectString = "SELECT DISTINCT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . $thePrimaryJoinClause . " WHERE " . $thePrimaryWhereClause . $restrictSearchClause. $outGroupByHavingClause . $orderBy;


					// intersect is a new MySQL feature to intersect the results of two separate select searches
					$intersect = "";
					// if we are searching both genes and something else, set up for intersection

					if ((mb_strlen($thePrimaryWhereClause) > 0) && (mb_strlen($theGenesWhereClause) > 0)) {

						$theBuddingQueryArray = array_merge($theBuddingQueryArray, $theGeneQueryArray);

						// "order by" is global to the intersected search
						$theSelectString = $theSelectString  . ' INTERSECT ' . "SELECT DISTINCT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . $theGeneJoinClause . " WHERE " . $theGenesWhereClause . $outGroupByHavingClauseForGenes . " ORDER BY strainName_col";
					
					} elseif (mb_strlen($theGenesWhereClause) > 0) {
						$theBuddingQueryArray = $theGeneQueryArray;

						$theSelectString = $intersect . "SELECT DISTINCT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . $theGeneJoinClause . " WHERE " . $theGenesWhereClause . $outGroupByHavingClauseForGenes . " ORDER BY truestrain_table.strainName_col";
					}

					// if we are at least searching genes, set up the select for it
					

					// it looks we just need to append after the where clause (and be before having, and order clauses)
					// need to really confirm we want to be before having

					/*echo "theGenesWhereClause " . $theGenesWhereClause . "<br>";
					echo "theGeneJoinClause " . $theGeneJoinClause . "<br>";

echo "<br>";
echo "<br>";
					echo "theSelectString " . $theSelectString . "<br>";

					echo "theBuddingQueryArray "  . "<br>";
					var_dump($theBuddingQueryArray);
					echo "<br>";*/

					break;

				case 'searchForEverything':
					// apparently we don’t need union distinct here
					$theSelectString = $theSelectString . " UNION SELECT truestrain_table.strain_id, truestrain_table.strainName_col FROM strain_table as truestrain_table " . $thePrimaryJoinClause . " WHERE " . $thePrimaryWhereClause . " ORDER BY strainName_col";
					break;
			}

			if ($searchState != 'searchForNothing') {
				$searchDatabase = new Peri_Database();

				$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);

				$preparedSQLQuery_prop->execute($theBuddingQueryArray);

				$theResult = $preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC);

				return ($theResult);
			} else {
				return false;
			}

		}
	}

	function searchDatabaseForPlasmids() {

		if (isset($_POST['allPlasmids_chkbox_htmlName']) && $_POST['allPlasmids_chkbox_htmlName']) {
			// display all plasmids
			$searchDatabase = new Peri_Database();
			$theSelectString = "SELECT plasmid_id FROM plasmid_table";
			$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);
			$preparedSQLQuery_prop->execute();
			return ($preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC));
		} else {

			$theSelectString = "";
			$thePrimaryJoinClause = "";
			$thePrimaryWhereClause = "";
			$theCommentJoinClause = "";
			$theCommentWhereClause = "";
			$theBuddingQueryArray = array();
			$theHavingCountArray = array();

			// $leftJoinObject is not used
			//$leftJoinObject = new LeftJoinerForStrains();

			// pass NULL for leftjoin which is not used since plasmid comments are only the comments we are searching for
			// and they are part of plasmids
			$theUnusedJoinReference = "";
			$theCommentSearchObject = new PlasmidCommentsSearchForPlasmid($theUnusedJoinReference, $theBuddingQueryArray,$theHavingCountArray);
			$theCommentWhereClause = $theCommentSearchObject->concatElementWhereClauseToMasterWhereClause($theCommentWhereClause);

			// $theCommentJoinClause is not used here
			//$theCommentJoinClause = $leftJoinObject->getJoinString();

			if ($theCommentWhereClause != "") {
				$theSelectString = $theSelectString . "SELECT DISTINCT plasmid_id, plasmidName_col FROM plasmid_table " . " WHERE " . $theCommentWhereClause;
			}

			// now we are doing a straight up AND search, so we can do an inner join
			$innerJoinObject = new InnerJoinerForPlasmids();


			$thePlasmidSearchObject = new PlasmidSearchForPlasmids($innerJoinObject,$theBuddingQueryArray,$theHavingCountArray);
			$thePrimaryWhereClause = $thePlasmidSearchObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$theAntibioticsObject = new AntibioticsSearchForPlasmids($innerJoinObject, $theBuddingQueryArray,$theHavingCountArray);
			$thePrimaryWhereClause = $theAntibioticsObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			// so far only fluorotags can have multiple elements (with OR)
			$theFluroTagObject = new FluoroTagSearchForPlasmids($innerJoinObject, $theBuddingQueryArray,$theHavingCountArray);
			$thePrimaryWhereClause = $theFluroTagObject->concatElementWhereClauseToMasterWhereClause($thePrimaryWhereClause);

			$thePrimaryJoinClause = $innerJoinObject->getJoinString();

			$searchState = 'searchForNothing';
			//$theSelectString not empty means there is a comments search
			// $thePrimaryJoinClause not empty means there is a search for something else
			if ( ($theSelectString != "") && ($thePrimaryJoinClause != "") ) {
				$searchState = 'searchForEverything';
			} else if ($theSelectString != "") {
				$searchState = 'searchForCommentsOnly';
			} else if ($thePrimaryJoinClause != "") {
				$searchState = 'searchForSomethingElse';
			} else if ($thePrimaryWhereClause != "") {
				$searchState = 'searchForJustPlasmids';
			}

			switch($searchState) {
				case 'searchForJustPlasmids':
					// here we are searching for JUST strain parameters
					// that is there is no join clause
					$theSelectString = "SELECT DISTINCT plasmid_id, plasmidName_col FROM plasmid_table " . " WHERE " . $thePrimaryWhereClause . " ORDER BY plasmid_table.plasmidName_col";

					break;
				case 'searchForCommentsOnly':
					break;
				case 'searchForSomethingElse':
					$theSelectString = "SELECT DISTINCT plasmid_id, plasmidName_col FROM plasmid_table " . $thePrimaryJoinClause . " WHERE " . $thePrimaryWhereClause . " ORDER BY plasmid_table.plasmidName_col";
					break;

				case 'searchForEverything':
					$theSelectString = $theSelectString . " UNION SELECT DISTINCT plasmid_id, plasmidName_col FROM plasmid_table " . $thePrimaryJoinClause . " WHERE " . $thePrimaryWhereClause . " ORDER BY plasmidName_col";
					break;
			}

			if ($searchState != 'searchForNothing') {
				$searchDatabase = new Peri_Database();

				$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);

				$preparedSQLQuery_prop->execute($theBuddingQueryArray);

				$theResult = $preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC);
				return ($theResult);
			} else {
				echo "we are in the wrong place<br>";
				return false;
			}

		}
	}
