<?php
/**
 * Ana Konfigürasyon Dosyası
 * Hem Admin hem Portal tarafından kullanılır
 */

// Oturum başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug modu (Production'da false yapın!)
define('DEBUG_MODE', true);

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/db.php';

// Timezone ayarla
date_default_timezone_set('Europe/Istanbul');

// Hata raporlama
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Proje root path'leri
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('PORTAL_PATH', ROOT_PATH . '/portal');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/portal/dosyalar');

// URL paths
define('BASE_URL', '/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('PORTAL_URL', BASE_URL . 'portal/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_URL', BASE_URL . 'portal/dosyalar/');

// Upload klasörleri
define('FATURALAR_PATH', UPLOADS_PATH . '/faturalar');
define('KAYITLAR_PATH', UPLOADS_PATH . '/kayitlar');
define('SERTIFIKALAR_PATH', UPLOADS_PATH . '/sertifikalar');

define('FATURALAR_URL', UPLOADS_URL . 'faturalar/');
define('KAYITLAR_URL', UPLOADS_URL . 'kayitlar/');
define('SERTIFIKALAR_URL', UPLOADS_URL . 'sertifikalar/');

/**
 * Settings tablosundan ayarları çek ve cache'le
 */
function getSettings() {
    static $settings = null;

    if ($settings === null) {
        $settings = [];
        $rows = fetchAll("SELECT setting_key, setting_value FROM settings");
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $settings;
}

/**
 * Tek bir setting değeri al
 */
function getSetting($key, $default = '') {
    $settings = getSettings();
    return $settings[$key] ?? $default;
}

/**
 * Setting güncelle
 */
function updateSetting($key, $value) {
    query("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?", [$value, $key]);
    // Cache'i temizle
    global $settingsCache;
    $settingsCache = null;
}

/**
 * Logo URL'ini al
 */
function getLogoUrl() {
    $logoUrl = getSetting('site_logo_url');
    if ($logoUrl) {
        return $logoUrl;
    }
    // Varsayılan logo yoksa images klasöründen kontrol et
    if (file_exists(ASSETS_PATH . '/images/logo.png')) {
        return ASSETS_URL . 'images/logo.png';
    }
    return '';
}

/**
 * Favicon URL'ini al
 */
function getFaviconUrl() {
    $faviconUrl = getSetting('site_favicon_url');
    if ($faviconUrl) {
        return $faviconUrl;
    }
    // Varsayılan favicon yoksa images klasöründen kontrol et
    if (file_exists(ASSETS_PATH . '/images/favicon.png')) {
        return ASSETS_URL . 'images/favicon.png';
    }
    return '';
}

/**
 * Site adını al
 */
function getSiteName() {
    return getSetting('site_name', 'Eğitim Portalı');
}

/**
 * Güvenli dosya yükleme fonksiyonu
 */
function uploadFile($file, $destination, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Geçersiz dosya'];
    }

    // Dosya yükleme hatalarını kontrol et
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Dosya yükleme hatası'];
    }

    // Dosya boyutu kontrolü (10MB max)
    if ($file['size'] > 10 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Dosya boyutu çok büyük (max 10MB)'];
    }

    // Dosya uzantısı kontrolü
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'message' => 'Dosya tipi desteklenmiyor'];
    }

    // Benzersiz dosya adı oluştur
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $destination . '/' . $filename;

    // Dosyayı taşı
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Dosya kaydedilemedi'];
    }

    return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
}

/**
 * Admin oturum kontrolü
 */
function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . ADMIN_URL . 'giris.php');
        exit;
    }
}

/**
 * Portal kullanıcı oturum kontrolü
 */
function checkUserAuth() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Location: ' . PORTAL_URL . 'giris.php');
        exit;
    }
}

/**
 * Giriş yapan admin bilgisini al
 */
function getAdminInfo() {
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }

    return fetchOne("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']]);
}

/**
 * Giriş yapan kullanıcı bilgisini al
 */
function getUserInfo() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Şifre hashleme
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Şifre doğrulama
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Türkçe tarih formatı
 */
function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

/**
 * Türkçe tarih saat formatı
 */
function formatDateTime($datetime, $format = 'd.m.Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Para formatı
 */
function formatMoney($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Sayfa başlığı ayarla
 */
function setPageTitle($title) {
    return $title . ' - ' . getSiteName();
}

/**
 * XSS koruması
 */
function clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF Token oluştur
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Token doğrula
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
