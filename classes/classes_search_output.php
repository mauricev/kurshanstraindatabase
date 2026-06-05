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

      // BUGFixed 2025-05-20 for some reason some cells are drawing too wide
      echo "<td class='wrap'>$stringToAppend_param</td>";
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

    public function assignToStrainName ($inID_param, $inLinkText_param = NULL) {
    
      //BUGFixed, need to encode and decode because html can't process original string
      $fileDataEncoded = urlencode($this->fileData);
      $linkText = $inLinkText_param ?? $inID_param;
      $linkHtml = '<a href="print_strain.php?output=' . $fileDataEncoded . '" target="_blank" rel="noopener noreferrer">' . $linkText . '</a>';

      echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
          document.getElementById(' . json_encode($inID_param, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ').innerHTML = ' . json_encode($linkHtml, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ';
        });
      </script>';


    }
  }
?>
