<?php
/**
 * Veritabanı Bağlantı Dosyası
 * PDO ile MySQL bağlantısı
 */

// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'egitim_portali');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// PDO Bağlantısı
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $db = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Hata durumunda güvenli mesaj göster (production'da detay gösterme)
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        die("Veritabanı Bağlantı Hatası: " . $e->getMessage());
    } else {
        die("Veritabanı bağlantısı kurulamadı. Lütfen sistem yöneticisi ile iletişime geçin.");
    }
}

/**
 * Veritabanı sorgu fonksiyonu (prepared statement ile)
 * @param string $query SQL sorgusu
 * @param array $params Parametreler
 * @return PDOStatement
 */
function query($query, $params = []) {
    global $db;
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
            die("Sorgu Hatası: " . $e->getMessage());
        } else {
            die("Bir hata oluştu. Lütfen tekrar deneyin.");
        }
    }
}

/**
 * Tek satır veri çek
 */
function fetchOne($query, $params = []) {
    $stmt = query($query, $params);
    return $stmt->fetch();
}

/**
 * Çoklu satır veri çek
 */
function fetchAll($query, $params = []) {
    $stmt = query($query, $params);
    return $stmt->fetchAll();
}

/**
 * Insert işlemi yap ve son eklenen ID'yi döndür
 */
function insert($query, $params = []) {
    global $db;
    query($query, $params);
    return $db->lastInsertId();
}

/**
 * Güvenli string escape
 */
function escape($value) {
    global $db;
    return $db->quote($value);
}
?>
