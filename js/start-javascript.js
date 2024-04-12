function specificDisables(checkBoxStateFrozen, checkBoxStateSurvival, frozenButtonID,survivalButtonID, moveButtonID) {
	if (checkBoxStateFrozen) {
        $('#' + survivalButtonID).prop('disabled', false);
        $('#' + moveButtonID).prop('disabled', false);
    } else {
        $('#' + survivalButtonID).prop('disabled', true);
        $('#' + moveButtonID).prop('disabled', true);
    }
    if (checkBoxStateSurvival) {
        $('#' + frozenButtonID).prop('disabled', true);
        $('#' + moveButtonID).prop('disabled', false);
    } else {
    	$('#' + frozenButtonID).prop('disabled', false);
        $('#' + moveButtonID).prop('disabled', true);
    }
}

async function setEnableStateOfCheckBoxes(whichProcess, theButtonID, strainID) {
    try {
        var frozenButtonID;
        var survivalButtonID;
        var moveButtonID;

        switch (whichProcess) {
            case "frozen":
                frozenButtonID = theButtonID;
                survivalButtonID = theButtonID.replace("frozen", "survival");
                moveButtonID = theButtonID.replace("frozen", "finaldestination");
                var checkBoxStateFrozen = (await returnCheckBoxValue("frozen", strainID)) !== "null";
        		var checkBoxStateSurvival = (await returnCheckBoxValue("survival", strainID)) !== "null";
                specificDisables(checkBoxStateFrozen, checkBoxStateSurvival, frozenButtonID, survivalButtonID, moveButtonID);
                break;
            case "survival":
                survivalButtonID = theButtonID;
                frozenButtonID = theButtonID.replace("survival", "frozen");
                moveButtonID = theButtonID.replace("survival", "finaldestination");
                var checkBoxStateFrozen = (await returnCheckBoxValue("frozen", strainID)) !== "null";
        		var checkBoxStateSurvival = (await returnCheckBoxValue("survival", strainID)) !== "null";
                specificDisables(checkBoxStateFrozen, checkBoxStateSurvival, frozenButtonID, survivalButtonID, moveButtonID)
                break;
        }
    } catch (error) {
        console.log("Error:", error);
    }
}

async function displayDate(whichProcess, theButtonID,strainID) {
	switch (whichProcess) {
        case "frozen":
            frozenDateID = theButtonID.replace("button", "date");
            frozenDate = await returnCheckBoxValue(whichProcess, strainID);
            if(frozenDate == "null") {
            	frozenDate = "";
            }
            $("#" + frozenDateID).html(frozenDate);
            break;
        case "survival":
            survivalDateID = theButtonID.replace("button", "date");
            survivalDate = await returnCheckBoxValue(whichProcess, strainID);
            if(survivalDate == "null") {
            	survivalDate = "";
            }
            $("#" + survivalDateID).html(survivalDate);
            break;
    }
}

function processStrain(whichProcess, strainID, theButtonID, checkBoxState) {
	try {
	  $.ajax({
	    url: '../strain_processing/process_strain.php',
	    method: 'POST',
	    data: { strainID: strainID, whichProcess: whichProcess, checkBoxState: checkBoxState },
	    success: function(data) {
	    	// should run only for frozen and freeze
	    	switch(whichProcess) {
	    	case "frozen":
	    	case "survival":
	    		setCheckBoxValue(whichProcess, theButtonID, strainID);
	    		break;
	    	}
	    	setEnableStateOfCheckBoxes(whichProcess,theButtonID, strainID);
	    	displayDate(whichProcess, theButtonID,strainID);

	    },
	    error: function(error) {
	   
	    }
	  });
	} catch (error) {
	    	console.log("Error in processStrain:", error);
	  	}
}

// can we institue a delay to make this work? (working around race condition)
function updateLog() {
	try {
	  $.ajax({
	    url: '../strain_processing/update_log.php',
	    method: 'POST',
	    success: function(data) {
	    	document.getElementById('textareaID').innerHTML = data;
	    },
	    error: function(error) {
	    	console.log(error);
	    }
	  });
	} catch (error) {
		    console.log("Error in updateLog:", error);
		}
}

// either returns a date or null
function returnCheckBoxValue(whichProcess, strainID) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: '../strain_processing/return_checkbox_status.php',
      type: 'POST',
      dataType: 'json',
      data: { whichProcess: whichProcess, strainID: strainID},
      success: function(response) {
        resolve(response.trim()); 
      },
      error: function(error) {
        reject(error);
        console.log("returnCheckBoxValue failed " + error);
      }
    });
  });
}

function setCheckBoxValue(whichProcess, theButtonID, strainID) {
	$.ajax({
	    url: '../strain_processing/return_checkbox_status.php',
	    type: 'POST',
	    dataType: 'json',
	    data: { whichProcess: whichProcess, strainID: strainID },
	    success: function(data) {
	    	var theResponse = data.trim();
	    	var isChecked = theResponse !== "null";
            $('#' + theButtonID).prop('checked', isChecked);
	    },
	    error: function(error) {
	        console.log(error);
	    }
    });
}

