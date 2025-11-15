<?php
require_once 'includes/auth-check.php';

$page_title = 'Dashboard';
$page_css = 'assets/css/dashboard.css';
$page_js = 'assets/js/dashboard.js';

// Kullanıcı istatistiklerini çek
$userId = $user['id'];
$courseCount = fetchOne("SELECT COUNT(*) as count FROM user_courses WHERE user_id = ?", [$userId])['count'] ?? 0;
$certificateCount = fetchOne("SELECT COUNT(*) as count FROM certificates WHERE user_id = ?", [$userId])['count'] ?? 0;

// Devam eden eğitimleri çek
$inProgressCourses = fetchAll("SELECT uc.*, c.title, c.duration_hours
    FROM user_courses uc
    JOIN courses c ON uc.course_id = c.id
    WHERE uc.user_id = ? AND uc.status = 'active' AND uc.completed_at IS NULL
    ORDER BY uc.enrollment_date DESC
    LIMIT 5", [$userId]);

// Son bildirimleri çek
$recentNotifications = fetchAll("SELECT id, message, status, created_at
    FROM sms_history
    WHERE phone = (SELECT phone FROM users WHERE id = ?)
    ORDER BY created_at DESC
    LIMIT 3", [$userId]);

// Bildirim tipini belirle
function getNotificationTypeIndex($message) {
    $lowerMsg = mb_strtolower($message, 'UTF-8');
    if (strpos($lowerMsg, 'başarı') !== false || strpos($lowerMsg, 'tamamland') !== false || strpos($lowerMsg, 'başarıyla') !== false) {
        return ['icon' => 'success', 'fa' => 'fa-check-circle', 'title' => 'İşlem Başarılı'];
    } elseif (strpos($lowerMsg, 'video') !== false || strpos($lowerMsg, 'yeni') !== false) {
        return ['icon' => 'info', 'fa' => 'fa-video', 'title' => 'Yeni İçerik'];
    } elseif (strpos($lowerMsg, 'sertifika') !== false || strpos($lowerMsg, 'hazır') !== false) {
        return ['icon' => 'warning', 'fa' => 'fa-file-alt', 'title' => 'Sertifika Hazır'];
    }
    return ['icon' => 'info', 'fa' => 'fa-info-circle', 'title' => 'Bildirim'];
}

function timeAgoIndex($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d == 0 && $diff->h <= 23) {
        if ($diff->h == 0) return $diff->i . ' dakika önce';
        return $diff->h . ' saat önce';
    }
    if ($diff->d == 1) return '1 gün önce';
    return $diff->d . ' gün önce';
}

include 'includes/header.php';
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <h1>Hoş Geldin, <?php echo isset($user['name']) ? $user['name'] . ' ' . ($user['surname'] ?? '') : 'Kullanıcı'; ?>! 👋</h1>
    <p>Eğitimlerinize kaldığınız yerden devam edebilirsiniz.</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- Sertifikalarım -->
    <div class="stat-card">
        <div class="stat-icon certificates">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <h3>Sertifikalarım</h3>
            <a href="sertifikalarim.php" class="stat-link">
                Görüntüle (<?php echo $certificateCount; ?>) <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Eğitimlerim -->
    <div class="stat-card">
        <div class="stat-icon education">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="stat-content">
            <h3>Eğitimlerim</h3>
            <a href="egitimler.php" class="stat-link">
                Görüntüle (<?php echo $courseCount; ?>) <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <!-- Profilim -->
    <div class="stat-card">
        <div class="stat-icon profile">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="stat-content">
            <h3>Profilim</h3>
            <a href="profil.php" class="stat-link">
                Görüntüle <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Devam Eden Eğitimler -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-play-circle"></i>
            Eğitimlerim
        </h2>
        <a href="egitimler.php" class="btn-view-all">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="education-grid">
        <?php if (empty($inProgressCourses)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #8b9cbc;">
                <i class="fas fa-graduation-cap" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                <p>Henüz devam eden eğitiminiz bulunmuyor</p>
            </div>
        <?php else: ?>
            <?php foreach ($inProgressCourses as $course):
                $progress = 0;
                if ($course['video_watched']) $progress += 50;
                if ($course['test_completed']) $progress += 50;

                $nextAction = !$course['video_watched'] ?
                    ['url' => 'egitim-video.php?id=' . $course['course_id'], 'text' => 'Videoyu İzle', 'icon' => 'fa-play'] :
                    ['url' => 'egitim-test.php?id=' . $course['course_id'], 'text' => 'Teste Geç', 'icon' => 'fa-arrow-right'];
            ?>
            <div class="education-card">
                <div class="education-header">
                    <div class="education-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="education-badge ongoing">
                        <i class="fas fa-circle"></i> Devam Ediyor
                    </div>
                </div>

                <h3 class="education-title"><?php echo htmlspecialchars($course['title']); ?></h3>

                <div class="progress-section">
                    <div class="progress-info">
                        <span>İlerleme</span>
                        <span class="progress-percentage"><?php echo $progress; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>

                <div class="education-status-list">
                    <div class="status-item <?php echo $course['video_watched'] ? 'completed' : 'pending'; ?>">
                        <div class="status-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="status-text">
                            <span class="status-label">Video</span>
                            <span class="status-value"><?php echo $course['video_watched'] ? 'İzlendi' : 'Bekliyor'; ?></span>
                        </div>
                        <i class="fas <?php echo $course['video_watched'] ? 'fa-check-circle' : 'fa-clock'; ?> status-check"></i>
                    </div>

                    <div class="status-item <?php echo $course['test_completed'] ? 'completed' : ($course['video_watched'] ? 'pending' : 'locked'); ?>">
                        <div class="status-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="status-text">
                            <span class="status-label">Test</span>
                            <span class="status-value"><?php echo $course['test_completed'] ? 'Tamamlandı' : ($course['video_watched'] ? 'Bekliyor' : 'Kilitli'); ?></span>
                        </div>
                        <i class="fas <?php echo $course['test_completed'] ? 'fa-check-circle' : ($course['video_watched'] ? 'fa-clock' : 'fa-lock'); ?> status-check"></i>
                    </div>

                    <div class="status-item <?php echo $course['test_completed'] ? 'pending' : 'locked'; ?>">
                        <div class="status-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="status-text">
                            <span class="status-label">Sertifika</span>
                            <span class="status-value"><?php echo $course['test_completed'] ? 'Beklemede' : 'Kilitli'; ?></span>
                        </div>
                        <i class="fas fa-clock status-check"></i>
                    </div>
                </div>

                <a href="<?php echo $nextAction['url']; ?>" class="btn-education">
                    <i class="fas <?php echo $nextAction['icon']; ?>"></i> <?php echo $nextAction['text']; ?>
                </a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Son Bildirimler -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-bell"></i>
            Son Bildirimler
        </h2>
        <a href="bildirimler.php" class="btn-view-all">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="notifications-container">
        <?php if (empty($recentNotifications)): ?>
            <div style="text-align: center; padding: 2rem; color: #8b9cbc;">
                <i class="fas fa-bell-slash" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                <p>Henüz bildiriminiz bulunmuyor</p>
            </div>
        <?php else: ?>
            <?php foreach ($recentNotifications as $notif):
                $notifType = getNotificationTypeIndex($notif['message']);
            ?>
            <div class="notification-card">
                <div class="notification-icon-wrapper <?php echo $notifType['icon']; ?>">
                    <i class="fas <?php echo $notifType['fa']; ?>"></i>
                </div>
                <div class="notification-body">
                    <h4><?php echo htmlspecialchars($notifType['title']); ?></h4>
                    <p><?php echo htmlspecialchars($notif['message']); ?></p>
                    <span class="notification-time">
                        <i class="fas fa-clock"></i> <?php echo timeAgoIndex($notif['created_at']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Yeni Eğitim Al -->
<div class="cta-banner">
    <div class="cta-content">
        <div class="cta-icon">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="cta-text">
            <h3>Yeni Eğitim Almak İster misiniz?</h3>
            <p>Kariyer hedeflerinize uygun eğitimleri keşfedin ve hemen başlayın.</p>
        </div>
    </div>
    <a href="kayit.php" class="btn-cta">
        <i class="fas fa-shopping-cart"></i> Eğitimleri İncele
    </a>
</div>

<?php include 'includes/footer.php'; ?>
