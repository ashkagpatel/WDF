
<?php
require_once "db.php";

$sql = "SELECT id, name, email, course FROM users WHERE email = :email";
$stmt = $conn->prepare($sql);

$email = "asha@example.com";
$stmt->execute(["email" => $email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "User found!<br>";
    echo "Name: " . htmlspecialchars($user["name"]) . "<br>";
    echo "Email: " . htmlspecialchars($user["email"]) . "<br>";
    echo "Course: " . htmlspecialchars($user["course"]);
} else {
    echo "User not found.";
}
?>
