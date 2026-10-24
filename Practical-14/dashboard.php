

<?php
require_once "student-auth.php";

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
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Student Dashboard</h2>

    <p>Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
    </p>

    <p>You are logged in as a student.</p>

    <h2>Menu</h2>

<?php require_once "menu.php"; ?>

    
    
</div>

</body>
</html>
