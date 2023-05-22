function allStrainsButtonUpdate(disabled_param) {
		// we are passing disabled_param to every button
		if (disabled_param) {
			$('#select-trueStrains')[0].selectize.disable();
		} else {
			$('#select-trueStrains')[0].selectize.enable();
		}

		if (strainNameBtnState == "filled") {
			// every button should be disabled
			disabled_param = true;
		}

		$('#commentID').prop('disabled', disabled_param);
		$('input[name=dateFrozenBeginning_htmlName]').prop('disabled', disabled_param);
		$('input[name=dateFrozenEnding_htmlName]').prop('disabled', disabled_param);

		$('input[name=chromosomeTransGenes_htmlName]').prop('disabled', disabled_param);
		$('input[name=strainsOnly_chkbox_htmlName]').prop('disabled', disabled_param);

		$('#chromosomeOnTransGenesID').prop('disabled', disabled_param);

		switch (disabled_param) {
			case true:
				$('#select-contributors')[0].selectize.disable();
				$('#select-coinjection_markers')[0].selectize.disable();
				$('#select-gene')[0].selectize.disable();
				$('#select-balancers')[0].selectize.disable();
				$('#select-transgene')[0].selectize.disable();
				$('#select-allele')[0].selectize.disable();
				$('#select-parentStrains')[0].selectize.disable();
				$('#select-plasmid')[0].selectize.disable();

				$('#select-author')[0].selectize.disable();
				$('#select-editor')[0].selectize.disable();
				$('#select-nitrogen')[0].selectize.disable();
				$('#select-freezer')[0].selectize.disable();
				break;
			case false:
				$('#select-contributors')[0].selectize.enable();
				$('#select-coinjection_markers')[0].selectize.enable();
				$('#select-gene')[0].selectize.enable();
				$('#select-balancers')[0].selectize.enable();
				$('#select-transgene')[0].selectize.enable();
				$('#select-allele')[0].selectize.enable();
				$('#select-parentStrains')[0].selectize.enable();
				$('#select-plasmid')[0].selectize.enable();

				$('#select-author')[0].selectize.enable();
				$('#select-editor')[0].selectize.enable();
				$('#select-nitrogen')[0].selectize.enable();
				$('#select-freezer')[0].selectize.enable();
				break;

		}
}

function disableORbuttons() {
	$('#geneName_chkboxID').prop("disabled",true);
	$('#balancerName_chkboxID').prop("disabled",true);
	$('#alleleName_chkboxID').prop("disabled",true);
	$('#transgeneName_chkboxID').prop("disabled",true);
	$('#parentStrainName_chkboxID').prop("disabled",true);
}
// in the search screen, we want to enable the OR buttons only when two or more items in th selector are present
// we are being passsed the ID of the dropdown menu

function orAndButtonSetup(inEvent_param) {
	var selectedListElement = inEvent_param.target.id;
	selectedListElement = '#' + selectedListElement + ' :selected';

	switch(inEvent_param.target.id) {
		case "select-parentStrains":
			orAndButtonID = '#parentStrainName_chkboxID';
			break;
		case "select-gene":
			orAndButtonID = '#geneName_chkboxID';
			break;
		case "select-balancers":
			orAndButtonID = '#balancerName_chkboxID';
			break;
		case "select-allele":
			orAndButtonID = '#alleleName_chkboxID';
			break;
		case "select-transgene":
			orAndButtonID = '#transgeneName_chkboxID';
			break;

	}
	if ($(selectedListElement).length > 1) {
		$(orAndButtonID).prop("disabled",false);
	} else {
		$(orAndButtonID).prop("disabled",true);
	}
}

