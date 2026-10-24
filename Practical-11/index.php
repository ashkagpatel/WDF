
<?php
require_once "db.php";

$search = trim($_GET["search"] ?? "");
$gender = trim($_GET["gender"] ?? "");

$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (studentname LIKE ? OR mobileno LIKE ?)";
    $value = "%" . $search . "%";
    $params[] = $value;
    $params[] = $value;
    $types .= "ss";
}

if ($gender !== "") {
    $sql .= " AND gender = ?";
    $params[] = $gender;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 11 - Student Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Student Management</h1>

    <?php if (isset($_GET["message"])): ?>
        <p class="success">
            <?= htmlspecialchars($_GET["message"]) ?>
        </p>
    <?php endif; ?>

    <p>
        <a href="add.php">+ Add New Student</a>
    </p>

    <form method="GET" action="index.php">
        <input
            type="text"
            name="search"
            placeholder="Search name or mobile"
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="gender">
            <option value="">All Genders</option>
            <option value="Female" <?= $gender === "Female" ? "selected" : "" ?>>Female</option>
            <option value="Male" <?= $gender === "Male" ? "selected" : "" ?>>Male</option>
            <option value="Other" <?= $gender === "Other" ? "selected" : "" ?>>Other</option>
        </select>

        <button type="submit">Search / Filter</button>
        <a href="index.php">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Blood Group</th>
                    <th>Birth Date</th>
                    <th>Birthplace</th>
                    <th>Nationality</th>
                    <th>Mother Tongue</th>
                    <th>Mobile</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= (int)$row["id"] ?></td>
                        <td><?= htmlspecialchars($row["studentname"]) ?></td>
                        <td><?= htmlspecialchars($row["gender"]) ?></td>
                        <td><?= htmlspecialchars($row["bloodgroup"]) ?></td>
                        <td><?= htmlspecialchars($row["birthdate"]) ?></td>
                        <td><?= htmlspecialchars($row["birthplace"]) ?></td>
                        <td><?= htmlspecialchars($row["nationality"]) ?></td>
                        <td><?= htmlspecialchars($row["mothertongue"]) ?></td>
                        <td><?= htmlspecialchars($row["mobileno"]) ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$row["id"] ?>">Edit</a>
                            |
                            <a href="delete.php?id=<?= (int)$row["id"] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No students found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
