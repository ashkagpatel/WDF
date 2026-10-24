
<?php
require_once "student-auth.php";
require_once "db.php";

$sql = "SELECT id, title, event_date, category, poster
        FROM events
        ORDER BY event_date ASC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Available Events</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION["user_name"] ?? "Student") ?>!
    </p>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($event["title"]) ?></td>
                        <td><?= htmlspecialchars($event["event_date"]) ?></td>
                        <td><?= htmlspecialchars($event["category"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events available.</p>
    <?php endif; ?>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
    <p><a href="logout.php">Logout</a></p>
</div>
</body>
</html>
