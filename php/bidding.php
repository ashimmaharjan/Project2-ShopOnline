<?php

header('Content-Type: text/xml');

$xmlFile = '../../../data/auction.xml';
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->load($xmlFile);
date_default_timezone_set('Australia/Sydney');

// Handle fetch, place bid, and buy operations based on the request type
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    fetchItems();
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];
    if ($action === "placeBid") {
        // Handle place bid operation
        if (isset($_POST["itemNumber"]) && isset($_POST["newBidPrice"]) && isset($_POST["bidderID"])) {
            $itemNumber = $_POST["itemNumber"];
            $newBidPrice = $_POST["newBidPrice"];
            $bidderID = $_POST["bidderID"];
            placeBid($itemNumber, $newBidPrice, $bidderID);
        } else {
            echo "Error: Incomplete bid data.";
        }
    } elseif ($action === "buyItem") {
        // Handle buy item operation
        if (isset($_POST["itemNumber"]) && isset($_POST["bidderID"])) {
            $itemNumber = $_POST["itemNumber"];
            $bidderID = $_POST["bidderID"];
            buyItem($itemNumber, $bidderID);
        } else {
            echo "Error: Incomplete buy item data.";
        }
    }
}

// Function to fetch items
function fetchItems()
{
    global $doc;
    $items = [];
    $itemsNode = $doc->getElementsByTagName('item');
    foreach ($itemsNode as $itemNode) {
        $item = [];
        $item['itemNumber'] = $itemNode->getElementsByTagName('ItemNumber')->item(0)->nodeValue;
        $item['itemName'] = $itemNode->getElementsByTagName('ItemName')->item(0)->nodeValue;
        $item['category'] = $itemNode->getElementsByTagName('Category')->item(0)->nodeValue;
        $item['description'] = $itemNode->getElementsByTagName('Description')->item(0)->nodeValue;
        $item['buyItNowPrice'] = $itemNode->getElementsByTagName('BuyItNowPrice')->item(0)->nodeValue;
        $item['currentBidPrice'] = $itemNode->getElementsByTagName('CurrentBidPrice')->item(0)->nodeValue;

        // Fetching start date, start time, and duration
        $startDate = $itemNode->getElementsByTagName('StartDate')->item(0)->nodeValue;
        $startTime = $itemNode->getElementsByTagName('StartTime')->item(0)->nodeValue;
        $durationInMinutes = $itemNode->getElementsByTagName('Duration')->item(0)->nodeValue;

        // Calculate time left
        $item['timeLeft'] = calculateTimeLeft($startDate, $startTime, $durationInMinutes);

        $item['sold'] = $itemNode->getElementsByTagName('Status')->item(0)->nodeValue === 'sold';
        array_push($items, $item);
    }
    echo json_encode($items);
}


// Function to calculate time left
function calculateTimeLeft($startDate, $startTime, $durationInMinutes)
{
    $auctionStartTime = strtotime($startDate . ' ' . $startTime);
    $auctionEndTime = $auctionStartTime + ($durationInMinutes * 60);

    $currentTime = time();
    $timeLeft = max(0, $auctionEndTime - $currentTime);

    return $timeLeft;
}


// Function to place bid
function placeBid($itemNumber, $newBidPrice, $bidderID)
{
    global $doc;
    global $xmlFile;
    $items = $doc->getElementsByTagName('item');
    foreach ($items as $item) {
        $itemNum = $item->getElementsByTagName('ItemNumber')->item(0)->nodeValue;
        if ($itemNum === $itemNumber) {
            $currentBidPrice = $item->getElementsByTagName('CurrentBidPrice')->item(0)->nodeValue;

            // Check if the new bid price is greater than the current bid price
            if ($newBidPrice <= $currentBidPrice) {
                echo "Sorry, your bid is not valid.";
                return;
            }

            // Update the current bid price
            $currentBidPriceNode = $item->getElementsByTagName('CurrentBidPrice')->item(0);
            $currentBidPriceNode->nodeValue = $newBidPrice;

            // Update the bidder ID
            $latestBid = $item->getElementsByTagName('LatestBid')->item(0);
            $latestBidderID = $latestBid->getElementsByTagName('BidderID')->item(0);
            $latestBidderID->nodeValue = $bidderID;

            $doc->formatOutput = true;
            $doc->save($xmlFile);

            echo "Thank you! Your bid is recorded in ShopOnline.";
            return;
        }
    }
    echo "Error: Item not found.";
}

// Function to buy item
function buyItem($itemNumber, $bidderID)
{
    global $doc;
    global $xmlFile;
    $items = $doc->getElementsByTagName('item');
    foreach ($items as $item) {
        $itemNum = $item->getElementsByTagName('ItemNumber')->item(0)->nodeValue;
        if ($itemNum === $itemNumber) {
            // Update the current bid price
            $currentBidPrice = $item->getElementsByTagName('CurrentBidPrice')->item(0);
            $currentBidPrice->nodeValue = $item->getElementsByTagName('BuyItNowPrice')->item(0)->nodeValue;

            // Update the bidder ID
            $latestBid = $item->getElementsByTagName('LatestBid')->item(0);
            $latestBidderID = $latestBid->getElementsByTagName('BidderID')->item(0);
            $latestBidderID->nodeValue = $bidderID;

            // Set item status to sold
            $item->getElementsByTagName('Status')->item(0)->nodeValue = 'sold';

            $doc->formatOutput = true;
            $doc->save($xmlFile);
            echo "Thank you for purchasing this item.";
            return;
        }
    }
    echo "Error: Item not found.";
}
