<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html>
<head>
		<title>KurshanLab Strain Database</title>
		<meta charset="utf-8">

		<link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/bootstrap/js/bootstrap.min.js"></script>
		<script src="/js/selectize.js"></script>
		<script src="/js/common-functions.js"></script>

 		<script>

		  $( document ).ready(function()
		  {
				$('#select-gene').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-allele').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-transgene').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-parentStrains').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-plasmid').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-contributors').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-coinjection_markers').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-antibiotics').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-fluorotags').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
				$('#select-balancers').selectize({
          create: false,
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });

				disableLists();
				cancelButton();

		  });
		</script>

</head>
<body class="bg-light">
	<div class="container">
		<div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" alt="" width="144" height="144" src="/images/peri-logo.jpg">
      <h2>KurshanLab Strain Database</h2>
      <p class="lead">Edit Items</p>
    </div>

		<form class="needs-validation" action="edit_gene_element.php" method="post">

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="strains_btn" class="custom-control-input" name='whichList_htmlName' value='strains_htmlValue' required>
						<label class="custom-control-label" for="strains_btn">Strains</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theStrainsListing = new LoadParentStrains();
						$isMultiple = false;
	          $theStrainsListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="gene_btn" name='whichList_htmlName' class="custom-control-input" value='genes_htmlValue' required>
						<label class="custom-control-label" for="gene_btn">Genes</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theGeneListing = new LoadGene();
						$isMultiple = false;
	          $theGeneListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="alleles_btn" name='whichList_htmlName' class="custom-control-input" value='alleles_htmlValue' required>
						<label class="custom-control-label" for="alleles_btn">Alleles</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
						require_once("../classes/classes_load_elements.php");
						$theAlleleListing = new LoadAllele();
						$isMultiple = false;
						$theAlleleListing->buildSelectTable($isMultiple);
					?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="transgenes_btn" class="custom-control-input" name='whichList_htmlName' value='transgenes_htmlValue' required>
						<label class="custom-control-label" for="transgenes_btn">Transgenes</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theTransGeneListing = new LoadTransGene();
						$isMultiple = false;
	          $theTransGeneListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="plasmids_btn" name='whichList_htmlName' class="custom-control-input" value='plasmids_htmlValue' required>
						<label class="custom-control-label" for="plasmids_btn">Plasmids</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $thePlasmidsListing = new LoadPlasmid();
						$isMultiple = false;
	          $thePlasmidsListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="contributors_btn" name='whichList_htmlName' class="custom-control-input" value='contributors_htmlValue' required>
						<label class="custom-control-label" for="contributors_btn">Contributors</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theContributorListing = new LoadContributor();
						$isMultiple = false;
	          $theContributorListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="coinjectionmarkers_btn" name='whichList_htmlName' class="custom-control-input" value='coinjection_markers_htmlValue' required>
						<label class="custom-control-label" for="coinjectionmarkers_btn">Co-injection Markers</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theCoInjectionMarkerListing = new LoadCoInjectionMarker();
						$isMultiple = false;
	          $theCoInjectionMarkerListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="antibiotics_btn" class="custom-control-input" name='whichList_htmlName' value='antibiotics_htmlValue' required>
						<label class="custom-control-label" for="antibiotics_btn">Antibiotics</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
						require_once("../classes/classes_load_elements.php");
						$theAntibioticListing = new LoadAntibiotic();
						$isMultiple = false;
						$theAntibioticListing->buildSelectTable($isMultiple);
					?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="fluorotags_btn" class="custom-control-input" name='whichList_htmlName' value='fluorotags_btn_htmlValue' required>
						<label class="custom-control-label" for="fluorotags_btn">Fluoro Tags</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theFluoroTagListing = new LoadFluoroTag();
						$isMultiple = false;
	          $theFluoroTagListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 mb-3">
					<div class="custom-control custom-radio">
						<input type = 'radio' id="balancers_btn" class="custom-control-input" name='whichList_htmlName' value='balancers_btn_htmlValue' required>
						<label class="custom-control-label" for="balancers_btn">Balancers</label>
					</div>
				</div>
				<div class="col-md-3 mb-3">
					<?php
	          require_once("../classes/classes_load_elements.php");
	          $theFluoroTagListing = new LoadBalancer();
						$isMultiple = false;
	          $theFluoroTagListing->buildSelectTable($isMultiple);
	        ?>
				</div>
			</div>

			<!-- <div class="row">
				<div class="col-md-6 mb-3">
				</div>
				<div class="col-md-4 mb-3">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="delete-checkbox" name='checkbox_htmlName' value='checkbox_htmlValue'>
						<label class="custom-control-label" for="delete-checkbox">Switch to delete mode</label>
					</div>
				</div>
			</div> -->
			<div class="row">
				<div class="col-md-3 mb-3">
				</div>
				<div class="col-md-2 mb-3">
					<button type="button" id="cancel" class="form-control">Cancel</button>
				</div>
				<div class="col-md-3 mb-3">
					<input type="submit" name='submit_htmlName' id='submit_btn_id' class="btn btn-primary btn-block" value="Edit Selected Item" alt="Edit Selected Item"/>
				</div>
			</div>
		</form>
	</div>

  <script>
	$( document ).ready(function()
 	{
		// when delete checkbox is checked or unchecked, we change the name of the submit button
		// $('input[name=checkbox_htmlName]').change(function() {
		// 		if(this.checked) {
		// 			$('input[name=submit_htmlName]').val('Delete Selected Item');
		// 		} else {
		// 			$('input[name=submit_htmlName]').val ('Edit Selected Item');
		// 		}
		// });

  	$('input[name=whichList_htmlName]').change(function()
  	{

			disableLists();

			switch($(this).val()) {
				case 'contributors_htmlValue':
					$('#select-contributors')[0].selectize.enable();
					break;
				case 'coinjection_markers_htmlValue':
					$('#select-coinjection_markers')[0].selectize.enable();
					break;
				case 'antibiotics_htmlValue':
					$('#select-antibiotics')[0].selectize.enable();
					break;
				case 'fluorotags_btn_htmlValue':
					$('#select-fluorotags')[0].selectize.enable();
					break;
        case 'genes_htmlValue':
					$('#select-gene')[0].selectize.enable();
          break;
        case 'alleles_htmlValue':
					$('#select-allele')[0].selectize.enable();
          break;
        case 'transgenes_htmlValue':
					$('#select-transgene')[0].selectize.enable();
          break;
				case 'strains_htmlValue':
					$('#select-parentStrains')[0].selectize.enable();
					break;
				case 'plasmids_htmlValue':
					$('#select-plasmid')[0].selectize.enable();
					break;
				case 'balancers_btn_htmlValue':
					$('#select-balancers')[0].selectize.enable();
					break;
      }
  	});
	});
  </script>

</body>
</html>
