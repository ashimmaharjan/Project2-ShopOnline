<?php

header('Content-Type: text/xml');

$xmlFile = '../../../data/auction.xml';
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->load($xmlFile);
date_default_timezone_set('Australia/Sydney');

// Handle maintenance actions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];
    if ($action === "processAuctionItems") {
        processAuctionItems();
    } elseif ($action === "generateReport") {
        generateReport();
    }
}

// Function to process auction items
function processAuctionItems()
{
    global $doc;
    global $xmlFile;
    $items = $doc->getElementsByTagName('item');
    $itemsProcessed = false; // Flag to track if any items were processed

    foreach ($items as $item) {
        $status = $item->getElementsByTagName('Status')->item(0)->nodeValue;
        if ($status === "in_progress") {
            // Fetching start date, start time, and duration
            $startDate = $item->getElementsByTagName('StartDate')->item(0)->nodeValue;
            $startTime = $item->getElementsByTagName('StartTime')->item(0)->nodeValue;
            $durationInMinutes = $item->getElementsByTagName('Duration')->item(0)->nodeValue;

            // Calculate time left
            $timeLeft = calculateTimeLeft($startDate, $startTime, $durationInMinutes);

            if ($timeLeft <= 0) {
                $currentBidPrice = $item->getElementsByTagName('LatestBid')->item(0)->getElementsByTagName('CurrentBidPrice')->item(0)->nodeValue;
                $reservePrice = $item->getElementsByTagName('ReservePrice')->item(0)->nodeValue;

                if ($currentBidPrice < $reservePrice) {
                    $statusNode = $item->getElementsByTagName('Status')->item(0);
                    $statusNode->nodeValue = "failed";
                } else {
                    $statusNode = $item->getElementsByTagName('Status')->item(0);
                    $statusNode->nodeValue = "sold";
                }
                $itemsProcessed = true;
            }
        }
    }

    if (!$itemsProcessed) {
        echo "No items need processing at the moment.";
        return;
    }

    $doc->formatOutput = true;
    $doc->save($xmlFile);
    echo "Auction items processed successfully.";
}

// Function to generate report
function generateReport()
{
    global $xmlFile;

    if (!file_exists($xmlFile)) {
        http_response_code(404);
        echo "XML file not found.";
        return;
    }

    // Read XML file
    $xmlContent = file_get_contents($xmlFile);

    // Output XML file
    header('Content-Type: application/xml');
    echo $xmlContent;

    removeSoldOrFailedItems();
}

function removeSoldOrFailedItems()
{
    global $doc;
    global $xmlFile;

    // Convert NodeList to array
    $itemsToRemove = iterator_to_array($doc->getElementsByTagName('item'));
    foreach ($itemsToRemove as $item) {
        $status = $item->getElementsByTagName('Status')->item(0)->nodeValue;
        if ($status === "sold" || $status === "failed") {
            $item->parentNode->removeChild($item);
        }
    }

    $doc->formatOutput = true;
    $doc->save($xmlFile);
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
