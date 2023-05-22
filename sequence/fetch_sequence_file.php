<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $filename = $_POST["filename"];
  $file = $_SERVER['DOCUMENT_ROOT'] . "/sequence_files/" . $filename;
  error_log("the file is " . $file);

  //$fileContents = file_get_contents($filename);

  // Set appropriate headers for file download
  header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
  header("Content-Type: application/octet-stream");
  header("Content-Length: " . filesize($file));

  //header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
  header("Content-Transfer-Encoding: binary");

    // Output the file contents
  readfile($file);

} else {
  error_log("File NOT saved.");
}
?>
