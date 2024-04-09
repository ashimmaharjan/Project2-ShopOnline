var xhr = false;
if (window.XMLHttpRequest) {
  xhr = new XMLHttpRequest();
} else if (window.ActiveXObject) {
  xhr = new ActiveXObject("Microsoft.XMLHTTP");
}

function login() {
  var email = document.getElementById("email").value;
  var password = document.getElementById("password").value;

  xhr.open("POST", "php/login.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = testLogin;
  xhr.send(
    "email=" +
      encodeURIComponent(email) +
      "&password=" +
      encodeURIComponent(password)
  );
}

function testLogin() {
  if (xhr.readyState == 4) {
    if (xhr.status == 200) {
      var loginMessage = xhr.responseText.trim();
      if (
        loginMessage.startsWith("Please") ||
        loginMessage.startsWith("Invalid") ||
        loginMessage.startsWith("XML Data")
      ) {
        document.getElementById("loginMessage").innerHTML = loginMessage;
        document.querySelector(".snackbar-container").style.display = "flex";
        document.querySelector(".snackbar").style.backgroundColor = "red";
      } else {
        // Parse response text as JSON to access customer ID and first name
        var response = JSON.parse(xhr.responseText);
        if (response.customerID && response.firstName) {
          // Store customer ID and first name in session storage
          sessionStorage.setItem("customerID", response.customerID);
          sessionStorage.setItem("firstName", response.firstName);
          // Redirect to home page
          window.location.href = "home.htm";
        }
      }
    } else {
      console.log("Error occurred while processing the login request.");
    }
  }
}

function clearLoginForm() {
  document.getElementById("email").value = "";
  document.getElementById("password").value = "";
}
