
<?php
require_once "auth.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager - Practical 13</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Event Management - Practical 13</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION["user_name"] ?? "Admin") ?>!</p>

    <h2>Add New Event</h2>

    <form id="eventForm">
        <label for="title">Event Title</label>
        <input type="text" id="title" maxlength="100" required>

        <label for="event_date">Event Date</label>
        <input type="date" id="event_date" required>

        <label for="category">Category</label>
        <input type="text" id="category" maxlength="50" required>

        <button type="submit">Add Event</button>
    </form>

    
<div id="editSection" hidden>
    <h2>Edit Event</h2>

    <form id="editForm">
        <input type="hidden" id="editId">

        <label for="editTitle">Event Title</label>
        <input type="text" id="editTitle" maxlength="100" required>

        <label for="editDate">Event Date</label>
        <input type="date" id="editDate" required>

        <label for="editCategory">Category</label>
        <input type="text" id="editCategory" maxlength="50" required>

        <button type="submit">Update Event</button>
        <button type="button" id="cancelEdit">Cancel</button>
    </form>
</div>


    <p id="message" role="status"></p>

    <h2>All Events</h2>
    <div id="eventList">Loading events...</div>

    <p><a href="admin-dashboard.php">Back to Admin Dashboard</a></p>
    <p><a href="logout.php">Logout</a></p>
</div>

<script src="script.js"></script>
</body>
</html>
