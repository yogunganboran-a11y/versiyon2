<?php
require_once 'includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tckn = trim($_POST['tckn'] ?? '');

    // TCKN validasyonu
    if (empty($tckn)) {
        echo json_encode([
            'success' => false,
            'message' => 'TCKN alanı zorunludur'
        ]);
        exit;
    }

    if (strlen($tckn) != 11 || !ctype_digit($tckn)) {
        echo json_encode([
            'success' => false,
            'message' => 'Geçersiz TCKN formatı'
        ]);
        exit;
    }

    // Veritabanından kullanıcıyı çek
    $user = fetchOne("SELECT * FROM users WHERE tckn = ?", [$tckn]);

    if ($user) {
        // Kullanıcı bulundu, oturum oluştur
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_tckn'] = $user['tckn'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_surname'] = $user['surname'];

        echo json_encode([
            'success' => true,
            'message' => 'Giriş başarılı'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Kullanıcı bulunamadı'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek'
    ]);
}
?>
