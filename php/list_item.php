<?php

header('Content-Type: text/xml');

if (
    isset($_POST["customerID"]) &&
    isset($_POST["itemName"]) &&
    isset($_POST["category"]) &&
    isset($_POST["description"]) &&
    isset($_POST["startPrice"]) &&
    isset($_POST["reservePrice"]) &&
    isset($_POST["buyItNowPrice"]) &&
    isset($_POST["duration"])
) {
    $customerID = $_POST["customerID"];
    $itemName = $_POST["itemName"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $startPrice = $_POST["startPrice"];
    $reservePrice = $_POST["reservePrice"];
    $buyItNowPrice = $_POST["buyItNowPrice"];
    $duration = $_POST["duration"];

    $errMsg = "";

    // Basic form validation
    if (
        empty($customerID) ||
        empty($itemName) ||
        empty($category) ||
        empty($description) ||
        empty($startPrice) ||
        empty($reservePrice) ||
        empty($buyItNowPrice) ||
        empty($duration)
    ) {
        $errMsg .= "Please fill out all fields. ";
    }

    if ($startPrice > $reservePrice) {
        $errMsg .= "Start price cannot be greater than reserve price. ";
    }
    if ($reservePrice > $buyItNowPrice) {
        $errMsg .= "Reserve price cannot be greater than buy-it-now price. ";
    }

    if ($errMsg != "") {
        echo "<error>$errMsg</error>";
    } else {
        // Generate item number
        $itemNumber = uniqid();

        // Get current date and time
        date_default_timezone_set('Australia/Sydney');
        $startDate = date("Y-m-d");
        $startTime = date("H:i:s");

        // Initial value for status
        $status = "in_progress";

        // Load existing XML file or create new if doesn't exist
        $xmlFile = '../../../data/auction.xml';
        $doc = new DomDocument();

        if (!file_exists($xmlFile)) {
            $items = $doc->createElement('items');
            $doc->appendChild($items);
        } else {
            $doc->preserveWhiteSpace = FALSE;
            $doc->load($xmlFile);
        }

        $items = $doc->getElementsByTagName('items')->item(0);
        $item = $doc->createElement('item');
        $items->appendChild($item);

        // create a Item Number node ....
        $itemNum = $doc->createElement('ItemNumber');
        $item->appendChild($itemNum);
        $itemNumberValue = $doc->createTextNode($itemNumber);
        $itemNum->appendChild($itemNumberValue);

        // Create a node for Seller ID
        $sellerIDNode = $doc->createElement('SellerID');
        $item->appendChild($sellerIDNode);
        $sellerIDValue = $doc->createTextNode($customerID);
        $sellerIDNode->appendChild($sellerIDValue);

        // Create a node for Item Name
        $itemNameNode = $doc->createElement('ItemName');
        $item->appendChild($itemNameNode);
        $itemNameValue = $doc->createTextNode($itemName);
        $itemNameNode->appendChild($itemNameValue);

        // Create a node for Category
        $categoryNode = $doc->createElement('Category');
        $item->appendChild($categoryNode);
        $categoryValue = $doc->createTextNode($category);
        $categoryNode->appendChild($categoryValue);

        // Create a node for Description
        $descriptionNode = $doc->createElement('Description');
        $item->appendChild($descriptionNode);
        $descriptionValue = $doc->createTextNode($description);
        $descriptionNode->appendChild($descriptionValue);

        // Create a node for Start Price
        $startPriceNode = $doc->createElement('StartPrice');
        $item->appendChild($startPriceNode);
        $startPriceValue = $doc->createTextNode($startPrice);
        $startPriceNode->appendChild($startPriceValue);

        // Create a node for Reserve Price
        $reservePriceNode = $doc->createElement('ReservePrice');
        $item->appendChild($reservePriceNode);
        $reservePriceValue = $doc->createTextNode($reservePrice);
        $reservePriceNode->appendChild($reservePriceValue);

        // Create a node for Buy It Now Price
        $buyItNowPriceNode = $doc->createElement('BuyItNowPrice');
        $item->appendChild($buyItNowPriceNode);
        $buyItNowPriceValue = $doc->createTextNode($buyItNowPrice);
        $buyItNowPriceNode->appendChild($buyItNowPriceValue);

        // Create a node for Start Date
        $startDateNode = $doc->createElement('StartDate');
        $item->appendChild($startDateNode);
        $startDateValue = $doc->createTextNode($startDate);
        $startDateNode->appendChild($startDateValue);

        // Create a node for Start Time
        $startTimeNode = $doc->createElement('StartTime');
        $item->appendChild($startTimeNode);
        $startTimeValue = $doc->createTextNode($startTime);
        $startTimeNode->appendChild($startTimeValue);

        // Create a node for Duration
        $durationNode = $doc->createElement('Duration');
        $item->appendChild($durationNode);
        $durationValue = $doc->createTextNode($duration);
        $durationNode->appendChild($durationValue);

        // Create a node for status
        $statusNode = $doc->createElement('Status');
        $item->appendChild($statusNode);
        $statusValue = $doc->createTextNode($status);
        $statusNode->appendChild($statusValue);

        // Create a node for Latest Bid
        $latestBidNode = $doc->createElement('LatestBid');
        $item->appendChild($latestBidNode);

        // Create a node for Bidder ID
        $bidderIDNode = $doc->createElement('BidderID');
        $latestBidNode->appendChild($bidderIDNode);
        $bidderIDValue = $doc->createTextNode($customerID);
        $bidderIDNode->appendChild($bidderIDValue);

        // Create a node for Bid Price (initially set to start price)
        $bidPriceNode = $doc->createElement('CurrentBidPrice');
        $latestBidNode->appendChild($bidPriceNode);
        $bidPriceValue = $doc->createTextNode($startPrice);
        $bidPriceNode->appendChild($bidPriceValue);

        $doc->formatOutput = true;
        $doc->save($xmlFile);
        echo "Thank you! Your item has been listed in ShopOnline. The item number is $itemNumber, and the bidding starts now: $startTime on $startDate.";
    }
} else {
    echo "Error: Incomplete form data.";
}
