
<?php
require_once "auth.php";

$sql = "SELECT id, title, event_date, category, poster
        FROM events
        ORDER BY event_date DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Failed to load events.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Practical 12</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Manage Events</h1>

    <?php if (isset($_GET["message"])): ?>
        <p class="success">
            <?= htmlspecialchars($_GET["message"]) ?>
        </p>
    <?php endif; ?>

    <p><a href="add-event.php">+ Add New Event</a></p>
    <p><a href="admin-dashboard.php">Back to Admin Dashboard</a></p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Poster</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($event = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= (int)$event["id"] ?></td>
                            <td><?= htmlspecialchars($event["title"]) ?></td>
                            <td><?= htmlspecialchars($event["event_date"]) ?></td>
                            <td><?= htmlspecialchars($event["category"]) ?></td>
                            <td>
                                <?php if (!empty($event["poster"])): ?>
                                    <img
                                        src="<?= htmlspecialchars($event["poster"]) ?>"
                                        alt="Event poster"
                                        width="90"
                                    >
                                <?php else: ?>
                                    No poster
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit-event.php?id=<?= (int)$event["id"] ?>">Edit</a>
                                |
                                <a href="delete-event.php?id=<?= (int)$event["id"] ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No events found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php
mysqli_free_result($result);
mysqli_close($conn);
?>
