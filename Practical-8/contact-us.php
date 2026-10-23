<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize data
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $message = htmlspecialchars(trim($_POST["message"] ?? ""));

    $errors = [];

    // Server-side validation

    if ($name == "") {
        $errors[] = "Name is required.";
    } elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {
        $errors[] = "Name should contain characters only.";
    }

    if ($email == "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if ($message == "") {
        $errors[] = "Message is required.";
    }


    // If validation fails
    if (!empty($errors)) {

        echo "<h2>Message Not Sent</h2>";

        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }

        echo "<a href='contact-us.html'>Go Back</a>";

    } else {

        // json file
        $file = "contact-us.json";

        // Read existing data
        if (file_exists($file) && filesize($file) > 0) {

            $jsonData = file_get_contents($file);
            $contacts = json_decode($jsonData, true);

            if (!is_array($contacts)) {
                $contacts = [];
            }

        } else {

            $contacts = [];
        }


        // Add new contact
        $contacts[] = [
            "name" => $name,
            "email" => $email,
            "message" => $message
        ];


        // Save data as JSON
        $json = json_encode($contacts, JSON_PRETTY_PRINT);

        if (file_put_contents($file, $json)) {

            echo "<h2>Message Sent Successfully!</h2>";
            echo "<p>Thank you, <b>$name</b>.</p>";
            echo "<p>Your message has been saved successfully.</p>";
            echo "<a href='contact-us.html'>Back to Contact</a>";

        } else {

            echo "<h2>Error</h2>";
            echo "<p>Could not save your message.</p>";
        }
    }
}

?>