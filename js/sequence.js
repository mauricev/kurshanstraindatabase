$(document).ready(function() {
  var inputFile = document.getElementById("sequenceFileID");

  var outputData = document.getElementById("sequenceFileData");

  inputFile.addEventListener("change", function() {
    if (this.files && this.files[0]) {
      var theFile = this.files[0];
      var theReaderBinary = new FileReader();
      var theReaderText = new FileReader();

      theReaderBinary.addEventListener('load', function(e) {
        var arrayBuffer = e.target.result;
        let filename = theFile.name;

        $.ajax({
          url: "../sequence/save_sequence_file.php",
          type: "POST",
          data: arrayBuffer,
          processData: false,
          contentType: "application/octet-stream",
          headers: {
            "Content-Disposition": "attachment; filename=\"" + filename + "\""
          },
          success: function(response) {
            console.log(response);
          },
          error: function(xhr) {
            console.log(xhr.statusText);
          }
        });
      });

      theReaderText.addEventListener('load', function (e) {
        outputData.textContent = e.target.result;
      });

      theReaderText.readAsText(theFile);

      theReaderBinary.readAsArrayBuffer(theFile);
    }
  });
});
