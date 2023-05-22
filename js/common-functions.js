function setupTransGeneStates(transGeneStateObject) {

	if($('input[name=manufacturedWhere_htmlName]:checked').val() == 'externally-sourced') {
		transGeneStateObject.labProducedState = 'externally-sourced';
	}
	if($('input[name=manufacturedWhere_htmlName]:checked').val() == 'lab-produced') {
		transGeneStateObject.labProducedState = 'lab-produced';
	}
	if($('input[name=locationInCell_htmlName]:checked').val() == 'integrated') {
			transGeneStateObject.integratedState = 'integrated';
	}
	if($('input[name=locationInCell_htmlName]:checked').val() == 'extra-chromosomal') {
			transGeneStateObject.integratedState = 'extra-chromosomal';
	}
	if($('input[name=locationInCell_htmlName]:checked').val() == 'single insertion') {
			transGeneStateObject.integratedState = 'single insertion';
	}
}

function edit_strain_source_buttons() {

	var producedWhereCurrentValue = $('input[name=manufacturedWhere_htmlName]:checked').val();
	var isStrainBeingEditedState = $("#strainBeingEditedHiddenField").val();

	$("#lab-produced-label").text("lab-produced");
	var ptkLabProducedSavedState = $("#PTKLabProducedStateHiddenField").val();
	var ptkLabProducedNumber = $("#PTKNumberHiddenField").val();

	var labLabel = 'lab-produced, tentative designation: ';
	var labLabelMerged = labLabel.concat(ptkLabProducedNumber);

	if (isStrainBeingEditedState == true) {
		if ( (ptkLabProducedSavedState == false) && (producedWhereCurrentValue == 'lab-produced_value') ) {
			$("#lab-produced-label").text(labLabelMerged);
		}
	} else {
		if (producedWhereCurrentValue == 'lab-produced_value') {
			$("#lab-produced-label").text(labLabelMerged);
		}
	}
}

// these two functions handling the initial disabling and enabling of items in response to the last vial state
function edit_strain_lastvial_button() {
	var isStrainBeingEditedState = $("#strainBeingEditedHiddenField").val();
	if (isStrainBeingEditedState == true) {
		var isLastVialState = $("#IsLastVialHiddenField").val();
		if (isLastVialState == true){
			$('#select-lastvialers')[0].selectize.enable();
		} else {
			$('#select-lastvialers')[0].selectize.disable();
		}
	}
}

function edit_strain_lastvial_thawed_required_button() {
	var isStrainBeingEditedState = $("#strainBeingEditedHiddenField").val();
	if (isStrainBeingEditedState == true) {
		var isLastVialState = $("#IsLastVialHiddenField").val();
		if (isLastVialState == true){
			$("#dateThawed_InputID").attr("required", true);
		} else {
			$("#dateThawed_InputID").attr("required", false);
		}
	}
}

function lablabelText(inHiddenField) {
	labLabel = "lab-produced, tentative designation: ";
	labLabelMerged = labLabel.concat("kur", inHiddenField);
	$("#lab-label").text(labLabelMerged);
}

