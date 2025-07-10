<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
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
        header("Location: add.php");
        return;
    }
    if (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        return;
    }
    // Validate positions and education
    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }
    $msg = validateEdu($pdo);
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }
    // Insert Profile
    $stmt = $pdo->prepare('INSERT INTO Profile
      (user_id, first_name, last_name, email, headline, summary)
      VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']
    ));
    $profile_id = $pdo->lastInsertId();

    // Insert Positions
    insertPositions($pdo, $profile_id);

    // Insert Education
    insertEducations($pdo, $profile_id);

    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dương Quốc Thái - Add Profile</title>
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
<div class="container">
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']) ?></h1>
    <?php flashMessages(); ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" size="60"></p>
        <p>Last Name: <input type="text" name="last_name" size="60"></p>
        <p>Email: <input type="text" name="email" size="30"></p>
        <p>Headline:<br/><input type="text" name="headline" size="80"></p>
        <p>Summary:<br/><textarea name="summary" rows="8" cols="80"></textarea></p>
        <!-- Education -->
        <p>
            Education: <input type="submit" id="addEdu" value="+">
            <div id="edu_fields"></div>
        </p>
        <!-- Positions -->
        <p>
            Position: <input type="submit" id="addPos" value="+">
            <div id="position_fields"></div>
        </p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>
<script>
countPos = 0;
countEdu = 0;

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
