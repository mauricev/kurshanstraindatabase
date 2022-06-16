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
