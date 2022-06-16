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
