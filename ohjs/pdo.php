<?php
// Đổi thông tin nếu bạn dùng user/pass khác
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=wa4e_profile', 'root', '');

// Bắt lỗi PDO dưới dạng Exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
