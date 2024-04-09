// Function to process auction items
function processAuctionItems() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "maintenance.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      alert(xhr.responseText); // Display response message
    }
  };
  xhr.send("action=processAuctionItems");
}

// Function to generate report
function generateReport() {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var xmlData = this.responseXML;
      console.log("XML data from php server", xmlData);

      var xsltProcessor = new XSLTProcessor();
      var xsl = new XMLHttpRequest();
      xsl.open("GET", "report.xsl", true);
      xsl.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          xsltProcessor.importStylesheet(this.responseXML);
          var resultDocument = xsltProcessor.transformToDocument(xmlData);

          document.getElementById("reportContainer").innerHTML =
            new XMLSerializer().serializeToString(resultDocument);
        }
      };
      xsl.send();
    }
  };
  xhr.open("POST", "maintenance.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("action=generateReport");
}
