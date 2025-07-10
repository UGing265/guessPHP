<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['who']) || !isset($_POST['pass'])) {
        die("User name and password are required");
    }

    $who = $_POST['who'];
    $pass = $_POST['pass'];

    if (strlen($who) < 1 || strlen($pass) < 1) {
        die("User name and password are required");
    }

    if ($pass !== "php123") {
        echo "<p>Incorrect password</p>";
    } else {
        // ✅ Redirect to game.php with GET
        header("Location: RPS.php?name=" . urlencode($who));
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login - Rock Paper Scissors - 48d75f4d</title>
</head>
<body>
  <h1>Please Log In</h1>
  <form method="POST">
    <p>Email: <input type="text" name="who" size="40"></p>
    <p>Password: <input type="password" name="pass" size="40"></p>
    <p><input type="submit" value="Log In"></p>
  </form>
</body>
</html>
