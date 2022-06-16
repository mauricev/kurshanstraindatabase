<?php
  // we start off by assuming nothing for the sequence stuff and then adding the filename the sequencefilename
  $theSequenceFileName = "";
  $theSequenceFileData = "";
  echo "files<br>";
  var_dump($_FILES);
  echo "<br>";

  if(isset($_FILES['fileChooser_htmlName'])) {
    $theSequenceFileName = $_FILES['fileChooser_htmlName']['name'];
  }

  if(isset($_POST['sequenceFileData_htmlName']) && $_POST['sequenceFileData_htmlName'] != "" ) {
    $theSequenceFileData = $_POST['sequenceFileData_htmlName'];
  }
  if ($isEntityBeingEdited) {
    $theOriginalSequenceFileName = $_POST['originalSequenceFileName_postvar'];
    $theOriginalSequenceData = $_POST['originalSequenceFile_postvar'];
    // sequence data hasn't changed! Has the name changed?

    // I am not quite sure what I wrote here. I think I am remove control keys and converting them to commas
    $theOldSequenceFileDataNoCntrl = preg_replace('/[[:cntrl:]]/', '', $theOriginalSequenceData);
    $theSequenceFileDataNoCntrl = preg_replace('/[[:cntrl:]]/', '', $theSequenceFileData);
    if ($theOldSequenceFileDataNoCntrl == $theSequenceFileDataNoCntrl) { // either no change or loaded the same file twice
      // one of two things are true; same data was loaded or NO data was loaded, in this last instance, filename is NULL
      // filename may or may not have changed
      // if filename wasn't set we didn't make any changes to the file or the filename, record the oldfilename
      if($theSequenceFileName == "") {
        $theSequenceFileName = $theOriginalSequenceFileName;
      }
    } else {
      if ($theSequenceFileData == "") {
        // if it's null, we never set it, so it should retain the old name
        $theSequenceFileName = $theOriginalSequenceFileName;
      }
    }
  }
?>