// this function displays the next kur item depending on the states
// of lab-produced, extra-chromosomal and integrated. It also displays just lab-produced
// when the externally sourced is selected
// this function doesn't alter any button states, disabled or enabled
function edit_transgene_update_buttons() {
	var theIntegratedState = 'no-state';
	var theLabProducedState = false;
	var kurExNumber = "";
	var kurIsNumber = "";
	var kurLabProducedSavedState;
	var kurExOrIsSavedState;

	var transGeneStateObject = {integratedState:'no-state', labProducedState:'no-state'};
	setupTransGeneStates(transGeneStateObject);

	$("#lab-label").text("lab-produced");
	kurExNumber = $("#kurNumberExHiddenField").val();
	kurIsNumber = $("#kurNumberIsHiddenField").val();
	kurSiNumber = $("#kurNumberSiHiddenField").val();

	kurLabProducedSavedState = $("#kurLabProducedStateHiddenField").val();
	kurIsSavedState = $("#kurExOrIsStateHiddenField").val();

	isTransGeneBeingEditedState = $("#isTransGeneBeingEditedHiddenField").val();

	if (isTransGeneBeingEditedState == true) {
		// was and still is lab-produced
		if ((kurLabProducedSavedState == true) && (transGeneStateObject.labProducedState == 'lab-produced')) {
			// if we are switching from extra-chromosomal to integrated or vice-versa
			if ((kurIsSavedState != 'Ex') && (transGeneStateObject.integratedState == 'extra-chromosomal')) {
				lablabelText(kurExNumber);
			}
			if ((kurIsSavedState != 'Is') && (transGeneStateObject.integratedState == 'integrated')) {
				lablabelText(kurIsNumber);
			}
			if ((kurIsSavedState != 'Si') && (transGeneStateObject.integratedState == 'single insertion')) {
				lablabelText(kurSiNumber);
			}
		}
		//had been externally-sourced
		if ((kurLabProducedSavedState == false) && (transGeneStateObject.labProducedState == 'lab-produced')) {
			// if we are switching from extra-chromosomal to integrated or vice-versa

			// I think this group of if statements is identical to what’s below.
			// can merge into one function DRY
			// also make the innnards a single function! no need to repeat them constantlyq
			if (transGeneStateObject.integratedState == 'extra-chromosomal') {
				lablabelText(kurExNumber);
			}
			if (transGeneStateObject.integratedState == 'integrated') {
				lablabelText(kurIsNumber);
			}
			if (transGeneStateObject.integratedState == 'single insertion') {
				lablabelText(kurSiNumber);
			}
		}
	}
 	else {
		// here we are only concerned with the current button states
		if (transGeneStateObject.labProducedState == 'lab-produced') {
			if (transGeneStateObject.integratedState == 'extra-chromosomal') {
				lablabelText(kurExNumber);
			}
			if (transGeneStateObject.integratedState == 'integrated') {
				lablabelText(kurIsNumber);
			}
			if (transGeneStateObject.integratedState == 'single insertion') {
				lablabelText(kurSiNumber);
			}
		}
	}
}

function all_transgene_update_buttons() {
	// this function handles the disabled/enabled state of the buttons
	var theIntegratedState = 'no-state';
	var theLabProducedState = false;

	var transGeneStateObject = {integratedState:'no-state', labProducedState:'no-state'};
	setupTransGeneStates(transGeneStateObject);

	if(transGeneStateObject.labProducedState == 'externally-sourced') {
		// externally source letters and numbers enabled here
		$('input[name=transgene_letters_name]').prop('disabled', false);
		$('input[name=transgene_numbers_name]').prop('disabled', false);
	}

	if(transGeneStateObject.labProducedState == 'lab-produced') {
		// externally source letters and numbers disabled here
		 $('input[name=transgene_letters_name]').prop('disabled', true);
		 $('input[name=transgene_numbers_name]').prop('disabled', true);
	}

	$("#ex-is-label").text("-");
	if (transGeneStateObject.labProducedState == 'externally-sourced') {
		if (transGeneStateObject.integratedState == 'extra-chromosomal') {
			$("#ex-is-label").text("Ex");
		}
		if (transGeneStateObject.integratedState == 'integrated') {
			$("#ex-is-label").text("Is");
		}
		if (transGeneStateObject.integratedState == 'single insertion') {
			$("#ex-is-label").text("Si");
		}
	}

	// start out with everything disabled
	// select-chromosome is actually the chromosome
	// selec-transgene is the associated extra-chromosomal transgene
	$('#select-chromosome').prop('disabled', true);
	$('#select-transgene')[0].selectize.disable();
	$('#select-plasmid')[0].selectize.disable();
	$('#select-coinjection_markers')[0].selectize.disable();

	// this is the checkbox extra-chromosomal transgene unknown
	$('#exTransGeneNA_htmlID').prop('disabled', true);

	if (transGeneStateObject.labProducedState == 'externally-sourced') {
		// externally-sourced, extra-chromosomal
		if (transGeneStateObject.integratedState == 'extra-chromosomal') {
			// select-transgene and checkbox disabled prior
			$('#select-coinjection_markers')[0].selectize.enable();
			$('#select-plasmid')[0].selectize.enable();
		}
		if (transGeneStateObject.integratedState == 'single insertion') {
			$('#select-plasmid')[0].selectize.enable();
		}
	}
		// externally-sourced, integrated
	if (transGeneStateObject.integratedState == 'integrated') {
		// always show chromosome when integrated
		$('#select-chromosome').prop('disabled', false);

		$('#exTransGeneNA_htmlID').prop('disabled', false);
		// associated chromosomal was disabled prior

		if ($('#exTransGeneNA_htmlID').is(":checked")) {
			// select transgene already disabled
			$('#select-coinjection_markers')[0].selectize.enable();
			$('#select-plasmid')[0].selectize.enable();
		} else {
			$('#select-transgene')[0].selectize.enable();
		}
	}
	if (transGeneStateObject.integratedState == 'single insertion') {
		// always show chromosome when integrated
		$('#select-chromosome').prop('disabled', false);
	}

	if (transGeneStateObject.labProducedState == 'lab-produced') {
		// lab-produced, extra-chromosomal
		if (transGeneStateObject.integratedState == 'extra-chromosomal') {
			// select-transgene and checkbox disabled prior
			$('#select-coinjection_markers')[0].selectize.enable();
			$('#select-plasmid')[0].selectize.enable();
		}
		if (transGeneStateObject.integratedState == 'single insertion') {
			$('#select-plasmid')[0].selectize.enable();
		}
	}
}

