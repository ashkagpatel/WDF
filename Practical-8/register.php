<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize form data
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $course = htmlspecialchars(trim($_POST["course"] ?? ""));
    $year = htmlspecialchars(trim($_POST["year"] ?? ""));
    $gender = htmlspecialchars(trim($_POST["gender"] ?? ""));
    $terms = isset($_POST["terms"]);

    $errors = [];

    // Server-side validation

    if ($name == "") {
        $errors[] = "Full name is required.";
    } elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {
        $errors[] = "Name should contain characters only.";
    }

    if ($email == "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if ($password == "") {
        $errors[] = "Password is required.";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*[0-9]).{8,}$/", $password)) {
        $errors[] = "Password must contain 8 characters, letters and numbers.";
    }

    if ($confirm_password == "") {
        $errors[] = "Confirm password is required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if ($course == "") {
        $errors[] = "Please select your course.";
    }

    if ($year == "") {
        $errors[] = "Please select your year.";
    }

    if ($gender == "") {
        $errors[] = "Please select your gender.";
    }

    if (!$terms) {
        $errors[] = "Please accept the Terms & Conditions.";
    }


    // If there are errors
    if (!empty($errors)) {

        echo "<h2>Registration Failed</h2>";

        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }

        echo "<a href='register.html'>Go Back</a>";

    } else {

        // Hash password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // JSON file
        $file = "register.json";

        // Read existing JSON data
        if (file_exists($file) && filesize($file) > 0) {

            $jsonData = file_get_contents($file);
            $registrations = json_decode($jsonData, true);

            if (!is_array($registrations)) {
                $registrations = [];
            }

        } else {

            $registrations = [];
        }


        // Add new registration
        $registrations[] = [
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword,
            "course" => $course,
            "year" => $year,
            "gender" => $gender
        ];


        // Save data as JSON
        $json = json_encode($registrations, JSON_PRETTY_PRINT);

        if (file_put_contents($file, $json)) {

            echo "<h2>Registration Successful!</h2>";
            echo "<p>Thank you, <b>$name</b>.</p>";
            echo "<p>Your registration data has been saved successfully.</p>";
            echo "<a href='login.html'>Go to Login</a>";

        } else {

            echo "<h2>Error</h2>";
            echo "<p>Could not save your registration data.</p>";
        }
    }
}

?>