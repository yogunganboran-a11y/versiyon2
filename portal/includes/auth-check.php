<?php
require_once 'config.php';

// Kullanıcı oturumu kontrolü
checkUserAuth();

// Kullanıcı bilgilerini al
$user = getUserInfo();

// Eğer kullanıcı bilgisi yoksa çıkış yap
if (!$user) {
    session_destroy();
    header('Location: giris.php');
    exit;
}
?>