function allPlasmidsButtonUpdate(disabled_param) {
	// we are passing disabled_param to every button
	if (disabled_param) {
		$('#select-plasmid')[0].selectize.disable();
	} else {
		$('#select-plasmid')[0].selectize.enable();
	}

	if (plasmidNameBtnState == "filled") {
		// every button should be disabled
		disabled_param = true;
	}

	$('#commentID').prop('disabled', disabled_param);

	$('#cDNAID').prop('disabled', disabled_param);

	switch (disabled_param) {
		case true:
			$('#select-contributors')[0].selectize.disable();
			$('#select-gene')[0].selectize.disable();
			$('#select-antibiotics')[0].selectize.disable();
			$('#select-fluorotags')[0].selectize.disable();

			$('#select-promoter')[0].selectize.disable();

			$('#select-author')[0].selectize.disable();
			$('#select-editor')[0].selectize.disable();

			break;
		case false:
			$('#select-contributors')[0].selectize.enable();
			$('#select-gene')[0].selectize.enable();
			$('#select-antibiotics')[0].selectize.enable();
			$('#select-fluorotags')[0].selectize.enable();

			$('#select-promoter')[0].selectize.enable();

			$('#select-author')[0].selectize.enable();
			$('#select-editor')[0].selectize.enable();
			break;

	}
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

//saveAs(data2blob(theSequenceData),theSequenceFileName);
//'../sequence/fetch_sequence_file.php'
// function fetchSequenceFileContents(filename) {
//   var formData = new FormData();
//   formData.append('filename', filename);
//
//   fetch('../sequence/fetch_sequence_file.php', {
//     method: 'POST',
//     body: formData
//   })
//   .then(response => {
//     var returnedFilename = response.headers.get('X-File-Name');
//     console.log('Returned filename:', returnedFilename);
//     return response.blob();
//   })
//   .then(blob => {
//     saveAs(blob, filename);
//   })
//   .catch(error => {
//     console.error('Error:', error);
//   });
// }

function fetchSequenceFileContents(filename) {
  var formData = new FormData();
  formData.append('filename', filename);

  fetch('../sequence/fetch_sequence_file.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.blob())
  .then(blob => {
    saveAs(blob, filename);
  })
  .catch(error => {
    console.error('Error:', error);
  });
}


// Function to extract filename from Content-Disposition header
// function getFilenameFromContentDisposition(contentDisposition) {
//   var filename = "";
//   if (contentDisposition && contentDisposition.indexOf('filename=') !== -1) {
//     var matches = contentDisposition.match(/filename=([^;]+)/);
//     if (matches && matches.length > 1) {
//       filename = matches[1].trim();
//     }
//   }
//   return filename;
// }


// function fetchSequenceFileContents(filename) {
//   var formData = new FormData();
//   formData.append('filename', filename);
//
//   fetch('../sequence/fetch_sequence_file.php', {
//     method: 'POST',
//     body: formData
//   })
//   .then(response => response.blob())
//   .then(blob => {
//     var fileURL = URL.createObjectURL(blob);
//     window.location.href = fileURL;
//     URL.revokeObjectURL(fileURL);
//   })
//   .catch(error => {
//     console.error('Error:', error);
//   });
// }

// attaches an event listener to every download button and retrieves which file they're associated with
// through hidden variables

function downloadSequenceButton() {
 var theDownloadButtons = document.querySelectorAll('.download'); // look for every button with the download class
 var theLength = theDownloadButtons.length;

 for (var theIndex = 0; theIndex < theLength; theIndex++) {

	 function handleDownloadButtonClick(inEvent_param) {
		 var theButtonID = inEvent_param.target.id;

		 // // 7th character is the start of id in, for example, button-50
		 // this will copy the strainID following by the index, so it AUTOMATICALLY finds the the correct hidden field; they match after the word hidden.
		 var theItemID = theButtonID.substring(7);
		 var theHiddenFieldID = "hidden-";
		 theHiddenFieldID = theHiddenFieldID.concat(theItemID);
		 //
		 // // name contains the plasmid name
		 var theSequenceFileName = document.getElementById(theHiddenFieldID).name;

		 fetchSequenceFileContents(theSequenceFileName);
	 }
	 theDownloadButtons[theIndex].addEventListener('click',handleDownloadButtonClick);
 }
}

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
