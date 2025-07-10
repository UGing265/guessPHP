<?php
function flashMessages() {
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green;">' . htmlentities($_SESSION['success']) . "</p>\n";
        unset($_SESSION['success']);
    }
}
function validatePos() {
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i]) && !isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i] ?? '';
        $desc = $_POST['desc' . $i] ?? '';
        if (strlen($year) == 0 || strlen($desc) == 0) return "All fields are required";
        if (!is_numeric($year)) return "Position year must be numeric";
    }
    return true;
}
function loadPos($pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank');
    $stmt->execute([':pid' => $profile_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
