function getFirstName() {
  var firstName = sessionStorage.getItem("firstName");
  return firstName || "";
}

function redirectToLogin() {
  window.location.href = "login.htm";
}

function redirectToHome() {
  window.location.href = "home.htm";
}

function checkSession() {
  var customerID = sessionStorage.getItem("customerID");
  if (customerID === null) {
    redirectToLogin();
    return;
  }
}

function displayWelcomeMessage() {
  checkSession();
  var firstName = getFirstName();
  var welcomeMessage = document.getElementById("welcomeMessage");
  if (welcomeMessage && firstName) {
    welcomeMessage.textContent = "Welcome, " + firstName + "!";
  }
}

function clearSessionStorage() {
  sessionStorage.clear();
}

function logout() {
  clearSessionStorage();
  redirectToLogin();
}
