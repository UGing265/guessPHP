<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = "Could not load profile";
    header("Location: index.php");
    return;
}

// Load positions & educations
$positions = loadPos($pdo, $_GET['profile_id']);
$educations = loadEdu($pdo, $_GET['profile_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dương Quốc Thái - View Profile</title>
</head>
<body>
<h1>Profile Information</h1>
<p>First Name: <?= htmlentities($row['first_name']) ?></p>
<p>Last Name: <?= htmlentities($row['last_name']) ?></p>
<p>Email: <?= htmlentities($row['email']) ?></p>
<p>Headline: <br><?= htmlentities($row['headline']) ?></p>
<p>Summary: <br><?= htmlentities($row['summary']) ?></p>

<?php if (count($educations) > 0): ?>
    <p>Education:</p>
    <ul>
        <?php foreach ($educations as $edu): ?>
            <li><?= htmlentities($edu['year']) ?>: <?= htmlentities($edu['name']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (count($positions) > 0): ?>
    <p>Positions:</p>
    <ul>
        <?php foreach ($positions as $pos): ?>
            <li><?= htmlentities($pos['year']) ?>: <?= htmlentities($pos['description']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="index.php">Done</a>
</body>
</html>
