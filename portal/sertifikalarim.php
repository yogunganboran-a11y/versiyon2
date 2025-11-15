<?php
require_once 'includes/auth-check.php';

$page_title = 'Sertifikalarım';
$page_css = 'assets/css/sertifikalarim.css';

// Kullanıcının sertifikalarını çek
$userId = $_SESSION['user_id'];
$certificates = fetchAll("
    SELECT
        cert.*,
        c.title as course_title,
        uc.enrollment_date
    FROM certificates cert
    JOIN courses c ON cert.course_id = c.id
    JOIN user_courses uc ON uc.user_id = cert.user_id AND uc.course_id = cert.course_id
    WHERE cert.user_id = ?
    ORDER BY cert.issue_date DESC
", [$userId]);

// İşlemde olan eğitimleri çek (video ve test tamamlanmış ama henüz sertifika verilmemiş)
$pendingCertificates = fetchAll("
    SELECT
        uc.*,
        c.title as course_title,
        c.certificate_days,
        c.certificate_time
    FROM user_courses uc
    JOIN courses c ON uc.course_id = c.id
    LEFT JOIN certificates cert ON cert.user_id = uc.user_id AND cert.course_id = uc.course_id
    WHERE uc.user_id = ?
    AND uc.test_completed = 1
    AND cert.id IS NULL
    ORDER BY uc.completed_at DESC
", [$userId]);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1>
        <i class="fas fa-file-alt"></i>
        Sertifikalarım
    </h1>
    <p>Tamamladığınız eğitimlerin sertifikalarını görüntüleyin ve indirin</p>
</div>

<!-- Certificates Grid -->
<div class="certificates-container">
    <?php if (empty($certificates) && empty($pendingCertificates)): ?>
        <div class="empty-state">
            <i class="fas fa-award" style="font-size: 4rem; color: #8b9cbc; margin-bottom: 1rem;"></i>
            <h3>Henüz sertifikanız bulunmuyor</h3>
            <p>Eğitimleri tamamladığınızda sertifikalarınız burada görünecektir.</p>
        </div>
    <?php else: ?>
        <?php foreach ($certificates as $cert): ?>
        <!-- Hazır Sertifika -->
        <div class="certificate-card ready">
            <div class="certificate-header">
                <div class="certificate-icon">
                    <i class="fas fa-award"></i>
                </div>
                <span class="certificate-status ready">
                    <i class="fas fa-check-circle"></i> Hazır
                </span>
            </div>

            <div class="certificate-body">
                <h3>Sertifika Bilgileriniz</h3>
                <div class="certificate-info">
                    <div class="info-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span><?php echo htmlspecialchars($cert['course_title']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>Kayıt Tarihi: <?php echo date('d.m.Y', strtotime($cert['enrollment_date'])); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-certificate"></i>
                        <span>Düzenleme: <?php echo date('d.m.Y', strtotime($cert['issue_date'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="certificate-actions">
                <button class="btn-view" onclick="window.open('sertifika-goster.php?id=<?php echo $cert['id']; ?>', '_blank')">
                    <i class="fas fa-eye"></i> Görüntüle
                </button>
                <button class="btn-download" onclick="window.open('sertifika-indir.php?id=<?php echo $cert['id']; ?>', '_blank')">
                    <i class="fas fa-download"></i> İndir
                </button>
            </div>
        </div>
        <?php endforeach; ?>

        <?php foreach ($pendingCertificates as $pending):
            // Tahmini hazır olma tarihi hesapla
            $completedDate = new DateTime($pending['completed_at']);
            $releaseDate = clone $completedDate;
            $releaseDate->modify('+' . ($pending['certificate_days'] ?? 7) . ' days');
            $releaseTime = $pending['certificate_time'] ?? '14:00';
        ?>
        <!-- İşlemde Sertifika -->
        <div class="certificate-card processing">
            <div class="certificate-header">
                <div class="certificate-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="certificate-status processing">
                    <i class="fas fa-spinner fa-pulse"></i> İşlemde
                </span>
            </div>

            <div class="certificate-body">
                <h3>Sertifika Bilgileriniz</h3>
                <div class="certificate-info">
                    <div class="info-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span><?php echo htmlspecialchars($pending['course_title']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>Kayıt Tarihi: <?php echo date('d.m.Y', strtotime($pending['enrollment_date'])); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-info-circle"></i>
                        <span>Tahmini Hazır Olma: <?php echo $releaseDate->format('d.m.Y') . ' - ' . $releaseTime; ?></span>
                    </div>
                </div>

                <div class="processing-message">
                    <i class="fas fa-info-circle"></i>
                    <p>Sertifikanız hazırlanıyor. Yaklaşık <strong><?php echo $releaseDate->format('d.m.Y') . ' - ' . $releaseTime; ?></strong> tarihinde bu alanda görünecektir.</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Info Box -->
<div class="info-box">
    <div class="info-icon">
        <i class="fas fa-lightbulb"></i>
    </div>
    <div class="info-content">
        <h3>Bilgilendirme</h3>
        <ul>
            <li>Hazır olan sertifikaları PDF formatında görüntüleyebilir ve indirebilirsiniz.</li>
            <li>Sertifikalarınız hakkında sorularınız için iletişime geçebilirsiniz.</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>