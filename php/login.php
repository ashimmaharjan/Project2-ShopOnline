<?php

if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        echo "Please enter both email and password.";
    } else {
        // Load the XML document
        $xmlfile = '../../../data/customer.xml';
        $doc = new DomDocument();
        if (file_exists($xmlfile)) {
            $doc->load($xmlfile);

            $customerFound = false;
            foreach ($doc->getElementsByTagName('customer') as $customer) {
                $customerEmail = $customer->getElementsByTagName('Email')->item(0)->nodeValue;
                $customerPassword = $customer->getElementsByTagName('Password')->item(0)->nodeValue;
                if ($customerEmail === $email && $customerPassword === $password) {
                    $customerFound = true;
                    // Set session variables
                    $_SESSION['customerID'] = $customer->getElementsByTagName('CustomerID')->item(0)->nodeValue;
                    $_SESSION['firstName'] = $customer->getElementsByTagName('FirstName')->item(0)->nodeValue;

                    echo json_encode(array(
                        'customerID' => $_SESSION['customerID'],
                        'firstName' => $_SESSION['firstName']
                    ));
                    exit();
                }
            }

            if (!$customerFound) {
                echo "Invalid email address or password.";
            }
        } else {
            echo "XML Data file not found";
        }
    }
} else {
    die("Invalid request.");
}
