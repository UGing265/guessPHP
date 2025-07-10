<?php
session_start();
require_once "pdo.php";
require_once "util.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dương Quốc Thái - Profile DB</title>
</head>
<body>
<h1>Resume Registry</h1>
<?php
flashMessages();
if (isset($_SESSION['name'])) {
    echo '<p><a href="logout.php">Logout</a></p>';
} else {
    echo '<p><a href="login.php">Please log in</a></p>';
}
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline, user_id FROM Profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) == 0) {
    echo "<p>No profiles found</p>";
} else {
    echo '<table border="1"><tr><th>Name</th><th>Headline</th><th>Action</th></tr>';
    foreach ($rows as $row) {
        $name = htmlentities($row['first_name'] . ' ' . $row['last_name']);
        $headline = htmlentities($row['headline']);
        echo "<tr><td>$name</td><td>$headline</td><td>";
        echo '<a href="view.php?profile_id=' . $row['profile_id'] . '">View</a>';
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
            echo ' / <a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a>';
            echo ' / <a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>';
        }
        echo "</td></tr>";
    }
    echo '</table>';
}
if (isset($_SESSION['user_id'])) {
    echo '<p><a href="add.php">Add New Entry</a></p>';
}
?>
</body>
</html>
