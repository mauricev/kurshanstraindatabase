$(document).ready(function() {
  var inputFile = document.getElementById("sequenceFileID");

  inputFile.addEventListener("change", function() {
    if (this.files && this.files[0]) {
      var theFile = this.files[0];
      var theReader = new FileReader();

      theReader.addEventListener('load', function(e) {
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

      theReader.readAsArrayBuffer(theFile);
    }
  });
});
