<?php
$page_title = 'Kullanıcılar';
include 'includes/header.php';

// Veritabanından kullanıcıları çek
$users = fetchAll("SELECT u.*, 
    (SELECT COUNT(*) FROM user_courses WHERE user_id = u.id) as course_count,
    (SELECT COUNT(*) FROM certificates WHERE user_id = u.id) as certificate_count
    FROM users u ORDER BY created_at DESC");
?>
