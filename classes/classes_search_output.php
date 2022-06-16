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

  //actual saving is done in javascript via hidden buttons to transfer the data
  public function returnTheFileData () {
    return $this->fileData;
  }
}
?>
