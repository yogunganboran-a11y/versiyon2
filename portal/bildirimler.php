<?php
require_once 'includes/auth-check.php';

$page_title = 'Bildirimler';
$page_css = 'assets/css/bildirimler.css';

$userId = $_SESSION['user_id'];

// SMS geçmişinden bildirimleri çek
$notifications = fetchAll("
    SELECT
        id,
        phone,
        message,
        status,
        created_at
    FROM sms_history
    WHERE phone = (SELECT phone FROM users WHERE id = ?)
    ORDER BY created_at DESC
    LIMIT 50
", [$userId]);

// Bildirim tipini belirle
function getNotificationType($message) {
    $lowerMsg = mb_strtolower($message, 'UTF-8');
    if (strpos($lowerMsg, 'başarı') !== false || strpos($lowerMsg, 'onay') !== false || strpos($lowerMsg, 'tamamland') !== false) {
        return ['type' => 'success', 'icon' => 'fa-check-circle'];
    } elseif (strpos($lowerMsg, 'uyarı') !== false || strpos($lowerMsg, 'dikkat') !== false || strpos($lowerMsg, 'hatırlat') !== false) {
        return ['type' => 'warning', 'icon' => 'fa-exclamation-triangle'];
    } elseif (strpos($lowerMsg, 'hata') !== false || strpos($lowerMsg, 'iptal') !== false || strpos($lowerMsg, 'başarısız') !== false) {
        return ['type' => 'error', 'icon' => 'fa-times-circle'];
    } else {
        return ['type' => 'info', 'icon' => 'fa-info-circle'];
    }
}

// Zaman farkı hesapla
function timeAgoNotif($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' yıl önce';
    if ($diff->m > 0) return $diff->m . ' ay önce';
    if ($diff->d > 0) {
        if ($diff->d == 1) return 'Dün';
        if ($diff->d < 7) return $diff->d . ' gün önce';
        return floor($diff->d / 7) . ' hafta önce';
    }
    if ($diff->h > 0) return $diff->h . ' saat önce';
    if ($diff->i > 0) return $diff->i . ' dakika önce';
    return 'Az önce';
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/bildirimler.css">

<div class="page-container">
    <!-- Sayfa Başlığı -->
    <div class="page-header">
        <div class="page-title">
            <h1>
                <i class="fas fa-bell"></i>
                Bildirimler
            </h1>
            <p class="page-subtitle">Tüm bildirimlerinizi bu sayfadan görüntüleyebilirsiniz</p>
        </div>
    </div>

    <!-- Bildirim Listesi -->
    <div class="notifications-list" id="notificationsList" style="<?php echo empty($notifications) ? 'display: none;' : ''; ?>">
        <?php foreach ($notifications as $notif):
            $notifType = getNotificationType($notif['message']);
            $statusClass = $notif['status'] == 'sent' ? 'success' : ($notif['status'] == 'failed' ? 'error' : 'info');
        ?>
        <div class="notification-card"
             data-id="<?php echo $notif['id']; ?>"
             data-type="<?php echo $notifType['type']; ?>">

            <div class="notification-indicator"></div>

            <div class="notification-icon-wrapper">
                <div class="notification-icon <?php echo $notifType['type']; ?>">
                    <i class="fas <?php echo $notifType['icon']; ?>"></i>
                </div>
            </div>

            <div class="notification-body">
                <div class="notification-text">
                    <strong><?php echo htmlspecialchars($notif['message']); ?></strong>
                    <p><?php echo htmlspecialchars($notif['phone']); ?> numarasına gönderildi</p>
                </div>
                <div class="notification-time">
                    <i class="far fa-clock"></i>
                    <?php echo timeAgoNotif($notif['created_at']); ?>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <!-- Boş Durum -->
    <div class="empty-state" id="emptyState" style="<?php echo empty($notifications) ? '' : 'display: none;'; ?>">
        <div class="empty-icon">
            <i class="fas fa-bell-slash"></i>
        </div>
        <h3>Bildirim Bulunamadı</h3>
        <p>Seçili filtreye uygun bildirim bulunmuyor.</p>
    </div>

    <!-- Pagination -->
    <div class="pagination-container" id="paginationContainer">
        <button class="pagination-btn" id="loadMoreBtn" onclick="loadMore()">
            <i class="fas fa-chevron-down"></i>
            Daha Fazla Yükle
        </button>
    </div>
</div>

<script src="assets/js/bildirimler.js"></script>

<?php include 'includes/footer.php'; ?>
