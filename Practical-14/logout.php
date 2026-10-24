

<?php
session_start();
require_once "db.php";

if (isset($_SESSION["user_id"])) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE users
         SET remember_token_hash = NULL,
             remember_expires_at = NULL
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

setcookie("remember_token", "", [
    "expires" => time() - 3600,
    "path" => "/Practical-10/",
    "httponly" => true,
    "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
    "samesite" => "Lax"
]);

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 3600,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => "Lax"
    ]);
}

session_destroy();

header("Location: login.php?logout=1");
exit;
?>

