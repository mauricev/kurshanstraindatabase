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
