
<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid student ID.");
}

$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    die("Student not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            header("Location: index.php?message=" . urlencode("Student deleted successfully."));
            exit;
        } else {
            $message = "Student could not be deleted.";
        }
    } else {
        $message = "Failed to delete student. Please try again.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Delete Student</h1>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <p>Are you sure you want to delete this student?</p>
    <p><strong>Name:</strong> <?= htmlspecialchars($student["studentname"]) ?></p>
    <p><strong>Mobile:</strong> <?= htmlspecialchars($student["mobileno"]) ?></p>

    <form method="POST" action="delete.php?id=<?= (int)$id ?>">
        <button type="submit">Yes, Delete Student</button>
        <a href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
