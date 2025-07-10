<?php
// autos.php
require_once "pdo.php";

if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1 ) {
    die("Name parameter missing");
}

if ( isset($_POST['logout']) ) {
    header("Location: index.php");
    return;
}

$message = false;
if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
    if ( strlen($_POST['make']) < 1 ) {
        $message = "Make is required";
    } elseif ( ! is_numeric($_POST['year']) || ! is_numeric($_POST['mileage']) ) {
        $message = "Mileage and year must be numeric";
    } else {
        $sql = "INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage'])
        );
        $message = "Record inserted";
    }
}

$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Dương Quốc Thái dc0f0463 - Autos</title>
</head>
<body>
<h1>Tracking Autos for <?= htmlentities($_GET['name']) ?></h1>
<?php
if ( $message !== false ) {
    echo('<p style="color: green;">'.htmlentities($message)."</p>");
}
?>
<form method="post">
<p>Make: <input type="text" name="make" size="40"></p>
<p>Year: <input type="text" name="year"></p>
<p>Mileage: <input type="text" name="mileage"></p>
<input type="submit" value="Add">
<input type="submit" name="logout" value="Logout">
</form>
<h2>Automobiles</h2>
<ul>
<?php
foreach ( $rows as $row ) {
    echo("<li>".htmlentities($row['year'])." ".htmlentities($row['make'])." / ".htmlentities($row['mileage'])."</li>\n");
}
?>
</ul>
</body>
</html>
