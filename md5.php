<!DOCTYPE html>
<html>
<head>
    <title>MD5 Cracker - Dương Quốc Thái</title>
</head>
<body>
    <h1>MD5 PIN Cracker</h1>

    <?php
    $goodtext = "Not found";
    $debug = false;

    if (isset($_GET['md5'])) {
        $time_pre = microtime(true);
        $md5 = $_GET['md5'];

        // Characters used for PIN: 0-9
        $txt = "0123456789";
        $show = 15;  // Show only first 15 attempts
        $count = 0;

        // Try all 4-digit PINs
        for ($i = 0; $i < 10000; $i++) {
            $try = str_pad((string)$i, 4, "0", STR_PAD_LEFT);
            $check = md5($try);

            if ($debug && $show > 0) {
                echo "<p>Trying: $try => $check</p>";
                $show--;
            }

            if ($check == $md5) {
                $goodtext = "PIN found: " . $try;
                break;
            }
        }

        $time_post = microtime(true);
        echo "<p>Elapsed time: ";
        echo $time_post - $time_pre;
        echo " seconds</p>";
    }
    ?>

    <p><?= htmlentities($goodtext); ?></p>

    <form>
        <input type="text" name="md5" size="40" placeholder="Enter MD5 hash">
        <input type="submit" value="Crack">
    </form>
</body>
</html>
