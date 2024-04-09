var xhr = false;
if (window.XMLHttpRequest) {
  xhr = new XMLHttpRequest();
} else if (window.ActiveXObject) {
  xhr = new ActiveXObject("Microsoft.XMLHTTP");
}

function register() {
  var firstName = document.getElementById("firstName").value;
  var lastName = document.getElementById("lastName").value;
  var email = document.getElementById("email").value;
  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("confirmPassword").value;

  xhr.open(
    "GET",
    "register.php?firstName=" +
      encodeURIComponent(firstName) +
      "&lastName=" +
      encodeURIComponent(lastName) +
      "&email=" +
      encodeURIComponent(email) +
      "&password=" +
      encodeURIComponent(password) +
      "&confirmPassword=" +
      encodeURIComponent(confirmPassword),
    true
  );

  xhr.onreadystatechange = testInput;
  xhr.send(null);
}

function testInput() {
  if (xhr.readyState == 4 && xhr.status == 200) {
    var registrationMessage = xhr.responseText.trim();
    var snackbar = document.querySelector(".snackbar");

    if (registrationMessage) {
      document.getElementById("registrationMessage").innerHTML =
        registrationMessage;
      document.querySelector(".snackbar-container").style.display = "flex";
      if (registrationMessage.startsWith("Dear")) {
        snackbar.style.backgroundColor = "green";
        clearForm();
      } else {
        snackbar.style.backgroundColor = "red";
      }
    } else {
      document.querySelector(".snackbar-container").style.display = "none";
    }
  }
}

function clearForm() {
  document.getElementById("firstName").value = "";
  document.getElementById("lastName").value = "";
  document.getElementById("email").value = "";
  document.getElementById("password").value = "";
  document.getElementById("confirmPassword").value = "";
}
