<?php
session_start();
require_once "pdo.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dương Quốc Thái - Automobile Database</title>
</head>
<body>
<h1>Welcome to the Automobiles Database</h1>

<?php
if (!isset($_SESSION['name'])) {
    echo '<p><a href="login.php">Please log in</a></p>';
} else {
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green;">' . htmlentities($_SESSION['success']) . "</p>\n";
        unset($_SESSION['success']);
    }

    $stmt = $pdo->query("SELECT * FROM autos");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) == 0) {
        echo "<p>No rows found</p>";
    } else {
        echo "<table border='1'>
            <tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr>";
        foreach ($rows as $row) {
            echo "<tr><td>" . htmlentities($row['make']) . "</td>";
            echo "<td>" . htmlentities($row['model']) . "</td>";
            echo "<td>" . htmlentities($row['year']) . "</td>";
            echo "<td>" . htmlentities($row['mileage']) . "</td>";
            echo "<td>
                <a href='edit.php?autos_id=" . $row['autos_id'] . "'>Edit</a> /
                <a href='delete.php?autos_id=" . $row['autos_id'] . "'>Delete</a>
                </td></tr>";
        }
        echo "</table>";
    }

    echo '<p><a href="add.php">Add New Entry</a> | <a href="logout.php">Logout</a></p>';
}
?>
</body>
</html>