async function returnDialogText(whichProcess, strainID, strainName) {
    var message = "";
    switch(whichProcess) {
	    case "handoff":
	        message = "Are you sure you want to handoff strain " + strainName + "?";
	        break;
	    case "frozen":
	        const checkBoxStateFrozen = (await returnCheckBoxValue(whichProcess, strainID)) !== "null";
	        if(checkBoxStateFrozen) {
	            message = "Are you sure you want to unset strain " + strainName + "’s frozen status?";
	        } else {
	            message = "Are you sure you want to set strain " + strainName + "’s frozen status?";
	        }
	        break;
	    case "survival":
	    	const theNullState = await returnCheckBoxValue(whichProcess, strainID);
	        const checkBoxStateSurvival = theNullState !== "null";
	        if(checkBoxStateSurvival) {
	            message = "Are you sure you want to unset strain " + strainName + "’s survived status?";
	        } else {
	            message = "Are you sure you want to set strain " + strainName + "’s survived status?";
	        }
	        break;
	    case "finaldestination":
	        message = "Are you sure you want to move strain " + strainName + " to its final destination?";
	        break;
    }
    return message;
}

function handleStrainButtons() {
	var confirmationDialog = document.getElementById('confirmation_dialog');
	var cancelButton = document.getElementById('cancel-button');
	var okButton = document.getElementById('ok-button');
	var messageText = document.getElementById('confirmation_string');
 
 	var theHandoffButtons = document.querySelectorAll('.handoff');
 	var theFrozenButtons = document.querySelectorAll('.frozen'); 
 	var theSurvivalButtons = document.querySelectorAll('.survival'); 
 	var theMoveButtons = document.querySelectorAll('.finaldestination');

 	
 	async function processStrainButtonClick(inEvent_param) {
		var theButtonID = inEvent_param.target.id;
		var theButtonIDParts = theButtonID.split('-');

		// here is how thebuttonid looks
		// $theButtonID = "handoff-button-row-" . $theRowNumber . "-strainid-" . $theStrainID['strain_id'] . "-strain_name-" . $theStrainName;
		var whichProcess = theButtonIDParts[0];
		var strainID = theButtonIDParts[5];
		var strainName = theButtonIDParts[7];

		messageText.textContent = await returnDialogText(whichProcess, strainID, strainName);

		okButton.dataset.clickedButtonId = theButtonID;
		cancelButton.dataset.clickedButtonId = theButtonID;

		confirmationDialog.showModal();
	}

	var theLength = theHandoffButtons.length;
	for (var theIndex = 0; theIndex < theLength; theIndex++) {
		theHandoffButtons[theIndex].addEventListener('click',processStrainButtonClick);
	}

	// if we are not on the editor page, length here should be zero
	var theLength = theFrozenButtons.length; // all three buttons exist in tandem
	for (var theIndex = 0; theIndex < theLength; theIndex++) {
		theFrozenButtons[theIndex].addEventListener('click',processStrainButtonClick);
		theSurvivalButtons[theIndex].addEventListener('click',processStrainButtonClick);
		theMoveButtons[theIndex].addEventListener('click',processStrainButtonClick);
	}

	cancelButton.addEventListener('click', () => {
		event.preventDefault();
	  	confirmationDialog.close();

	  	var theButtonID = cancelButton.dataset.clickedButtonId;
	  	var isChecked = $('#' + theButtonID).prop('checked');
	  	// toggle the button’s checked state back to its orignal state
	  	$('#' + theButtonID).prop('checked', !isChecked);
	});

	okButton.addEventListener('click', (event) => {
	    event.preventDefault();
	    confirmationDialog.close();

	    (async () => { 
	    	// okButton.dataset.clickedButtonId = "handoff-button-row-" . $theRowNumber . "-strainid-" . $theStrainID['strain_id'] . "-strain_name-" . $theStrainName;
	        var theButtonID = okButton.dataset.clickedButtonId;
	        var theButtonIDParts = theButtonID.split('-');
	        var whichProcess = theButtonIDParts[0];
	        var rowNumber = theButtonIDParts[3];
	        var strainID = theButtonIDParts[5];

	        var checkBoxState = "not used";
	        if (whichProcess == "frozen" || whichProcess == "survival") {
	            try {
	                checkBoxState = (await returnCheckBoxValue(whichProcess, strainID)) !== "null"; // when not null, it contains a date
	            } catch (error) {
	                console.log("Error getting checkbox state:", error);
	            }
	        }

	        // if the user chose survival and the checkbox comes back false, the user also wants the strain to be de-handed off
	
	        processStrain(whichProcess, strainID, theButtonID, checkBoxState);


	        // when it’s true, it’s becoming false, being de-selected, so we proeed with handoff
	        if((whichProcess == "survival") && (checkBoxState == true)) {
	        	whichProcess = "de-handoff";
	        	processStrain(whichProcess, strainID, theButtonID, checkBoxState);
	        }
	       
	        var rowToRemove = document.getElementById(rowNumber);
	        if ((whichProcess == "finaldestination") && rowToRemove) {
	            rowToRemove.remove();
	        }

	        // we need to not only remove it from the current list but also add it to the other list
	        if ( (whichProcess == "handoff") || ((whichProcess == "de-handoff") && (checkBoxState == true)) ) {
	        	 setTimeout(() => {
	            	window.location.reload();
	        	}, 1000); 
	        } else {
	        	setTimeout(() => {
	            	updateLog();
	        	}, 1000); 
	        }
	        
    })();
});

}

