
<?php
require_once "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");

    if (
        $name === "" || $gender === "" || $email === "" ||
        $password === "" || $confirmPassword === "" ||
        $course === "" || $year === ""
    ) {
        $message = "Please fill in all fields.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";

    } else {
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $message = "This email is already registered.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, gender, email, password, course, year)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $name,
                $gender,
                $email,
                $hashedPassword,
                $course,
                $year
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration successful!";
                $messageType = "success";
            } else {
                $message = "Registration failed. Please try again.";
                $messageType = "error";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>User Registration</h2>

    <?php if ($message !== ""): ?>
        <p class="<?php echo htmlspecialchars($messageType); ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form action="register.php" method="POST" onsubmit="return validateForm()" novalidate>
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required
               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8">

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <label for="course">Course</label>
        <input type="text" id="course" name="course" required
               value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>">

        <label for="year">Year</label>
        <select id="year" name="year" required>
            <option value="">Select Year</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
            <option value="4th Year">4th Year</option>
        </select>

        <button type="submit">Register</button>
    </form>
</div>

<script src="script.js"></script>
</body>
</html>
