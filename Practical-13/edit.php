
<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$message = "";

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
            "UPDATE students SET
                studentname = ?, gender = ?, bloodgroup = ?,
                birthdate = ?, birthplace = ?, nationality = ?,
                mothertongue = ?, mobileno = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssi",
            $studentname,
            $gender,
            $bloodgroup,
            $birthdate,
            $birthplace,
            $nationality,
            $mothertongue,
            $mobileno,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: index.php?message=" . urlencode("Student updated successfully."));
            exit;
        } else {
            $message = "Failed to update student. Please try again.";
        }

        mysqli_stmt_close($stmt);
    }

    $student["studentname"] = $studentname;
    $student["gender"] = $gender;
    $student["bloodgroup"] = $bloodgroup;
    $student["birthdate"] = $birthdate;
    $student["birthplace"] = $birthplace;
    $student["nationality"] = $nationality;
    $student["mothertongue"] = $mothertongue;
    $student["mobileno"] = $mobileno;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Student</h1>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= (int)$id ?>">
        <label>Student Name</label>
        <input type="text" name="studentname" value="<?= htmlspecialchars($student["studentname"]) ?>" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Female" <?= $student["gender"] === "Female" ? "selected" : "" ?>>Female</option>
            <option value="Male" <?= $student["gender"] === "Male" ? "selected" : "" ?>>Male</option>
            <option value="Other" <?= $student["gender"] === "Other" ? "selected" : "" ?>>Other</option>
        </select>

        <label>Blood Group</label>
        <input type="text" name="bloodgroup" maxlength="5" value="<?= htmlspecialchars($student["bloodgroup"]) ?>" required>

        <label>Birth Date</label>
        <input type="date" name="birthdate" value="<?= htmlspecialchars($student["birthdate"]) ?>" required>

        <label>Birthplace</label>
        <input type="text" name="birthplace" value="<?= htmlspecialchars($student["birthplace"]) ?>" required>

        <label>Nationality</label>
        <input type="text" name="nationality" value="<?= htmlspecialchars($student["nationality"]) ?>" required>

        <label>Mother Tongue</label>
        <input type="text" name="mothertongue" value="<?= htmlspecialchars($student["mothertongue"]) ?>" required>

        <label>Mobile Number</label>
        <input type="tel" name="mobileno" maxlength="15" value="<?= htmlspecialchars($student["mobileno"]) ?>" required>

        <button type="submit">Update Student</button>
        <a href="index.php">Back to Students</a>
    </form>
</div>
</body>
</html>
