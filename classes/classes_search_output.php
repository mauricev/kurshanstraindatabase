<?php

  class TableOutputClass {
    protected $fileData;
      public function __construct() {
        $this->fileData = "";
      }

    public function appendColumn () {
      $this->fileData = $this->fileData . "\t";
    }

    public function appendTableRow () {
      echo "</tr>";
      $this->fileData = $this->fileData . "\n";
    }

    public function appendTableRowStartWithClass ($inClass_param) {
      echo "<tr class=$inClass_param>"; // there is no closing tag here; this means we are at the start of a row only
      // we just started a row; the row is not done yet!
    }

    public function appendTableHeader ($stringToAppend_param) {
      echo "<th>$stringToAppend_param</th>";
      $this->fileData = $this->fileData . $stringToAppend_param;
      $this->appendColumn();
    }

    public function appendTableData ($stringToAppend_param) {

       // BUGFixed 2025-05-20 remove spurious returns and newline
      $stringToAppend_param = str_replace([chr(13), chr(10)],'', $stringToAppend_param);

      echo "<td>$stringToAppend_param</td>";
      $this->fileData = $this->fileData . $stringToAppend_param;
      $this->appendColumn();
    }

    // this method skips add table data tags
    public function appendExportedTableData ($stringToAppend_param) {
      $this->fileData = $this->fileData . $stringToAppend_param;
      $this->appendColumn();
    }

    public function appendTableDataWithClass ($stringToAppend_param, $inClass_param) {
      echo "<td class=$inClass_param>$stringToAppend_param</td>";
      $this->fileData = $this->fileData . $stringToAppend_param;
      $this->appendColumn();
    }

     public function appendTableDataWithClassAndID ($stringToAppend_param, $inClass_param, $inID_param) {
      echo "<td id=$inID_param class=$inClass_param>$stringToAppend_param</td>";
      $this->fileData = $this->fileData . $stringToAppend_param;
      $this->appendColumn();
    }

    //actual saving is done in javascript via hidden buttons to transfer the data
    public function returnTheFileData () {
      return $this->fileData;
    }
  }

  class PrintOutputClass {
    protected $fileData;

      public function __construct() {
        $this->fileData = "";
      }

    // paragraph symbol is the delimiter
    public function appendToPrintData ($stringToAppend_param) {
      $this->fileData = $this->fileData . "¶"  . $stringToAppend_param;
    }

    public function assignToStrainName ($inID_param) {
    
      //BUGFixed, need to encode and decode because html can't process original string
      $fileDataEncoded = urlencode($this->fileData);

      echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
          document.getElementById("' . $inID_param . '").innerHTML = \'<a href="print_strain.php?output=' . $fileDataEncoded . '" target="_blank" rel="noopener noreferrer">' . $inID_param . '</a>\';
        });
      </script>';


    }
  }
?>