function allelesUpdateEditFieldState() {

	$("#lab-produced-label").text("lab-produced");
	var theLabProducedState = "no-state";
	var labLabel;
	var labLabelMerged;
	var kurAlleleNumber;
	var alleleBeingEditedState;
	var alleleExistingLabProducedState;

	if($('input[name=manufacturedWhere_htmlName]:checked').val() == 'externally-sourced') {
		$('input[name=geneElementLetters_htmlName]').prop('disabled', false);
		$('input[name=geneElementNumbers_htmlName]').prop('disabled', false);
		theLabProducedState = 'externally-sourced';
	} else {
		$('input[name=geneElementLetters_htmlName]').prop('disabled', true);
		$('input[name=geneElementNumbers_htmlName]').prop('disabled', true);
		if($('input[name=manufacturedWhere_htmlName]:checked').val() == 'lab-produced') {
			theLabProducedState = 'lab-produced';
		}
	}

	kurAlleleNumber = $("#kurAlleleHiddenField").val();
	alleleBeingEditedState = $("#alleleBeingEditedHiddenField").val();

	if (alleleBeingEditedState == true) {
		if (($("#alleleLabProducedStateHiddenField").val() == false) && (theLabProducedState == 'lab-produced')) {
			// we are now switching to the lab produced state
			labLabel = "lab-produced, tentative designation: ";
			labLabelMerged = labLabel.concat(kurAlleleNumber);
			$("#lab-produced-label").text(labLabelMerged);
		}
	} else {
		if (theLabProducedState == 'lab-produced') {
			// we are now switching to the lab produced state
			labLabel = "lab-produced, tentative designation: ";
			labLabelMerged = labLabel.concat(kurAlleleNumber);
			$("#lab-produced-label").text(labLabelMerged);
		}
	}
}

function lastTubeEnableContributor() {
	var isStrainBeingEditedState = $("#strainBeingEditedHiddenField").val();
	if (isStrainBeingEditedState == true ) {
		if($('#lastTubeCheckBoxID').prop('checked')) {
			$('#select-lastvialers')[0].selectize.enable();
		} else {
			$('#select-lastvialers')[0].selectize.disable();
		}
	}
}
function lastTubeRequireThawed() {
	var isStrainBeingEditedState = $("#strainBeingEditedHiddenField").val();
	if (isStrainBeingEditedState == true ) {
		if($('#lastTubeCheckBoxID').prop('checked')) {
			$("#dateThawed_InputID").attr("required", true);
			console.log("required");
		} else {
			$("#dateThawed_InputID").attr("required", false);
			console.log("not required");
		}
	}
}

function lastTubeEnabledResponse(inEvent_param) {
	lastTubeEnableContributor();
	lastTubeRequireThawed();
}

function lastTubeCheckBoxAddListener() {
	var isStrainBeingEditedState = $("#strainBeingEditedHiddenField").val();
	if (isStrainBeingEditedState == true ) {
		var btn = document.getElementById('lastTubeCheckBoxID');
		btn.addEventListener('click', lastTubeEnabledResponse);
	}
}

