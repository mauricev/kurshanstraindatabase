// this script reads the data from a given file and fills in the textarea field.
    // we don't pass the filename to php, but the data in the textarea
    // when we edit, we read the data from the text field and assign it to the text area

$( document ).ready(function()
{
  var inputFile = document.getElementById("sequenceFileID");
  var outputData = document.getElementById("sequenceFileData");

  inputFile.addEventListener("change", function () {
    if (this.files && this.files[0]) {
      var theFile = this.files[0];
      var theReader = new FileReader();

      theReader.addEventListener('load', function (e) {
        outputData.textContent = e.target.result;
      });

       theReader.readAsText(theFile);
    }
  });

});
