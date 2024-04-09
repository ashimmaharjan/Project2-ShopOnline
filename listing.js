var xhr = false;
if (window.XMLHttpRequest) {
  xhr = new XMLHttpRequest();
} else if (window.ActiveXObject) {
  xhr = new ActiveXObject("Microsoft.XMLHTTP");
}

function submitListing() {
  var customerID = sessionStorage.getItem("customerID");
  var itemName = document.getElementById("itemName").value.trim();
  var category = document.getElementById("category").value.trim();
  var description = document.getElementById("description").value.trim();
  var startPrice = parseFloat(document.getElementById("startPrice").value);
  var reservePrice = parseFloat(document.getElementById("reservePrice").value);
  var buyItNowPrice = parseFloat(
    document.getElementById("buyItNowPrice").value
  );
  var day = parseInt(document.getElementById("day").value);
  var hour = parseInt(document.getElementById("hour").value);
  var min = parseInt(document.getElementById("min").value);

  // Combine day, hour, and min into a single duration variable (in minutes)
  var duration = day * 24 * 60 + hour * 60 + min;

  // Form validation
  if (
    !itemName ||
    !category ||
    !description ||
    !startPrice ||
    !reservePrice ||
    !buyItNowPrice ||
    !day ||
    !hour ||
    !min
  ) {
    alert("Please fill out all fields.");
    return;
  }
  if (startPrice > reservePrice) {
    alert("Start price cannot be greater than reserve price.");
    return;
  }
  if (reservePrice > buyItNowPrice) {
    alert("Reserve price cannot be greater than buy-it-now price.");
    return;
  }

  // Send the form data to the server
  xhr.open("POST", "list_item.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = handleListingResponse;
  xhr.send(
    "customerID=" +
      encodeURIComponent(customerID) +
      "&itemName=" +
      encodeURIComponent(itemName) +
      "&category=" +
      encodeURIComponent(category) +
      "&description=" +
      encodeURIComponent(description) +
      "&startPrice=" +
      encodeURIComponent(startPrice) +
      "&reservePrice=" +
      encodeURIComponent(reservePrice) +
      "&buyItNowPrice=" +
      encodeURIComponent(buyItNowPrice) +
      "&duration=" +
      encodeURIComponent(duration)
  );
}

function handleListingResponse() {
  if (xhr.readyState == 4 && xhr.status == 200) {
    var responseMessage = xhr.responseText.trim();
    var snackbar = document.querySelector(".snackbar");

    if (responseMessage) {
      document.getElementById("listingMessage").innerHTML = responseMessage;
      document.querySelector(".snackbar-container-2").style.display = "flex";
      console.log("Response Message from server is:", responseMessage);

      if (responseMessage.startsWith("Thank")) {
        snackbar.style.backgroundColor = "green";
        clearListingForm();
      } else {
        snackbar.style.backgroundColor = "red";
      }
    } else {
      document.querySelector(".snackbar-container-2").style.display = "none";
    }
  }
}

function clearListingForm() {
  document.getElementById("itemName").value = "";
  document.getElementById("category").value = "";
  document.getElementById("description").value = "";
  document.getElementById("startPrice").value = "";
  document.getElementById("reservePrice").value = "";
  document.getElementById("buyItNowPrice").value = "";
  document.getElementById("day").value = "";
  document.getElementById("hour").value = "";
  document.getElementById("min").value = "";

  // Scroll to the top of the page
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function fetchCategories() {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "getCategories.php", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var categories = JSON.parse(xhr.responseText);
      populateCategoryOptions(categories);
    }
  };
  xhr.send();
}

function populateCategoryOptions(categories) {
  var categoryDropdown = document.getElementById("category");
  categoryDropdown.innerHTML = "<option value=''>Select a category.</option>";
  categories.forEach(function (category) {
    var option = document.createElement("option");
    option.value = category;
    option.textContent = category;
    categoryDropdown.appendChild(option);
  });
}
