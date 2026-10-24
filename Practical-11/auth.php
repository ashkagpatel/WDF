
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";

function restoreRememberedLogin($conn)
{
    if (isset($_SESSION["user_id"])) {
        return;
    }

    if (empty($_COOKIE["remember_token"])) {
        return;
    }

    $token = $_COOKIE["remember_token"];
    $tokenHash = hash("sha256", $token);

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, email, role
         FROM users
         WHERE remember_token_hash = ?
         AND remember_expires_at > NOW()"
    );

    mysqli_stmt_bind_param($stmt, "s", $tokenHash);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($user) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["user_email"] = $user["email"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["last_activity"] = time();
    } else {
        setcookie("remember_token", "", [
            "expires" => time() - 3600,
            "path" => "/Practical-10/",
            "httponly" => true,
            "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
            "samesite" => "Lax"
        ]);
    }
}

restoreRememberedLogin($conn);
?>
