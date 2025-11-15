<?php
require_once 'includes/auth-check.php';

$page_title = 'Eğitimlerim';
$page_css = 'assets/css/egitimler.css';

// Kullanıcının eğitimlerini çek
$userId = $_SESSION['user_id'];
$userCourses = fetchAll("
    SELECT
        uc.id as enrollment_id,
        uc.course_id,
        uc.enrollment_date,
        uc.video_watched,
        uc.test_completed,
        uc.completed_at,
        uc.status,
        c.title,
        c.duration_hours
    FROM user_courses uc
    JOIN courses c ON uc.course_id = c.id
    WHERE uc.user_id = ?
    ORDER BY uc.enrollment_date DESC
", [$userId]);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1>
        <i class="fas fa-graduation-cap"></i>
        Eğitimlerim
    </h1>
    <p>Satın aldığınız eğitimleri görüntüleyin ve devam edin</p>
</div>

<!-- Education Cards -->
<div class="education-container">
    <?php if (empty($userCourses)): ?>
        <div class="empty-state">
            <i class="fas fa-graduation-cap" style="font-size: 4rem; color: #8b9cbc; margin-bottom: 1rem;"></i>
            <h3>Henüz eğitiminiz bulunmuyor</h3>
            <p>Eğitim satın almak için lütfen bizimle iletişime geçin.</p>
        </div>
    <?php else: ?>
        <?php foreach ($userCourses as $course):
            // İlerleme hesaplama
            $progress = 0;
            if ($course['video_watched']) $progress += 50;
            if ($course['test_completed']) $progress += 50;

            // Durum belirleme
            if ($course['completed_at']) {
                $statusClass = 'completed';
                $statusText = '<i class="fas fa-check-circle"></i> Tamamlandı';
            } elseif ($course['video_watched']) {
                $statusClass = 'in-progress';
                $statusText = '<i class="fas fa-spinner fa-pulse"></i> Devam Ediyor';
            } else {
                $statusClass = 'not-started';
                $statusText = '<i class="fas fa-clock"></i> Başlanmadı';
            }
        ?>
        <div class="education-card <?php echo $statusClass; ?>">
            <div class="education-header">
                <div class="education-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="education-status <?php echo $statusClass; ?>">
                    <?php echo $statusText; ?>
                </span>
            </div>

            <div class="education-body">
                <h3><?php echo htmlspecialchars($course['title']); ?></h3>

                <div class="education-info">
                    <div class="info-row">
                        <span class="info-label">Kayıt Tarihi:</span>
                        <span class="info-value"><?php echo date('d.m.Y', strtotime($course['enrollment_date'])); ?></span>
                    </div>
                    <?php if ($course['completed_at']): ?>
                    <div class="info-row">
                        <span class="info-label">Tamamlanma:</span>
                        <span class="info-value"><?php echo date('d.m.Y', strtotime($course['completed_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="progress-section">
                    <div class="progress-header">
                        <span>İlerleme</span>
                        <span class="progress-percent"><?php echo $progress; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>

                <div class="education-steps">
                    <div class="step <?php echo $course['video_watched'] ? 'completed' : ($progress > 0 ? 'active' : 'pending'); ?>">
                        <i class="fas fa-video"></i>
                        <span><?php echo $course['video_watched'] ? 'Video İzlendi' : 'Video Bekleniyor'; ?></span>
                    </div>
                    <div class="step <?php echo $course['test_completed'] ? 'completed' : ($course['video_watched'] ? 'active' : 'pending'); ?>">
                        <i class="fas fa-clipboard-check"></i>
                        <span><?php echo $course['test_completed'] ? 'Test Tamamlandı' : 'Test Bekliyor'; ?></span>
                    </div>
                </div>
            </div>

            <div class="education-actions">
                <?php if ($course['completed_at']): ?>
                    <a href="egitim-video.php?id=<?php echo $course['course_id']; ?>" class="btn-review">
                        <i class="fas fa-redo"></i> Tekrar İzle
                    </a>
                <?php elseif ($course['video_watched']): ?>
                    <a href="egitim-test.php?id=<?php echo $course['course_id']; ?>" class="btn-continue">
                        <i class="fas fa-clipboard-list"></i> Teste Başla
                    </a>
                <?php else: ?>
                    <a href="egitim-video.php?id=<?php echo $course['course_id']; ?>" class="btn-start">
                        <i class="fas fa-play"></i> Videoyu İzle
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Info Box -->
<div class="info-box">
    <div class="info-icon">
        <i class="fas fa-info-circle"></i>
    </div>
    <div class="info-content">
        <h3>Eğitim Süreci</h3>
        <ul>
            <li>Önce eğitim videosunu izleyin, video bitince otomatik olarak teste yönlendirileceksiniz.</li>
            <li>Testi tamamladıktan sonra sertifikanız hazırlanacak ve bildirilecektir.</li>
            <li>Eğitimlerinizi istediğiniz zaman tekrar izleyebilirsiniz.</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>