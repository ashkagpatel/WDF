
<?php
require_once "auth.php";
require_once "audit.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $category = trim($_POST["category"] ?? "");

    if ($title === "" || $event_date === "" || $category === "") {
        $message = "Please fill in all event details.";
    } elseif (
        !isset($_FILES["poster"]) ||
        $_FILES["poster"]["error"] !== UPLOAD_ERR_OK
    ) {
        $message = "Please select a poster image.";
    } else {
        $file = $_FILES["poster"];
        $maxSize = 2 * 1024 * 1024;

        $allowedTypes = [
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "png" => "image/png",
            "webp" => "image/webp"
        ];

        $extension = strtolower(
            pathinfo($file["name"], PATHINFO_EXTENSION)
        );

        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file["tmp_name"]);

        if ($file["size"] > $maxSize) {
            $message = "Poster must be 2 MB or smaller.";
        } elseif (
            !isset($allowedTypes[$extension]) ||
            $allowedTypes[$extension] !== $mimeType
        ) {
            $message = "Only JPG, PNG and WEBP images are allowed.";
        } else {
            $uploadDir = __DIR__ . "/uploads/posters/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = bin2hex(random_bytes(16)) . "." . $extension;
            $destination = $uploadDir . $fileName;
            $relativePath = "uploads/posters/" . $fileName;

            if (move_uploaded_file($file["tmp_name"], $destination)) {
                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO events (title, event_date, category, poster)
                     VALUES (?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ssss",
                    $title,
                    $event_date,
                    $category,
                    $relativePath
                );

                
if (mysqli_stmt_execute($stmt)) {
    logAudit(
        $conn,
        $_SESSION["user_id"],
        "Event Added",
        "Added event: " . $title
    );

    mysqli_stmt_close($stmt);

    header(
        "Location: events.php?message=" .
        urlencode("Event added successfully.")
    );
    exit;
}

                else {
                    if (file_exists($destination)) {
                        unlink($destination);
                    }

                    $message = "Failed to save event in the database.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $message = "Poster upload failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Add New Event</h1>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="add-event.php" method="POST" enctype="multipart/form-data">
        <label for="title">Event Title</label>
        <input type="text" id="title" name="title" maxlength="100" required>

        <label for="event_date">Event Date</label>
        <input type="date" id="event_date" name="event_date" required>

        <label for="category">Category</label>
        <input type="text" id="category" name="category" maxlength="50" required>

        <label for="poster">Event Poster (JPG, PNG, WEBP; max 2 MB)</label>
        <input
            type="file"
            id="poster"
            name="poster"
            accept=".jpg,.jpeg,.png,.webp"
            required
        >

        <button type="submit">Add Event</button>
        <a href="events.php">Back to Events</a>
    </form>
</div>
</body>
</html>
