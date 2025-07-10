<!DOCTYPE html>
<html>
<head>
    <title>Guessing Game - 48d75f4d</title>
</head>
<body>
    <h1>Guessing Game</h1>

    <?php
    if (!isset($_GET['guess'])) {
        echo "<p>Missing guess parameter</p>";
    } else {
        $guess = $_GET['guess'];

        if (strlen($guess) < 1) {
            echo "<p>Your guess is too short</p>";
        } elseif (!is_numeric($guess)) {
            echo "<p>Your guess is not a number</p>";
        } else {
            $guess = (int)$guess;
            $correct = 32;

            if ($guess < $correct) {
                echo "<p>Your guess is too low</p>";
            } elseif ($guess > $correct) {
                echo "<p>Your guess is too high</p>";
            } else {
                echo "<p>Congratulations - You are right</p>";
            }
        }
    }
    ?>
</body>
</html>
