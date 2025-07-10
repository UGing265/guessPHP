<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['name'])) {
    die("ACCESS DENIED");
}

if (!isset($_GET['autos_id'])) {
    $_SESSION['error'] = "Missing autos_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :id");
$stmt->execute([':id' => $_GET['autos_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['error'] = "Bad value for autos_id";
    header("Location: index.php");
    return;
}

if (isset($_POST['make'], $_POST['model'], $_POST['year'], $_POST['mileage'])) {
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
        strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?autos_id=" . $_GET['autos_id']);
        return;
    } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Year and mileage must be numeric";
        header("Location: edit.php?autos_id=" . $_GET['autos_id']);
        return;
    } else {
        $stmt = $pdo->prepare("UPDATE autos SET make=:mk, model=:md, year=:yr, mileage=:mi WHERE autos_id=:id");
        $stmt->execute([
            ':mk' => $_POST['make'],
            ':md' => $_POST['model'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage'],
            ':id' => $_GET['autos_id']
        ]);
        $_SESSION['success'] = "Record edited";
        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Dương Quốc Thái - Edit</title></head>
<body>
<h1>Editing Automobile</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
    <p>Make: <input type="text" name="make" value="<?= htmlentities($row['make']) ?>"></p>
    <p>Model: <input type="text" name="model" value="<?= htmlentities($row['model']) ?>"></p>
    <p>Year: <input type="text" name="year" value="<?= htmlentities($row['year']) ?>"></p>
    <p>Mileage: <input type="text" name="mileage" value="<?= htmlentities($row['mileage']) ?>"></p>
    <input type="submit" value="Save">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>
