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
