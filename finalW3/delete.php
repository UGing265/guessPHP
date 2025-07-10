<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['delete']) && isset($_POST['autos_id'])) {
    $stmt = $pdo->prepare("DELETE FROM autos WHERE autos_id = :id");
    $stmt->execute([':id' => $_POST['autos_id']]);
    $_SESSION['success'] = "Record deleted";
    header("Location: index.php");
    return;
}

if (!isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT make FROM autos WHERE autos_id = :id");
$stmt->execute([':id' => $_GET['autos_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    $_SESSION['error'] = "Bad value for autos_id";
    header("Location: index.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head><title>Dương Quốc Thái - Delete</title></head>
<body>
<h1>Confirm Deletion</h1>
<p>Are you sure you want to delete: <?= htmlentities($row['make']) ?>?</p>
<form method="post">
    <input type="hidden" name="autos_id" value="<?= $_GET['autos_id'] ?>">
    <input type="submit" name="delete" value="Delete">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>