//very complex function to display the "invalid-feedbacks" when a relevant item doesn't meet
//the required criteria. Note that if you don't have invalid-feedback for a required item,
//the submit button will just sit there and there'll be no error message
// also be sure to disable items that aren't relevant; otherwise, they will block the submit button
(function() {
	'use strict';
	window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
			form.addEventListener('submit', function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}
				form.classList.add('was-validated');
			}, false);
		});
	}, false);
})();

function cancelButton() {
	var btn = document.getElementById('cancel');
	btn.addEventListener('click', function() {
		document.location.href = '../start/start.php';
	});
}

function setSecondChromosomeState() {
	$('#select-chromosome1').on("change", function() {
		theIndex=$("#select-chromosome1 option:selected").index();
		//$("#select-chromosome2 option[value='I']").prop('disabled', true);
		$("#select-chromosome2 option").each(function(i){
        $(this).prop('disabled', false);
    });
		// +1 because second chromosome starts off with empty string as an option
		$('#select-chromosome2 option')[theIndex+1].disabled = true;
	});
}

function setFirstChromosomeState() {
	$('#select-chromosome2').on("change", function() {
		theIndex=$("#select-chromosome2 option:selected").index();
		//$("#select-chromosome2 option[value='I']").prop('disabled', true);
		$("#select-chromosome1 option").each(function(i){
        $(this).prop('disabled', false);
    });
		// -1 because second chromosome starts off with empty string as an option
		if (theIndex > 0) {
			$('#select-chromosome1 option')[theIndex-1].disabled = true;
		}
	});
}

function disableLists () {
	$('#select-gene')[0].selectize.disable();
	$('#select-allele')[0].selectize.disable();
	$('#select-transgene')[0].selectize.disable();
	$('#select-parentStrains')[0].selectize.disable();
	$('#select-plasmid')[0].selectize.disable();
	$('#select-contributors')[0].selectize.disable();
	$('#select-coinjection_markers')[0].selectize.disable();
	$('#select-antibiotics')[0].selectize.disable();
	$('#select-fluorotags')[0].selectize.disable();
	$('#select-balancers')[0].selectize.disable();
}

// string data going to the browser must be a "blob"
function data2blob(data) {
  var theCharacters = "";
	// apparently doesn't need to be encoded
	theCharacters = data;

  var theByteArray = new Array(theCharacters.length);
  for (var theIndex = 0; theIndex < theCharacters.length; theIndex++) {
    theByteArray[theIndex] = theCharacters.charCodeAt(theIndex);
  }
	// Uint8Array because each character is one byte wide;
  var theBlob = new Blob([new Uint8Array(theByteArray)]);
  return theBlob;
}

// attaches an event listener to every download button and retrieves which file they're associated with
// through hidden variables
// function downloadPlasmidSequenceButton() {
// 	var theDownloadButtons = document.querySelectorAll('.download');
// 	var theLength = theDownloadButtons.length;
// 
// 	for (var theIndex = 0; theIndex < theLength; theIndex++) {
//
// 		function handleDownloadButtonClick(inEvent_param) {
// 			var theButtonID = inEvent_param.target.id;
//
// 			// // 7th character is the start of id in, for example, button-50
// 			var thePlasmidID = theButtonID.substring(7);
// 			var theHiddenFieldID = "hidden-";
// 			theHiddenFieldID = theHiddenFieldID.concat(thePlasmidID);
// 			//
// 			// // name contains the plasmid name
// 			var thePlasmidName = document.getElementById(theHiddenFieldID).name;
//
// 			var theSequenceData = document.getElementById(theHiddenFieldID).value;
// 			saveAs(data2blob(theSequenceData),thePlasmidName);
// 			//the above can be window.saveAs(blob, filename)
// 		}
// 		theDownloadButtons[theIndex].addEventListener('click',handleDownloadButtonClick);
// 	}
//  }

 function downloadSearchAsExcelButton() {
 	var theDownloadButton = document.getElementById('excelDownloadBtn');

	function handleSearchButtonClick(inEvent_param) {
		var theFileData = document.getElementById('excelDownloadData').value;
		var theFileName = document.getElementById('excelWhichSearch').value;
		saveAs(data2blob(theFileData),theFileName + ".tsv");
		//the above can be window.saveAs(blob, filename)
	}
	theDownloadButton.addEventListener('click',handleSearchButtonClick);
}
