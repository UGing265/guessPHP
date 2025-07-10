<?php
session_start();
require_once "pdo.php";
$salt = 'XyZzy12*_';

// Xử lý khi submit form
if (isset($_POST['email']) && isset($_POST['pass'])) {
    // Validate
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = "Email and password are required";
    } elseif (strpos($_POST['email'], '@') === false) {
        $_SESSION['error'] = "Email must have an at-sign (@)";
    } else {
        $check = hash('md5', $salt . $_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
        $stmt->execute([':em' => $_POST['email'], ':pw' => $check]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            // Đăng nhập thành công
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Incorrect password";
        }
    }
    header("Location: login.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dương Quốc Thái - Login</title>
    <script>
    function doValidate() {
        let addr = document.getElementById('email').value;
        let pw = document.getElementById('id_1723').value;
        if (addr == "" || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if (addr.indexOf('@') == -1) {
            alert("Email address must contain @");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>
<h1>Please Log In</h1>
<?php
if (isset($_SESSION['error'])) {
    echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
?>
<form method="POST">
    <label for="email">Email</label>
    <input type="text" name="email" id="email"><br/>
    <label for="id_1723">Password</label>
    <input type="password" name="pass" id="id_1723"><br/>
    <input type="submit" onclick="return doValidate();" value="Log In">
    <a href="index.php">Cancel</a>
</form>
</body>
</html>
