<?php
session_start();
require_once "pdo.php";
if (!isset($_SESSION['user_id'])) die("ACCESS DENIED");
if (!isset($_GET['profile_id'])) { $_SESSION['error']="Missing profile_id"; header("Location: index.php"); return; }
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute([':pid'=>$_GET['profile_id'], ':uid'=>$_SESSION['user_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) { $_SESSION['error']="Profile not found"; header("Location: index.php"); return; }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid");
    $stmt->execute([':pid'=>$_GET['profile_id'], ':uid'=>$_SESSION['user_id']]);
    $_SESSION['success'] = "Profile deleted";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head><title>Dương Quốc Thái - Delete</title></head>
<body>
<h1>Delete Profile</h1>
<p>Are you sure you want to delete profile for <?= htmlentities($row['first_name'] . ' ' . $row['last_name']) ?>?</p>
<form method="post">
    <input type="submit" name="delete" value="Delete">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>
