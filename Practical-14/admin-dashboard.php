

<?php
require_once "auth.php";
require_once "db.php";

$timeout = 900;

if (
    isset($_SESSION["last_activity"]) &&
    time() - $_SESSION["last_activity"] > $timeout
) {
    $_SESSION = [];
    session_destroy();

    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION["last_activity"] = time();

// Only admin can access this dashboard.
if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    exit("Access denied. Admin only.");
}

$_SESSION["last_activity"] = time();

// Dashboard statistics
$userCount = 0;
$studentCount = 0;
$eventCount = 0;
$latestRegistrations = [];
$dashboardError = "";

try {
    $userResult = $conn->query("SELECT COUNT(*) AS total FROM users");
    $userCount = (int) $userResult->fetch_assoc()["total"];

    $studentResult = $conn->query("SELECT COUNT(*) AS total FROM students");
    $studentCount = (int) $studentResult->fetch_assoc()["total"];

    $eventResult = $conn->query("SELECT COUNT(*) AS total FROM events");
    $eventCount = (int) $eventResult->fetch_assoc()["total"];

    $sql = "SELECT
                r.id,
                u.name AS user_name,
                u.email,
                e.title AS event_title,
                r.registered_at
            FROM registrations r
            INNER JOIN users u ON r.user_id = u.id
            INNER JOIN events e ON r.event_id = e.id
            ORDER BY r.registered_at DESC
            LIMIT 5";

    $registrationResult = $conn->query($sql);

    while ($row = $registrationResult->fetch_assoc()) {
        $latestRegistrations[] = $row;
    }
} catch (Throwable $error) {
    $dashboardError = "Could not load all dashboard data. Please check the database.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Practical 14</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION["user_name"] ?? "Admin") ?>!
    </p>

    <p>You are logged in as an administrator.</p>

    <?php if ($dashboardError !== ""): ?>
        <p><?= htmlspecialchars($dashboardError) ?></p>
    <?php endif; ?>

    <h2>Dashboard Statistics</h2>

    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?= $userCount ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Students</h3>
            <p><?= $studentCount ?></p>
        </div>

        <div class="stat-card">
            <h3>Total Events</h3>
            <p><?= $eventCount ?></p>
        </div>
    </div>

    <h2>Latest Registrations</h2>

    <?php if (count($latestRegistrations) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Event</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestRegistrations as $registration): ?>
                    <tr>
                        <td><?= htmlspecialchars($registration["user_name"]) ?></td>
                        <td><?= htmlspecialchars($registration["email"]) ?></td>
                        <td><?= htmlspecialchars($registration["event_title"]) ?></td>
                        <td><?= htmlspecialchars($registration["registered_at"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No registrations found.</p>
    <?php endif; ?>

    <h2>Menu</h2>

<?php require_once "menu.php"; ?>
</div>

</body>
</html>
