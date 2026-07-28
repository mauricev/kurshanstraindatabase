<?php
require_once(__DIR__ . '/sequence_file_paths.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $filename = sequenceSafeFilename($_POST["filename"] ?? NULL);
    $file = sequenceFilePath($filename, true);

    header("Content-Disposition: attachment; filename=\"" . addcslashes($filename, "\\\"") . "\"");
    header("Content-Type: application/octet-stream");
    header("Content-Length: " . filesize($file));
    header("Content-Transfer-Encoding: binary");

    readfile($file);
  }
  catch (Throwable $e) {
    error_log("File NOT fetched: " . $e->getMessage());
    http_response_code(404);
    echo "File not found.";
  }

} else {
  http_response_code(405);
  error_log("File NOT fetched.");
}
?>
