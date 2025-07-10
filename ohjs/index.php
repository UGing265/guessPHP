<?php
session_start();
require_once "pdo.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dương Quốc Thái - Profile Database</title>
</head>
<body>
<h1>Welcome to the Profile Database</h1>

<?php
// Hiển thị thông báo thành công
if (isset($_SESSION['success'])) {
    echo '<p style="color:green;">' . htmlentities($_SESSION['success']) . "</p>\n";
    unset($_SESSION['success']);
}

// Hiển thị link login hoặc logout
if (isset($_SESSION['name'])) {
    echo '<p><a href="logout.php">Logout</a></p>';
} else {
    echo '<p><a href="login.php">Please log in</a></p>';
}

// Hiển thị danh sách hồ sơ
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline, user_id FROM Profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) == 0) {
    echo "<p>No profiles found</p>";
} else {
    echo "<table border=\"1\">";
    echo "<tr><th>Name</th><th>Headline</th><th>Action</th></tr>";
    foreach ($rows as $row) {
        echo "<tr><td>" . htmlentities($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>" . htmlentities($row['headline']) . "</td><td>";
        echo '<a href="view.php?profile_id=' . $row['profile_id'] . '">View</a>';
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
            echo ' / <a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a>';
            echo ' / <a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>';
        }
        echo "</td></tr>";
    }
    echo "</table>";
}

// Hiển thị nút thêm đúng chuẩn autograder
if (isset($_SESSION['user_id'])) {
    echo '<p><a href="add.php">Add New Entry</a></p>';
}
?>
</body>
</html>
