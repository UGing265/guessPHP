<?php
if (!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die("Name parameter missing");
}

$who = htmlentities($_GET['name']);
$names = array('Rock', 'Paper', 'Scissors');
$human = isset($_POST["human"]) ? $_POST["human"] + 0 : -1;
$computer = rand(0, 2);

function check($human, $computer) {
    if ($human == $computer) return "Tie";
    if ($human == 0 && $computer == 2) return "You Win";
    if ($human == 1 && $computer == 0) return "You Win";
    if ($human == 2 && $computer == 1) return "You Win";
    return "You Lose";
}

if (isset($_POST['logout'])) {
    header('Location: lo_RPS.php');
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Game - Rock Paper Scissors - 48d75f4d</title>
</head>
<body>
  <h1>Rock Paper Scissors</h1>
  <p>Welcome: <?= $who ?></p>

  <form method="post">
    <select name="human">
      <option value="-1">--Select--</option>
      <option value="0">Rock</option>
      <option value="1">Paper</option>
      <option value="2">Scissors</option>
      <option value="3">Test</option>
    </select>
    <input type="submit" value="Play">
    <input type="submit" name="logout" value="Logout">
  </form>

<pre>
<?php
if ($human == -1) {
    echo "Please select a strategy and press Play.\n";
} elseif ($human == 3) {
    for ($c = 0; $c < 3; $c++) {
        for ($h = 0; $h < 3; $h++) {
            $r = check($h, $c);
            echo "Human={$names[$h]} Computer={$names[$c]} Result=$r\n";
        }
    }
} else {
    echo "Your Play={$names[$human]} Computer Play={$names[$computer]} Result=";
    echo check($human, $computer) . "\n";
}
?>
</pre>
</body>
</html>
