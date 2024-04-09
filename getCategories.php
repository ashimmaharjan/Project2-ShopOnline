<?php

header('Content-Type: text/xml');

$xmlFile = '../../data/auction.xml';
$categories = array();

if (file_exists($xmlFile)) {
    $doc = new DOMDocument();
    $doc->load($xmlFile);
    $items = $doc->getElementsByTagName('item');
    foreach ($items as $item) {
        $category = $item->getElementsByTagName('Category')->item(0)->nodeValue;
        if (!in_array($category, $categories)) {
            $categories[] = $category;
        }
    }
}

$categories[] = "Other";

echo json_encode($categories);
