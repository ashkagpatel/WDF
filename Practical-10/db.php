

<?php
$host = "localhost";
$dbname = "studenthub";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed.");
}
?>
