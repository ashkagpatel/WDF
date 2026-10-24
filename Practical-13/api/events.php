
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once "../auth.php";

$method = $_SERVER["REQUEST_METHOD"];

function sendResponse($status, $data)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function readJson()
{
    $data = json_decode(file_get_contents("php://input"), true);
    return is_array($data) ? $data : null;
}

function validEvent($data)
{
    if (!is_array($data)) {
        return false;
    }

    $title = trim($data["title"] ?? "");
    $date = trim($data["event_date"] ?? "");
    $category = trim($data["category"] ?? "");

    $parsedDate = DateTime::createFromFormat("!Y-m-d", $date);

    return $title !== ""
        && strlen($title) <= 100
        && $category !== ""
        && strlen($category) <= 50
        && $parsedDate !== false
        && $parsedDate->format("Y-m-d") === $date;
}

if ($method === "GET") {
    $result = mysqli_query(
        $conn,
        "SELECT id, title, event_date, category, poster
         FROM events ORDER BY event_date DESC"
    );

    if (!$result) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to fetch events."
        ]);
    }

    $events = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }

    mysqli_free_result($result);

    sendResponse(200, [
        "success" => true,
        "events" => $events
    ]);
}

if ($method === "POST") {
    $data = readJson();

    if (!validEvent($data)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Enter a valid title, date and category."
        ]);
    }

    $title = trim($data["title"]);
    $date = trim($data["event_date"]);
    $category = trim($data["category"]);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO events (title, event_date, category)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "sss", $title, $date, $category);

    if (mysqli_stmt_execute($stmt)) {
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        sendResponse(201, [
            "success" => true,
            "message" => "Event added successfully.",
            "id" => $id
        ]);
    }

    mysqli_stmt_close($stmt);

    sendResponse(500, [
        "success" => false,
        "message" => "Failed to add event."
    ]);
}

if ($method === "PUT") {
    $data = readJson();
    $id = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1 || !validEvent($data)) {
        sendResponse(400, [
            "success" => false,
            "message" => "Enter a valid event ID and details."
        ]);
    }

    $title = trim($data["title"]);
    $date = trim($data["event_date"]);
    $category = trim($data["category"]);

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE events
         SET title = ?, event_date = ?, category = ?
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $title,
        $date,
        $category,
        $id
    );

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        sendResponse(500, [
            "success" => false,
            "message" => "Failed to update event."
        ]);
    }

    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected === 0) {
        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM events WHERE id = ?"
        );
        mysqli_stmt_bind_param($check, "i", $id);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);
        $exists = mysqli_fetch_assoc($result);
        mysqli_stmt_close($check);

        if (!$exists) {
            sendResponse(404, [
                "success" => false,
                "message" => "Event not found."
            ]);
        }
    }

    sendResponse(200, [
        "success" => true,
        "message" => "Event updated successfully."
    ]);
}

if ($method === "DELETE") {
    $data = readJson();
    $id = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        sendResponse(400, [
            "success" => false,
            "message" => "Valid event ID is required."
        ]);
    }

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM events WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if (!$success) {
        sendResponse(500, [
            "success" => false,
            "message" => "Failed to delete event."
        ]);
    }

    if ($affected === 0) {
        sendResponse(404, [
            "success" => false,
            "message" => "Event not found."
        ]);
    }

    sendResponse(200, [
        "success" => true,
        "message" => "Event deleted successfully."
    ]);
}

header("Allow: GET, POST, PUT, DELETE");

sendResponse(405, [
    "success" => false,
    "message" => "Method not allowed."
]);
?>
