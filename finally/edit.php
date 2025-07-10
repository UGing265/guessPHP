<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) die("ACCESS DENIED");

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

// Load profile for validation
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid");
$stmt->execute(array(":pid" => $_GET['profile_id'], ":uid" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = "Could not load profile";
    header("Location: index.php");
    return;
}

// Handle cancel
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate profile fields
    if (
        empty($_POST['first_name']) || empty($_POST['last_name']) ||
        empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary'])
    ) {
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }
    // Validate positions and education
    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }
    $msg = validateEdu($pdo);
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_GET['profile_id']);
        return;
    }
    // Update profile
    $stmt = $pdo->prepare('UPDATE Profile SET
      first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
      WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_GET['profile_id'],
        ':uid' => $_SESSION['user_id']
    ));

    // Remove old positions/education
    $stmt = $pdo->prepare("DELETE FROM Position WHERE profile_id = :pid");
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $stmt = $pdo->prepare("DELETE FROM Education WHERE profile_id = :pid");
    $stmt->execute(array(':pid' => $_GET['profile_id']));

    // Insert positions/education
    insertPositions($pdo, $_GET['profile_id']);
    insertEducations($pdo, $_GET['profile_id']);

    $_SESSION['success'] = "Profile updated";
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
    <title>Dương Quốc Thái - Edit Profile</title>
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
<div class="container">
    <h1>Editing Profile for <?= htmlentities($_SESSION['name']) ?></h1>
    <?php flashMessages(); ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" size="60" value="<?= htmlentities($row['first_name']) ?>"></p>
        <p>Last Name: <input type="text" name="last_name" size="60" value="<?= htmlentities($row['last_name']) ?>"></p>
        <p>Email: <input type="text" name="email" size="30" value="<?= htmlentities($row['email']) ?>"></p>
        <p>Headline:<br/><input type="text" name="headline" size="80" value="<?= htmlentities($row['headline']) ?>"></p>
        <p>Summary:<br/><textarea name="summary" rows="8" cols="80"><?= htmlentities($row['summary']) ?></textarea></p>
        <!-- Education -->
        <p>
            Education: <input type="submit" id="addEdu" value="+">
            <div id="edu_fields">
            <?php
            $edu_num = 0;
            foreach ($educations as $edu) {
                $edu_num++;
                echo '<div id="edu' . $edu_num . '">';
                echo '<p>Year: <input type="text" name="edu_year' . $edu_num . '" value="' . htmlentities($edu['year']) . '" />';
                echo '<input type="button" value="-" onclick="$(\'#edu' . $edu_num . '\').remove();return false;"></p>';
                echo 'School: <input type="text" size="80" name="edu_school' . $edu_num . '" class="school" value="' . htmlentities($edu['name']) . '" />';
                echo '</div>';
            }
            ?>
            </div>
        </p>
        <!-- Positions -->
        <p>
            Position: <input type="submit" id="addPos" value="+">
            <div id="position_fields">
            <?php
            $pos_num = 0;
            foreach ($positions as $pos) {
                $pos_num++;
                echo '<div id="position' . $pos_num . '">';
                echo '<p>Year: <input type="text" name="year' . $pos_num . '" value="' . htmlentities($pos['year']) . '" />';
                echo '<input type="button" value="-" onclick="$(\'#position' . $pos_num . '\').remove();return false;"></p>';
                echo '<textarea name="desc' . $pos_num . '" rows="8" cols="80">' . htmlentities($pos['description']) . '</textarea>';
                echo '</div>';
            }
            ?>
            </div>
        </p>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>
<script>
countPos = <?= $pos_num ?>;
countEdu = <?= $edu_num ?>;

$(document).ready(function () {
    window.console && console.log('Document ready called');

    $('#addPos').click(function (event) {
        event.preventDefault();
        if (countPos >= 9) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        $('#position_fields').append(
            '<div id="position' + countPos + '">' +
            '<p>Year: <input type="text" name="year' + countPos + '" value="" />' +
            '<input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;"></p>' +
            '<textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>' +
            '</div>');
    });

    $('#addEdu').click(function (event) {
        event.preventDefault();
        if (countEdu >= 9) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        $('#edu_fields').append(
            '<div id="edu' + countEdu + '">' +
            '<p>Year: <input type="text" name="edu_year' + countEdu + '" value="" />' +
            '<input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"></p>' +
            'School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />' +
            '</div>');
        $('.school').autocomplete({ source: "school.php" });
    });

    $('.school').autocomplete({ source: "school.php" });
});
</script>
</body>
</html>
