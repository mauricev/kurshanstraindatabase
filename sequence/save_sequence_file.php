<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //22 is where the actual filename begins
  $filename = $_SERVER['HTTP_CONTENT_DISPOSITION'];
  $filename = substr($filename, 22);
  $filename = str_replace('"', '', $filename);
  $filename = $_SERVER['DOCUMENT_ROOT'] . "/sequence_files/" . $filename;
  $data = file_get_contents("php://input");

  file_put_contents($filename, $data);

  echo "File saved successfully.";
} else {
  error_log("File NOT saved.");
}
?>
