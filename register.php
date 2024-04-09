<?php

header('Content-Type: text/xml');

if (isset($_GET["firstName"]) && isset($_GET["lastName"]) && isset($_GET["email"]) && isset($_GET["password"]) && isset($_GET["confirmPassword"])) {

    $firstName = $_GET["firstName"];
    $lastName = $_GET["lastName"];
    $email = $_GET["email"];
    $password = $_GET["password"];
    $confirmPassword = $_GET["confirmPassword"];

    $errMsg = "";
    if (empty($firstName)) {
        $errMsg .= "Please enter first name. <br />";
    }

    if (empty($lastName)) {
        $errMsg .= "Please enter last name. <br />";
    }

    if (empty($email)) {
        $errMsg .= "Please enter an email id. <br />";
    } elseif (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/", $email)) {
        $errMsg .= "Invalid email address format. <br />";
    }

    if (empty($password)) {
        $errMsg .= "Please enter a password. <br />";
    }
    if (empty($confirmPassword)) {
        $errMsg .= "Please re-enter your password. <br />";
    }

    if ($password != $confirmPassword) {
        $errMsg .= "Your passwords do not match.. <br />";
    }

    if ($errMsg != "") {
        echo $errMsg;
    } else {

        $xmlfile = '../../data/customer.xml';

        $doc = new DomDocument();

        if (!file_exists($xmlfile)) {
            $customers = $doc->createElement('customers');
            $doc->appendChild($customers);
        } else {
            $doc->preserveWhiteSpace = FALSE;
            $doc->load($xmlfile);
        }

        $emailExists = false;
        foreach ($doc->getElementsByTagName('Email') as $existingEmail) {
            if ($existingEmail->nodeValue == $email) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            echo "Email already exists. Please use a different email.";
        } else {
            //create a customer node under customers node
            $customers = $doc->getElementsByTagName('customers')->item(0);
            $customer = $doc->createElement('customer');
            $customers->appendChild($customer);

            // Generate customer ID
            $customerID = uniqid();

            // create a Customer ID node ....
            $ID = $doc->createElement('CustomerID');
            $customer->appendChild($ID);
            $idValue = $doc->createTextNode($customerID);
            $ID->appendChild($idValue);

            // create a First Name node ....
            $FirstName = $doc->createElement('FirstName');
            $customer->appendChild($FirstName);
            $FirstNameValue = $doc->createTextNode($firstName);
            $FirstName->appendChild($FirstNameValue);

            // create a Last Name node ....
            $LastName = $doc->createElement('LastName');
            $customer->appendChild($LastName);
            $LastNameValue = $doc->createTextNode($lastName);
            $LastName->appendChild($LastNameValue);

            //create a Email node ....
            $Email = $doc->createElement('Email');
            $customer->appendChild($Email);
            $emailValue = $doc->createTextNode($email);
            $Email->appendChild($emailValue);

            //create a pwd node ....
            $pwd = $doc->createElement('Password');
            $customer->appendChild($pwd);
            $pwdValue = $doc->createTextNode($password);
            $pwd->appendChild($pwdValue);

            //save the xml file
            $doc->formatOutput = true;
            $doc->save($xmlfile);
            echo "Dear $firstName, you have successfully registered, a confirmation email has been sent to $email";

            // Send welcome email
            $to = $email;
            $subject = "Welcome to ShopOnline!";
            $message = "Dear $firstName, welcome to use ShopOnline! Your customer id is $customerID and the password is $password.";
            $headers = "From: registration@shoponline.com.au";
            mail($to, $subject, $message, $headers);
        }
    }
}
