<?php
  // pulls in the existing sequence info in case we are editing an existing entity (allele, plasmid)
  //forgot to call htmlspecialchars on this; it displays
  $theOriginalSequenceDataFileName = "";
  if ($geneElementArrayToEdit['sequenceDataName_col'] != NULL){
    $theOriginalSequenceDataFileName = htmlspecialchars($geneElementArrayToEdit['sequenceDataName_col'],ENT_QUOTES);
  }
  //BUG: some html sequence was escaping the hidden function; putting this entry in htmlspecialchars fixed it.
  $theOriginalSequenceData = htmlspecialchars($geneElementArrayToEdit['sequence_data_col'],ENT_QUOTES);
?>
