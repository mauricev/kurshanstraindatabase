<!-- <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = $_SERVER['DOCUMENT_ROOT'] . "/sequence_files/" . $_POST["filename"];
    $data = $_POST["data"];

    // Debugging statement
    error_log("Filename: " . $filename);

    // Save the data to a file
    file_put_contents($filename, $data);

    // Debugging statement
    error_log("I am in file saving code.");

    // Write a message to the log file
    $logMessage = "File saved: " . $filename . "\n";
    file_put_contents("error.txt", $logMessage, FILE_APPEND);

    // Return a success message to the client
    echo "File saved successfully.";
  } else {
    error_log("File NOT saved.");
  }
?> -->


<!-- <?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = $_SERVER['DOCUMENT_ROOT'] . "/sequence_files/" . $_POST["filename"];
    $data = file_get_contents("php://input");

    // Open the file in binary mode for writing
    $file = fopen($filename, "wb");

    // Write the data to the file
    fwrite($file, $data, strlen($data));

    // Close the file
    fclose($file);


    // Return a success message to the client
    echo "File saved successfully.";
  } else {
    error_log("File NOT saved.");
  }
?>  -->

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename = $_SERVER['DOCUMENT_ROOT'] . "/sequence_files/" . $_POST["filename"];
    $data = file_get_contents($_FILES["data"]["tmp_name"]);

    error_log("Is this code being executed?");

    file_put_contents($filename, $data);

    // Return a success message to the client
    echo "File not saved successfully.";
} else {
    error_log("File NOT saved at all.");
}
?>
