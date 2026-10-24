
<?php
require_once "auth.php";
require_once "audit.php";

$message = "";
$event = null;
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: events.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, title, event_date, category, poster
     FROM events WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$event) {
    header("Location: events.php?message=" . urlencode("Event not found."));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $newPoster = $event["poster"];
    $uploadedFile = null;

    if ($title === "" || $event_date === "" || $category === "") {
        $message = "Please fill in all event details.";
    } elseif (
        !isset($_FILES["poster"]) ||
        $_FILES["poster"]["error"] === UPLOAD_ERR_NO_FILE
    ) {
        // Keep the existing poster.
    } elseif ($_FILES["poster"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Poster upload failed. Please try again.";
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

            if (move_uploaded_file($file["tmp_name"], $destination)) {
                $newPoster = "uploads/posters/" . $fileName;
                $uploadedFile = $destination;
            } else {
                $message = "Poster upload failed. Please try again.";
            }
        }
    }

    if ($message === "") {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE events
             SET title = ?, event_date = ?, category = ?, poster = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $title,
            $event_date,
            $category,
            $newPoster,
            $id
        );

      if (mysqli_stmt_execute($stmt)) {
    logAudit(
        $conn,
        $_SESSION["user_id"],
        "Event Updated",
        "Updated event ID: " . $id . ", Title: " . $title
    );

    mysqli_stmt_close($stmt);

            // Remove the old poster only after successful update.
            if (
                $uploadedFile !== null &&
                !empty($event["poster"])
            ) {
                $oldPoster = __DIR__ . "/" . $event["poster"];

                if (
                    is_file($oldPoster) &&
                    realpath(dirname($oldPoster)) ===
                    realpath(__DIR__ . "/uploads/posters")
                ) {
                    unlink($oldPoster);
                }
            }

            header(
                "Location: events.php?message=" .
                urlencode("Event updated successfully.")
            );
            exit;
        } else {
            if ($uploadedFile !== null && is_file($uploadedFile)) {
                unlink($uploadedFile);
            }

            mysqli_stmt_close($stmt);
            $message = "Failed to update event.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Event</h1>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="edit-event.php?id=<?= (int)$event["id"] ?>"
          method="POST" enctype="multipart/form-data">

        <label for="title">Event Title</label>
        <input type="text" id="title" name="title"
               maxlength="100" required
               value="<?= htmlspecialchars($event["title"]) ?>">

        <label for="event_date">Event Date</label>
        <input type="date" id="event_date" name="event_date"
               required
               value="<?= htmlspecialchars($event["event_date"]) ?>">

        <label for="category">Category</label>
        <input type="text" id="category" name="category"
               maxlength="50" required
               value="<?= htmlspecialchars($event["category"]) ?>">

        <p>Current Poster:</p>
        <?php if (!empty($event["poster"])): ?>
            <img src="<?= htmlspecialchars($event["poster"]) ?>"
                 alt="Current event poster" width="120">
        <?php else: ?>
            <p>No poster uploaded.</p>
        <?php endif; ?>

        <label for="poster">Choose New Poster (Optional)</label>
        <input type="file" id="poster" name="poster"
               accept=".jpg,.jpeg,.png,.webp">
        <small>JPG, PNG or WEBP; maximum size 2 MB.</small>

        <p>
            <button type="submit">Update Event</button>
            <a href="events.php">Back to Events</a>
        </p>
    </form>
</div>
</body>
</html>
