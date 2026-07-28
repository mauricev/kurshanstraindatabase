<?php
require_once(__DIR__ . '/sequence_file_paths.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  try {
    $filename = sequenceFilenameFromContentDisposition($_SERVER['HTTP_CONTENT_DISPOSITION'] ?? NULL);
    $file = sequenceFilePath($filename, false);
    $data = file_get_contents("php://input");

    if ($data === false || file_put_contents($file, $data, LOCK_EX) === false) {
      throw new RuntimeException('Unable to save sequence file.');
    }

    echo "File saved successfully.";
  }
  catch (Throwable $e) {
    error_log("File NOT saved: " . $e->getMessage());
    http_response_code(400);
    echo "File not saved.";
  }
} else {
  http_response_code(405);
  error_log("File NOT saved.");
}
?>
