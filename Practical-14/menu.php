
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION["role"] ?? "";
?>

<nav class="dynamic-menu">
    <?php if ($role === "admin"): ?>
        <a href="admin-dashboard.php">Admin Dashboard</a>
        <a href="events.php">Manage Events</a>
        <a href="logout.php">Logout</a>

    <?php elseif ($role === "student"): ?>
        <a href="dashboard.php">Student Dashboard</a>
        <a href="student-events.php">View Events</a>
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</nav>
