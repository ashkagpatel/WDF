
<?php
session_start();
require_once "auth.php";


$timeout = 900; // 15 minutes

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

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

if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>

    <p>Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
    </p>

    <p>You are logged in as an administrator.</p>

    <a href="logout.php">Logout</a>
</div>

</body>
</html>
