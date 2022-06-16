<?php include_once('../classes/session.php');?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="../css/kurshan.css"/>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/common-functions.js"></script>

    <script>
      $( document ).ready(function()
      {
        cancelButton();
      });
    </script>

  </head>
  <body class="bg-light">
  	<div class="container">
      <?php
        require_once("../classes/classes_load_elements.php");

        $singleElementBeingEdited = false;

        if (isset($_POST["whichList_htmlName"])) {
          $singleElementBeingEdited = true;
        }

        if ($singleElementBeingEdited) {
          switch($_POST["whichList_htmlName"]) {
            case 'contributors_htmlValue':
              $singleElementObjectToEdit = new LoadContributor();
              $selectedElement_param = $_POST['contributorArray_htmlName'];
              break;
            case 'coinjection_markers_htmlValue':
              $singleElementObjectToEdit = new LoadCoInjectionMarker();
              $selectedElement_param = $_POST['coinjectionMarkerArray_htmlName'];
              break;
            case 'antibiotics_htmlValue':
              $singleElementObjectToEdit = new LoadAntibiotic();
              $selectedElement_param = $_POST['antibioticArray_htmlName'];
              break;
            case 'fluorotags_btn_htmlValue':
              $singleElementObjectToEdit = new LoadFluoroTag();
              $selectedElement_param = $_POST['fluorotagArray_htmlName'];
              break;
          }
          $elementArrayToEdit = $singleElementObjectToEdit->returnSpecificRecord($selectedElement_param[0]);
          // because we are already in the class, we can just pass the stored column name to fetch the record's array
  				$theOldElementName = htmlspecialchars($elementArrayToEdit[$singleElementObjectToEdit->returnElementNameColumn()],ENT_QUOTES);

  				$theOldElementID = $selectedElement_param[0];
          $theElementString = $singleElementObjectToEdit->returnElementString();
          $theElementStringLC = $singleElementObjectToEdit->returnElementLC();
          $theHTMLName = $singleElementObjectToEdit->returnHTMLName();
        } else {

          if(isset($_POST['newContributor_htmlName']))
          {
            $theHTMLName = 'newContributor_htmlName';
            $theElementString = "Contributor";
            $theElementStringLC = "contributor";
          } else if(isset($_POST['newCoInjection_htmlName']))
          {
            $theHTMLName = 'newCoInjection_htmlName';
            $theElementString = "Co-injection Marker";
            $theElementStringLC = "co-injection marker";
          } else if(isset($_POST['newAntibioticResistance_htmlName']))
          {
            $theHTMLName = 'newAntibioticResistance_htmlName';
            $theElementString = "Antibiotic";
            $theElementStringLC = "antibiotic";
          } else if(isset($_POST['newFluoro_htmlName']))
          {
            $theHTMLName = 'newFluoro_htmlName';
            $theElementString = "Fluor/tag";
            $theElementStringLC = "fluor/tag";
          }
        }
			?>
			<div class='py-5 text-center'>
        <img class='d-block mx-auto mb-4' alt='' width='72' height='72'>
        <h2>KurshanLab Strain Database</h2>
        <?php
          if ($singleElementBeingEdited) {
            echo "<p class='lead'>Edit \"$theElementString\", \"$theOldElementName\"</p>";
          } else {
            echo "<p class='lead'>Add New \"$theElementString\"</p>";
          }
        ?>
      	</div>
    		<form class='needs-validation' action='../single-element/submit_edited_element.php' method='post'>
      		<div class='row'>
        		<div class='col-md-3 mb-3'>
          		<?php
                if ($singleElementBeingEdited) {
                  echo "<input type='text' id='newElement_fieldID' name='newElement_fieldName' class='form-control' value=\"$theOldElementName\" required placeholder=\"enter $theElementStringLC\"/>";
                } else {
                  echo "<input type='text' id='newElement_fieldID' name='newElement_fieldName' class='form-control'required placeholder=\"enter $theElementStringLC\"/>";
                }
              ?>
            </div>
      		</div>
      		<div class='row'>
            <div class="col-md-2 mb-3">
              <button type="button" id="cancel" class="form-control">Cancel</button>
            </div>
						<div class='col-md-3 mb-3'>
              <?php
                echo "<input type='hidden' name='original_isElementBeingEdited_postvar' value=$singleElementBeingEdited>";
                echo "<input type='hidden' name='original_element_postvar' value=$theHTMLName>";
                if ($singleElementBeingEdited) {// original_element_postvar contains what kind of class needs to be created in submit_edited_element.php
							    echo "<input type='hidden' name='original_elementID_postvar' value=$theOldElementID>";
            	    echo "<input type='submit' class='btn btn-primary btn-block' name='accept_element_entry' value=\"Edit $theElementString Entry\" alt=\"Edit $theElementString Entry\"/>";
                } else {
                  echo "<input type='submit' class='btn btn-primary btn-block' name='accept_element_entry' value=\"Accept $theElementString Entry\" alt=\"Accept $theElementString Entry\"/>";
                }
              ?>
            </div>
      		</div>
        </form>
    </div>
</body>
</html>
