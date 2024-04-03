<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html>
<head>
	<title>Peri Database Test 1</title>
	<meta charset="utf-8">

	<link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
	<script src="/js/jquery.min.js"></script>
	<script src="/js/bootstrap/js/bootstrap.min.js"></script>
	<script src="/js/start-javascript.js"></script>

	<style>
		h6 {
			margin-bottom:-10px;
		}
	</style>

	<script>
      $( document ).ready(function()
      {
        handleStrainButtons();
      });
    </script>

 </head>
 <body class="bg-light">
 	<div class="container">
		<div class="py-5 text-center">
			<img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
      <h2>KurshanLab Strain Database</h2>
      <p class="lead">Home</p>
    </div>
	<div class="container">
		
		<form class="needs-validation" action="" method="post">

			<div class="row">
				<div class="col-md-2 mb-3">
					<h6>main stuff</h6>
				</div>
			</div>

			<div class="row">

				<div class="col-md-2 mb-3">
					<input type="submit" name='newStrain_htmlName' class="btn btn-primary btn-block form-control" value="Add Strain" formaction="../strains/edit_strain.php" alt="Add Strain"/>
				</div>

				<div class="col-md-2 mb-3">
					<input type="submit" name='newPlasmid_htmlName' class="btn btn-primary btn-block" formaction="../plasmids/edit_plasmid.php"value="Add Plasmid" alt="Add Plasmid"/>
				</div>

			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<h6>foundational stuff</h6>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<input type="submit" name='newGene_htmlName' class="btn btn-primary btn-block form-control" formaction="../genes/edit_gene.php" value="Add Gene" alt="Gene"/>
				</div>

				<div class="col-md-2 mb-3">
					<input type="submit" name='newAllele_htmlName' class="btn btn-primary btn-block form-control" formaction="../alleles/edit_allele.php" value="Add Allele" alt="Add Allele"/>
				</div>

				<div class="col-md-2 mb-3">
					<input type="submit" name='newTransgene_htmlName' class="btn btn-primary btn-block form-control" formaction="../transgenes/edit_transgene.php" value="Add Transgene" alt="Add Transgene"/>
				</div>

				<div class="col-md-2 mb-3">
					<input type="submit" name='newBalancer_htmlName' class="btn btn-primary btn-block form-control" formaction="../balancers/edit_balancer.php" value="Add Balancer" alt="Add Balancer"/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<h6>more stuff</h6>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<input type="submit" name='newContributor_htmlName' class="btn btn-primary btn-block" formaction="../single-element/edit_singleElement.php" value="Add Contributor" alt="Add Contributor"/>
				</div>
				<div class="col-md-3 mb-3">
					<input type="submit" name='newCoInjection_htmlName' class="btn btn-primary btn-block" formaction="../single-element/edit_singleElement.php" value="Add Co-injection Marker" alt="Add CoInjection Marker"/>
				</div>
				<div class="col-md-2 mb-3" style="margin-left:-70px">
					<input type="submit" name='newAntibioticResistance_htmlName' class="btn btn-primary btn-block" formaction="../single-element/edit_singleElement.php" value="Add Antibiotic" alt="Add Antibiotic"/>
				</div>
				<div class="col-md-2 mb-3" style="margin-left:-20px">
					<input type="submit" name='newFluoro_htmlName'class="btn btn-primary btn-block" formaction="../single-element/edit_singleElement.php" value="Add Fluor/Tag" alt="Add Fluor/Tag"/>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<h6>search</h6>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<input type="submit" name='search_htmlName' class="btn btn-primary btn-block" formaction="../search/search_strains.php" value="Search Strains" alt="Search Strains"/>
				</div>
				<div class="col-md-2 mb-3">
					<input type="submit" name='searchPlasmids_htmlName' class="btn btn-primary btn-block" formaction="../search/search_plasmids.php" value="Search Plasmids" alt="Search Plasmids"/>
				</div>
			</div>
			<?php
				//this field will show only for users designated as editors
				require_once('../classes/classes_database.php');

				$userObject = new User("","",""); // we don’t need to assign any variables here; we just need it to query the database author table
				if ($userObject->IsCurrentUserAnEditor()) {
					echo "<div class='row'>";
						echo "<div class='col-md-2 mb-3'>";
							echo "<h6>edit</h6>";
						echo "</div>";
					echo "</div>";

					echo "<div class='row'>";
						echo "<div class='col-md-2 mb-3'>";
							echo "<input type='submit' name='edit_htmlName' class='form-control btn btn-primary btn-block' formaction='lister.php' value='Edit Entries' alt='Edit Entries'/>";
						echo "</div>";

					echo "</div>";
				}
			?>

			<!-- table goes here to display last vialed strains -->
			<div class="row">
				<div class="col-md-12 mb-3">
					<?php
						$theSelectString = "SELECT strain_table.strain_id FROM strain_table WHERE strain_table.isLastVial_col = ? ORDER BY strain_table.dateThawed_col asc";

						$searchDatabase = new Peri_Database();

						$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);
						$theQueryArray = [1]; // this is the ? above. If it’s 1, it’s true
						$preparedSQLQuery_prop->execute($theQueryArray);
						$theSearchResult = $preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC);
						if (count($theSearchResult) > 0 ) {

							echo "<label>thawed strains</label>";
							echo "<table class='table table-striped table-hover table-bordered twelvepoints' id='thawed-table'>";
								echo "<th class='font-weight-bold'>strain name</th>";
								echo "<th class='font-weight-bold'>date thawed</th>";
								echo "<th class='font-weight-bold'>thawed by</th>";

								require_once("../classes/classes_load_elements.php");
								$theStrainClass = new LoadParentStrains();

								foreach ($theSearchResult as $theStrainID) {

									// BUGfixed, was missing row entry, 2024_02_13
									echo "<tr>";

									$theStrainArray = $theStrainClass->returnSpecificRecord($theStrainID['strain_id']);
									$theStrainName = htmlspecialchars($theStrainArray['strainName_col'],ENT_QUOTES);
									echo "<td>$theStrainName</td>";

									$dateThawed = "";
									if (isset($theStrainArray['dateThawed_col']) && $theStrainArray['dateThawed_col'] != "") {
										$dateThawed = htmlspecialchars($theStrainArray['dateThawed_col'],ENT_QUOTES);
									}
									echo "<td>$dateThawed</td>";

									// this needs to do a lookup
									$theContributorObject = new LoadContributor();
									$theContributorString = "";
									$theActualContributor = $theStrainArray['lastVialContributor_fk'];

									if ((isset($theActualContributor)) && ($theActualContributor != 0) && ($theActualContributor != NULL) ) {
										$theContributorArray = $theContributorObject->returnSpecificRecord($theActualContributor);
										$theContributorString = htmlspecialchars($theContributorArray['contributorName_col'],ENT_QUOTES);
									}
									echo "<td>$theContributorString</td>";

									echo "</tr>";

								}
							echo "</table>";
							
						} else {
							echo "<label>there are no thawed strains</label>";
						}
					?>
				</div>
			</div>

			<!-- when we click a button on the screen, it assigns the OK button of this dialog its information (strain id, etc) -->
		    <dialog id="confirmation_dialog">
		        <h2>Confirmation</h2>
		        <p id="confirmation_string"></p>
		        <button id="cancel-button">Cancel</button>
		        <button id="ok-button">OK</button>
		    </dialog>

			<!-- for regular users, table goes here to display strains not yet handed off -->
			<div class="row">
				<div class="col-md-12 mb-3">
					<?php
						$userObject = new User("","",""); // we don’t need to assign any variables here; we just need it to query the database author table
						if ($userObject->IsCurrentUserAnEditor()) {
							// we had no choice but to hard code is null, binding a null value does not work
							
							// EXCLUDE MOVED STRAINS

							$theSelectString = "SELECT strain_table.strain_id FROM strain_table WHERE strain_table.dateHandedOff_col IS NOT NULL AND strain_table.dateMoved_col IS NULL ORDER BY strain_table.strainName_col asc";

							$searchDatabase = new Peri_Database();

							$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);
							$preparedSQLQuery_prop->execute();
							$theSearchResult = $preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC);

							if (count($theSearchResult) > 0 ) {

								echo "<label>handed off strains waiting to be processed further</label>";
								echo "<table class='table table-striped table-hover table-bordered twelvepoints' id='processstrains-table'>";
									echo "<th class='font-weight-bold'>strain name</th>";
									echo "<th class='font-weight-bold'>frozen?</th>";
									echo "<th class='font-weight-bold'>survived?</th>";
									echo "<th class='font-weight-bold'>move to final destination</th>";

									require_once("../classes/classes_load_elements.php");
									$theStrainClass = new LoadParentStrains();

									$theRowNumber = 1; // starting with 1, not 0

									foreach ($theSearchResult as $theStrainID) {

										// we track row numbers so we know which one to delete when handed off
										echo "<tr id=$theRowNumber>";

										$theStrainArray = $theStrainClass->returnSpecificRecord($theStrainID['strain_id']);
										$theStrainName = htmlspecialchars($theStrainArray['strainName_col'],ENT_QUOTES);
										$theStrainID = htmlspecialchars($theStrainArray['strain_id'],ENT_QUOTES);
										if ($theStrainArray['dateFrozen_col'] != null) {
											$theFrozenDate = htmlspecialchars($theStrainArray['dateFrozen_col'],ENT_QUOTES);
										} else {
											$theFrozenDate = "";
										}
										if ($theStrainArray['dateSurvived_col'] != null) {
											$theSurvivalDate = htmlspecialchars($theStrainArray['dateSurvived_col'],ENT_QUOTES);
										} else {
											$theSurvivalDate = "";
										}
										
										echo "<td>$theStrainName</td>";

										echo "<td>";
											$theFrozenState = ($theFrozenDate != null);
            					$frozenButtonID = "frozen-button-row-" . $theRowNumber . "-strainid-" . $theStrainID . "-strain_name-" . $theStrainName;
            					$frozenDateID = "frozen-date-row-" . $theRowNumber . "-strainid-" . $theStrainID . "-strain_name-" . $theStrainName;
            					$theSurvivalState = ($theSurvivalDate != null);
											$survivalButtonID = "survival-button-row-" . $theRowNumber . "-strainid-" . $theStrainID . "-strain_name-" . $theStrainName;
											$survivalDateID = "survival-date-row-" . $theRowNumber . "-strainid-" . $theStrainID . "-strain_name-" . $theStrainName;

											$theFrozenButtonState = "";
											$theSurvivalButtonEnabledState = "";
											$theMoveButtonEnabledState = "";

							        if ($theFrozenState == false) {
							           $theSurvivalButtonEnabledState = "disabled";
							           $theMoveButtonEnabledState = "disabled";
							        }
							        if ($theSurvivalState) {
							            $theFrozenButtonState = "disabled";
							        } else {
							           	$theMoveButtonEnabledState = "disabled";
							        }

            					// we use a class because there are multiple buttons on this page
											echo "<div class='form-check'>";
											  echo "<div class='custom-control custom-checkbox'>";
											    if ($theFrozenState) {
											      echo "<input type='checkbox' class='form-check-input frozen' checked $theFrozenButtonState id=$frozenButtonID>";
											    } else {
											      echo "<input type='checkbox' class='form-check-input frozen' $theFrozenButtonState id=$frozenButtonID>";
											    }
											    echo "<label class='form-check-label' for='$frozenButtonID'>Frozen?</label>";
											  echo "</div>";
											echo "</div>";
											echo "<span id=$frozenDateID>$theFrozenDate</span>"; 

										echo "</td>";

										echo "<td>";
											echo "<div class='form-check'>";
			                  					echo "<div class='custom-control custom-checkbox'>";
			                  						if ($theSurvivalState) {
			                  							echo "<input type='checkbox' class='form-check-input survival' $theSurvivalButtonEnabledState checked id=$survivalButtonID>";
			                  						} else {
			                  							echo "<input type='checkbox' class='form-check-input survival' $theSurvivalButtonEnabledState id=$survivalButtonID>";
			                  						}
													echo "<label class='form-check-label' for='customCheck1'>Survived?</label>";
												echo "</div>";
											echo "</div>";
											echo "<span id=$survivalDateID>$theSurvivalDate</span>"; 
										echo "</td>";

										echo "<td>";
										$finalDestinationButtonID = "finaldestination-button-row-" . $theRowNumber . "-strainid-" . $theStrainID . "-strain_name-" . $theStrainName;
										echo "<button type='button' id=$finalDestinationButtonID class='btn btn-outline-info btn-sm finaldestination' $theMoveButtonEnabledState>final move...</button>";
										echo "</td>";

										echo "</tr>";
										$theRowNumber = $theRowNumber + 1;

									}
								echo "</table>";
								
							} else {
								echo "<label>there are no strains waiting to be processed</label>";
							}
						}

					echo "</div>";
				echo "</div>";
				echo "<div class='row'>";
					echo "<div class='col-md-12 mb-3'>";

						// we had no choice but to hard code is null, binding a null value does not work
						$theSelectString = "SELECT strain_table.strain_id FROM strain_table WHERE strain_table.dateHandedOff_col IS NULL AND strain_table.author_fk = ? ORDER BY strain_table.strainName_col asc";

						$searchDatabase = new Peri_Database();

						$preparedSQLQuery_prop = $searchDatabase->sqlPrepare($theSelectString);
						
						// Start output buffering
						ob_start();
						// Print the session array
						print_r($_SESSION);
						// Capture the output
						$sessionContents = ob_get_clean();
						// Log it to the PHP error log
						error_log("Session Contents: " . $sessionContents);


						$preparedSQLQuery_prop->execute([$_SESSION['user']]); // we show only those not handed off strains created by the current user

						//$nullDate = null;
						//$preparedSQLQuery_prop->bindParam(1, $nullDate, PDO::PARAM_NULL);
						//$theQueryArray = [null]; // this is the ? above.
						//$preparedSQLQuery_prop->execute($theQueryArray);
						
						$theSearchResult = $preparedSQLQuery_prop->fetchAll(PDO::FETCH_ASSOC);

						if (count($theSearchResult) > 0 ) {

							echo "<label>my strains waiting to be handed off</label>";
							echo "<table class='table table-striped table-hover table-bordered twelvepoints' id='handoff-table'>";
								echo "<th class='font-weight-bold'>strain name</th>";

								$userObject = new User("","",""); // we don’t need to assign any variables here; we just need it to query the database author table
								if ($userObject->IsCurrentUserAnEditor()) {
									echo "<th class='font-weight-bold'>hand off to me</th>";
								} else {
									echo "<th class='font-weight-bold'>hand off to the strain manager</th>";
								}
								

								require_once("../classes/classes_load_elements.php");
								$theStrainClass = new LoadParentStrains();

								$theRowNumber = 1; // starting with 1, not 0

								foreach ($theSearchResult as $theStrainID) {

									// we track row numbers so we know which one to delete when handed off
									echo "<tr id=$theRowNumber>";


									$theStrainArray = $theStrainClass->returnSpecificRecord($theStrainID['strain_id']);
									$theStrainName = htmlspecialchars($theStrainArray['strainName_col'],ENT_QUOTES);
									echo "<td>$theStrainName</td>";

									echo "<td>";
                  					$theButtonID = "handoff-button-row-" . $theRowNumber . "-strainid-" . $theStrainID['strain_id'] . "-strain_name-" . $theStrainName;
                  					// class "download" is used to identify the button
                  					// we use a class because there are multiple buttons on this page
                  					echo "<button type='button' id=$theButtonID class='btn btn-outline-info btn-sm handoff'>handoff</button>";
									echo "</td>";

									echo "</tr>";
									$theRowNumber = $theRowNumber + 1;

								}
							echo "</table>";
							
						} else {
							echo "<label>there are no strains waiting to be handed off</label>";
						}
					?>
				</div>
			</div>


			<div class="row">
				<div class="col-md-12 mb-3">
					<label for="textarea_htmlName">what you have done so far</label>
					<?php
						require_once('../classes/logger.php');
						$theLogObject = new Logger();
						$theLogString = $theLogObject->returnLog();
						echo "<textarea id='textareaID' name='textarea_htmlName' class='md-textarea twelvepoints'  readonly style='width:100%; overflow-y:scroll' rows='16'>$theLogString</textarea>";
					?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-2 mb-3">
					<label for="logout_htmlName">End the session</label>
					<input type="submit" name='logout_htmlName' class="btn btn-primary btn-block" formaction="logout.php" value="Logout" alt="Logout"/>
				</div>
			</div>

		</form>
	</div>
</body>
</html>
