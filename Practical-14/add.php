
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentname = trim($_POST["studentname"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $bloodgroup = trim($_POST["bloodgroup"] ?? "");
    $birthdate = trim($_POST["birthdate"] ?? "");
    $birthplace = trim($_POST["birthplace"] ?? "");
    $nationality = trim($_POST["nationality"] ?? "");
    $mothertongue = trim($_POST["mothertongue"] ?? "");
    $mobileno = trim($_POST["mobileno"] ?? "");

    if (
        $studentname === "" || $gender === "" ||
        $bloodgroup === "" || $birthdate === "" ||
        $birthplace === "" || $nationality === "" ||
        $mothertongue === "" || $mobileno === ""
    ) {
        $message = "Please fill in all fields.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO students
            (studentname, gender, bloodgroup, birthdate, birthplace,
             nationality, mothertongue, mobileno)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssss",
            $studentname,
            $gender,
            $bloodgroup,
            $birthdate,
            $birthplace,
            $nationality,
            $mothertongue,
            $mobileno
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: index.php?message=" . urlencode("Student added successfully."));
            exit;
        } else {
            $message = "Failed to add student. Please try again.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Add New Student</h1>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="add.php">
        <label>Student Name</label>
        <input type="text" name="studentname" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
            <option value="Other">Other</option>
        </select>

        <label>Blood Group</label>
        <input type="text" name="bloodgroup" maxlength="5" required>

        <label>Birth Date</label>
        <input type="date" name="birthdate" required>

        <label>Birthplace</label>
        <input type="text" name="birthplace" required>

        <label>Nationality</label>
        <input type="text" name="nationality" required>

        <label>Mother Tongue</label>
        <input type="text" name="mothertongue" required>

        <label>Mobile Number</label>
        <input type="tel" name="mobileno" maxlength="15" required>

        <button type="submit">Add Student</button>
        <a href="index.php">Back to Students</a>
    </form>
</div>
</body>
</html>
