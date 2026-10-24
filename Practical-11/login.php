
<?php
session_start();
require_once "db.php";

$message = "";

if (isset($_GET["timeout"])) {
    $message = "Your session expired. Please log in again.";
} elseif (isset($_GET["logout"])) {
    $message = "You have logged out successfully.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $message = "Please enter email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password, role
             FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["last_activity"] = time();

            if (isset($_POST["remember_me"])) {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash("sha256", $token);
    $expiresAt = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60));

    $stmtRemember = mysqli_prepare(
        $conn,
        "UPDATE users
         SET remember_token_hash = ?, remember_expires_at = ?
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmtRemember,
        "ssi",
        $tokenHash,
        $expiresAt,
        $user["id"]
    );

    mysqli_stmt_execute($stmtRemember);
    mysqli_stmt_close($stmtRemember);

    setcookie("remember_token", $token, [
        "expires" => time() + (30 * 24 * 60 * 60),
        "path" => "/Practical-10/",
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax"
    ]);
}

            if ($user["role"] === "admin") {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $message = "Invalid email or password.";
        }

        mysqli_stmt_close($stmt);
    }
}
require_once "auth.php";
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 10 - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>User Login</h2>

    <?php if ($message !== ""): ?>
        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            required
            value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <label>
    <input type="checkbox" name="remember_me" value="1">
    Remember Me
</label>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account?
        <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>
