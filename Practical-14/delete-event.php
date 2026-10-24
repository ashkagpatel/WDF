
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
    "SELECT id, title, poster FROM events WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$event) {
    header(
        "Location: events.php?message=" .
        urlencode("Event not found.")
    );
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (($_POST["confirm"] ?? "") === "yes") {
        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM events WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

       
if (mysqli_stmt_execute($stmt) &&
    mysqli_stmt_affected_rows($stmt) === 1) {

    logAudit(
        $conn,
        $_SESSION["user_id"],
        "Event Deleted",
        "Deleted event ID: " . $id . ", Title: " . $event["title"]
    );

    mysqli_stmt_close($stmt);


            // Delete the poster after the event is deleted.
            if (!empty($event["poster"])) {
                $posterPath = __DIR__ . "/" . $event["poster"];
                $uploadFolder = realpath(
                    __DIR__ . "/uploads/posters"
                );

                if (
                    $uploadFolder !== false &&
                    is_file($posterPath) &&
                    realpath(dirname($posterPath)) === $uploadFolder
                ) {
                    unlink($posterPath);
                }
            }

            header(
                "Location: events.php?message=" .
                urlencode("Event deleted successfully.")
            );
            exit;
        } else {
            mysqli_stmt_close($stmt);
            $message = "Failed to delete event.";
        }
    } else {
        header("Location: events.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Delete Event</h1>

    <?php if ($message !== ""): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <p>Are you sure you want to delete this event?</p>

    <p>
        <strong>Event:</strong>
        <?= htmlspecialchars($event["title"]) ?>
    </p>

    <form action="delete-event.php?id=<?= (int)$event["id"] ?>"
          method="POST">
        <button type="submit" name="confirm" value="yes">
            Yes, Delete
        </button>

        <button type="submit" name="confirm" value="no">
            Cancel
        </button>
    </form>
</div>
</body>
</html>
