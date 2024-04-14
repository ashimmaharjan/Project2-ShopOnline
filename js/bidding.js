// Function to periodically retrieve all items from the server
function fetchItems() {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "php/bidding.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var items = JSON.parse(xhr.responseText);
      updateUI(items);
    }
  };
  xhr.send();
}

// Function to handle placing a bid
function placeBid(itemNumber) {
  var newBidPrice = prompt("Enter your new bid price:");
  if (newBidPrice === null || newBidPrice === "") {
    alert("Please enter value for new bid price.");
    return;
  }

  // Validate if new bid price is a valid number or float
  if (!/^\d*\.?\d*$/.test(newBidPrice)) {
    alert("Please enter a valid number for the new bid price.");
    return;
  }

  var bidderID = sessionStorage.getItem("customerID");
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "php/bidding.php", true); // Send request to bidding.php
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      alert(xhr.responseText);
      fetchItems();
    }
  };
  xhr.send(
    "action=placeBid" +
      "&itemNumber=" +
      encodeURIComponent(itemNumber) +
      "&newBidPrice=" +
      encodeURIComponent(newBidPrice) +
      "&bidderID=" +
      encodeURIComponent(bidderID)
  );
}

// Function to handle buying an item
function buyItem(itemNumber) {
  var xhr = new XMLHttpRequest();
  var bidderID = sessionStorage.getItem("customerID");
  xhr.open("POST", "php/bidding.php", true); // Send request to bidding.php
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      alert(xhr.responseText);
      fetchItems();
    }
  };
  xhr.send(
    "action=buyItem" +
      "&itemNumber=" +
      encodeURIComponent(itemNumber) +
      "&bidderID=" +
      encodeURIComponent(bidderID)
  );
}

// Function to format time left for display
function formatTimeLeft(timeLeft) {
  var days = Math.floor(timeLeft / (3600 * 24));
  var hours = Math.floor((timeLeft % (3600 * 24)) / 3600);
  var minutes = Math.floor((timeLeft % 3600) / 60);
  var seconds = timeLeft % 60;

  var timeString = "";
  if (days > 0) {
    timeString += days + " days ";
  }
  if (hours > 0) {
    timeString += hours + " hours ";
  }
  if (minutes > 0) {
    timeString += minutes + " minutes ";
  }
  timeString += seconds + " seconds";

  return timeString + " remaining";
}

// Function to update the UI with the retrieved items
function updateUI(items) {
  var itemList = document.getElementById("itemList");
  itemList.innerHTML = "";
  items.forEach(function (item) {
    var itemContainer = document.createElement("div");
    itemContainer.classList.add("bidItemCard");

    var labelsContainer = document.createElement("div");
    labelsContainer.classList.add("labels-container");
    var labelItemName = document.createElement("label");
    labelItemName.textContent = "Name:";
    labelsContainer.appendChild(labelItemName);

    var labelCategory = document.createElement("label");
    labelCategory.textContent = "Category:";
    labelsContainer.appendChild(labelCategory);

    var labelDescription = document.createElement("label");
    labelDescription.textContent = "Description:";
    labelsContainer.appendChild(labelDescription);

    var labelBuyPrice = document.createElement("label");
    labelBuyPrice.textContent = "Buy It Now Price:";
    labelsContainer.appendChild(labelBuyPrice);

    var labelCurrentBidPrice = document.createElement("label");
    labelCurrentBidPrice.textContent = "Current Bid Price:";
    labelsContainer.appendChild(labelCurrentBidPrice);

    var labelTimeLeft = document.createElement("label");
    labelTimeLeft.textContent = "Time Left:";
    labelsContainer.appendChild(labelTimeLeft);
    itemContainer.appendChild(labelsContainer);

    var detailsContainer = document.createElement("div");
    detailsContainer.classList.add("details-container");
    var itemName = document.createElement("p");
    itemName.textContent = item.itemName;
    detailsContainer.appendChild(itemName);

    var itemCategory = document.createElement("p");
    itemCategory.textContent = item.category;
    detailsContainer.appendChild(itemCategory);

    var itemDescription = document.createElement("p");
    itemDescription.textContent = item.description.substring(0, 30) + "...";
    detailsContainer.appendChild(itemDescription);

    var buyItNowPrice = document.createElement("p");
    buyItNowPrice.textContent = "$" + item.buyItNowPrice;
    detailsContainer.appendChild(buyItNowPrice);

    var currentBidPrice = document.createElement("p");
    currentBidPrice.textContent = "$" + item.currentBidPrice;
    detailsContainer.appendChild(currentBidPrice);

    var timeLeft = document.createElement("p");
    timeLeft.textContent = formatTimeLeft(item.timeLeft);
    detailsContainer.appendChild(timeLeft);
    itemContainer.appendChild(detailsContainer);

    var buttonsContainer = document.createElement("div");
    buttonsContainer.classList.add("bid-buttons");
    if (!item.sold && item.timeLeft > 0) {
      var placeBidButton = document.createElement("button");
      placeBidButton.classList.add("submitButton");
      placeBidButton.textContent = "Place Bid";
      placeBidButton.addEventListener("click", function () {
        placeBid(item.itemNumber);
      });
      buttonsContainer.appendChild(placeBidButton);

      var buyItNowButton = document.createElement("button");
      buyItNowButton.textContent = "Buy It Now";
      buyItNowButton.classList.add("buy-it-now-button");
      buyItNowButton.addEventListener("click", function () {
        buyItem(item.itemNumber);
      });
      buttonsContainer.appendChild(buyItNowButton);
    } else {
      var statusMessage = document.createElement("p");
      statusMessage.classList.add("bid-status-message");
      statusMessage.textContent = item.sold
        ? "Item has been sold."
        : "Time has expired.";
      buttonsContainer.appendChild(statusMessage);
    }
    itemContainer.appendChild(buttonsContainer);

    itemList.appendChild(itemContainer);
  });
}
fetchItems();
setInterval(fetchItems, 5000);
