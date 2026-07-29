<?php
  require_once(__DIR__ . '/sequence_file_paths.php');

  function sequenceUniqueStoredFilename(string $filename): string {
    $filename = sequenceSafeFilename($filename);
    $suffix = '-' . bin2hex(random_bytes(8));
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    if ($extension !== '') {
      $stem = substr($filename, 0, -(strlen($extension) + 1));
      $maxStemLength = 255 - strlen($suffix) - strlen($extension) - 1;
      $storedFilename = substr($stem, 0, $maxStemLength) . $suffix . "." . $extension;
    } else {
      $storedFilename = substr($filename, 0, 255 - strlen($suffix)) . $suffix;
    }

    return sequenceSafeFilename($storedFilename);
  }

  // Store new uploads on disk and keep only the stored filename in the database.
  $theSequenceFileName = "";
  $theSequenceFileData = NULL;
  $theSequenceFieldsShouldBeUpdated = false;

  if(isset($_FILES['fileChooser_htmlName']) && ($_FILES['fileChooser_htmlName']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && ($_FILES['fileChooser_htmlName']['name'] ?? '') !== "") {
    $theSequenceFileName = sequenceUniqueStoredFilename($_FILES['fileChooser_htmlName']['name']);
    $theSequenceFilePath = sequenceFilePath($theSequenceFileName, false);
    if (!(move_uploaded_file($_FILES['fileChooser_htmlName']['tmp_name'], $theSequenceFilePath))) {
      throw new RuntimeException('Unable to save sequence file.');
    }
    $theSequenceFieldsShouldBeUpdated = true;
  }

  if ($isEntityBeingEdited) {
    $theOriginalSequenceFileName = $_POST['originalSequenceFileName_postvar'];
    $theOriginalSequenceData = $_POST['originalSequenceFile_postvar'];
    if (!$theSequenceFieldsShouldBeUpdated) {
      $theSequenceFileName = $theOriginalSequenceFileName;
      $theSequenceFileData = $theOriginalSequenceData;
    }
  }
?>
