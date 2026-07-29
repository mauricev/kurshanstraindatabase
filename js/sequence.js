$(document).ready(function() {
  var inputFile = document.getElementById("sequenceFileID");

  var outputData = document.getElementById("sequenceFileData");

  inputFile.addEventListener("change", function() {
    if (this.files && this.files[0]) {
      outputData.value = "";
    }
  });
});
