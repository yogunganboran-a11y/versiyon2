<?php
require_once 'entegrasyon/config.php';
checkAdminAuth();

$page_title = 'WhatsApp';

// WhatsApp konuşmalarını veritabanından çek
$chats = fetchAll("
    SELECT
        wc.*,
        u.name,
        u.surname,
        u.phone,
        (SELECT message FROM whatsapp_messages WHERE conversation_id = wc.id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM whatsapp_messages WHERE conversation_id = wc.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
        (SELECT COUNT(*) FROM whatsapp_messages WHERE conversation_id = wc.id AND is_read = 0 AND direction = 'incoming') as unread
    FROM whatsapp_conversations wc
    LEFT JOIN users u ON wc.user_id = u.id
    ORDER BY wc.last_activity DESC
");

// Avatar oluşturma fonksiyonu
function getAvatar($name, $surname) {
    $first = $name ? mb_substr($name, 0, 1, 'UTF-8') : '';
    $last = $surname ? mb_substr($surname, 0, 1, 'UTF-8') : '';
    return strtoupper($first . $last);
}

// Zaman farkını hesapla
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d == 0) return 'Bugün';
    if ($diff->d == 1) return 'Dün';
    if ($diff->d < 7) return $diff->d . ' gün önce';
    if ($diff->d < 30) return floor($diff->d / 7) . ' hafta önce';
    return $ago->format('d.m.Y');
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/whatsapp.css">

<style>
/* WhatsApp için özel tam ekran */
body.whatsapp-page {
    overflow: hidden;
}

body.whatsapp-page .admin-wrapper {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

body.whatsapp-page .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    z-index: 1000;
}

body.whatsapp-page .main-content {
    margin-left: 80px;
    width: calc(100% - 80px);
    padding: 0 !important;
    height: 100vh;
    overflow: hidden;
}

body.whatsapp-page .whatsapp-container {
    height: 100vh;
    margin: 0;
    border-radius: 0;
}
</style>

<script>
document.body.classList.add('whatsapp-page');
</script>

<div class="whatsapp-container">
    <!-- Sol Panel - Sohbet Listesi -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <div>
                <h2>
                    <i class="fab fa-whatsapp"></i>
                    Sohbetler <span id="chatCount">(70)</span>
                </h2>
            </div>
            <div class="date-filter">
                <select id="dateFilter" class="date-filter-select" onchange="filterByDate()">
                    <option value="today" selected>Bugün</option>
                    <option value="yesterday">Dün</option>
                    <option value="week">Bu Hafta</option>
                    <option value="month">Bu Ay</option>
                    <option value="all">Tümü</option>
                </select>
            </div>
        </div>
        
        <div class="chat-search">
            <i class="fas fa-search"></i>
            <input type="text" id="chatSearch" placeholder="Sohbet ara..." onkeyup="searchChats()">
        </div>
        
        <div class="chat-list">
            <?php if (empty($chats)): ?>
                <div style="text-align: center; padding: 2rem; color: #8b9cbc;">
                    <i class="fab fa-whatsapp" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p style="margin-top: 1rem;">Henüz WhatsApp konuşması yok</p>
                </div>
            <?php else: ?>
                <?php foreach ($chats as $chat):
                    $fullName = ($chat['name'] && $chat['surname']) ? $chat['name'] . ' ' . $chat['surname'] : $chat['phone'];
                    $avatar = getAvatar($chat['name'] ?? '', $chat['surname'] ?? '');
                    $timeText = $chat['last_message_time'] ? timeAgo($chat['last_message_time']) : '';
                ?>
                <div class="chat-item <?php echo $chat['unread'] > 0 ? 'unread' : ''; ?>"
                     data-chat-id="<?php echo $chat['id']; ?>"
                     data-phone="<?php echo htmlspecialchars($chat['phone']); ?>"
                     onclick="selectChat(<?php echo $chat['id']; ?>)">
                    <div class="chat-avatar"><?php echo $avatar ?: '?'; ?></div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <span class="chat-name"><?php echo htmlspecialchars($fullName); ?></span>
                            <span class="chat-time"><?php echo $timeText; ?></span>
                        </div>
                        <div class="chat-preview">
                            <span class="chat-last-message"><?php echo htmlspecialchars($chat['last_message'] ?? ''); ?></span>
                            <?php if ($chat['unread'] > 0): ?>
                            <span class="chat-unread-badge"><?php echo $chat['unread']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sağ Panel - Sohbet İçeriği -->
    <div class="chat-content">
        <!-- Boş Durum -->
        <div class="chat-empty">
            <i class="fab fa-whatsapp"></i>
            <h3>WhatsApp Web</h3>
            <p>Bir sohbet seçin ve mesajlaşmaya başlayın</p>
        </div>
        
        <!-- Aktif Sohbet Başlığı -->
        <div class="active-chat-header" style="display: none;">
            <button class="mobile-back-btn" onclick="backToChats()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="active-chat-info">
                <div class="chat-avatar">AY</div>
                <div class="active-chat-details">
                    <h3>Ahmet Yılmaz</h3>
                    <div class="active-chat-status">Çevrimiçi</div>
                </div>
            </div>
            
            <div class="bot-control">
                <div class="bot-status">
                    <div class="bot-indicator"></div>
                    <i class="fas fa-robot"></i> Bot Aktif
                </div>
                <button class="bot-toggle" onclick="toggleBot()">Botu Durdur</button>
            </div>
        </div>
        
        <!-- Mesajlar -->
        <div class="messages-container" style="display: none;"></div>
        
        <!-- Mesaj Girişi -->
        <div class="message-input-container" style="display: none;">
            <div class="message-input-wrapper">
                <button class="emoji-btn">
                    <i class="far fa-smile"></i>
                </button>
                <button class="attach-btn" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="fileInput" style="display: none;" accept="image/*,video/*,application/pdf,.doc,.docx" onchange="handleFileUpload(event)">
                <input type="text" id="messageInput" placeholder="Bir mesaj yazın...">
            </div>
            <button class="send-btn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script src="assets/js/whatsapp.js"></script>

<?php include 'includes/footer.php'; ?>