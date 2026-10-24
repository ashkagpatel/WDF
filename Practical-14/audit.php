
<?php
require_once "db.php";

function logAudit($conn, $userId, $action, $details = "")
{
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action, details)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("iss", $userId, $action, $details);
    $stmt->execute();
    $stmt->close();
}
?>
