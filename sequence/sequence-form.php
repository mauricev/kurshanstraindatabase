<!-- what’s below is for the sequencing stuff -->
<div class="row">
  <div class="form-group col-md-8 mb-3">
    <div class="col-md-7 mb-3">
      <div>
        <?php
          if ($isEntityBeingEdited) {
            echo "<label class='control-label' for='originalsequenceFileName'>Existing file name: \"$theOriginalSequenceDataFileName\"</label>";
          }
        ?>
      </div>
    </div>
    <div class="custom-file">
      <input type='file'  class='form-control-file' id='sequenceFileID' name='fileChooser_htmlName'>
      <label class="custom-file-label control-label" for="sequenceFileID">Choose sequence file</label>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-3 mb-3">
    <label class='control-label' for="sequenceFileData">Current sequence file contents</label>
  </div>
  <div class="col-md-5 mb-3">
    <!-- there can't be any returns or spaces in this textarea; otherwise, it counts as an entry -->
    <textarea class="form-control" id="sequenceFileData" readonly name="sequenceFileData_htmlName"><?php
      if ($isEntityBeingEdited) {
        echo "$theOriginalSequenceData";
      }
    ?></textarea>
  </div>
</div>
