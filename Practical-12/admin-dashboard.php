
<?php
require_once "auth.php";

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Practical 12</title>
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

    <h2>Event Management</h2>

    <p>
        <a href="events.php">Manage Events</a>
    </p>

    <p>
        <a href="logout.php">Logout</a>
    </p>
</div>

</body>
</html>
