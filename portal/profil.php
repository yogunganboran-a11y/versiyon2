<?php
require_once 'includes/auth-check.php';

$page_title = 'Profilim';
$page_css = 'assets/css/profil.css';

// Kullanıcının eğitimlerini çek
$userId = $user['id'];
$userCourses = fetchAll("SELECT uc.*, c.title, c.duration_hours, uc.completed_at
    FROM user_courses uc
    JOIN courses c ON uc.course_id = c.id
    WHERE uc.user_id = ?
    ORDER BY uc.enrollment_date DESC", [$userId]);

// Kullanıcının sertifikalarını çek
$userCertificates = fetchAll("SELECT cert.*, c.title as course_title
    FROM certificates cert
    JOIN courses c ON cert.course_id = c.id
    WHERE cert.user_id = ?
    ORDER BY cert.issue_date DESC", [$userId]);

// Kullanıcının ödemelerini çek
$userPayments = fetchAll("SELECT p.*, c.title as course_title
    FROM payments p
    JOIN courses c ON p.course_id = c.id
    WHERE p.user_id = ? AND p.status = 'completed'
    ORDER BY p.created_at DESC", [$userId]);

include 'includes/header.php';
?>

<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-avatar">
        <i class="fas fa-user-circle"></i>
    </div>
    <div class="profile-info">
        <h1><?php echo htmlspecialchars(($user['name'] ?? '') . ' ' . ($user['surname'] ?? '')); ?></h1>
        <p class="profile-subtitle">Öğrenci</p>
        <div class="profile-badges">
            <span class="badge-item">
                <i class="fas fa-graduation-cap"></i> <?php echo count($userCourses); ?> Eğitim
            </span>
            <span class="badge-item">
                <i class="fas fa-file-alt"></i> <?php echo count($userCertificates); ?> Sertifika
            </span>
        </div>
    </div>
</div>

<!-- Personal Info Section -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-user-circle"></i>
            Kişisel Bilgiler
        </h2>
    </div>
    
    <div class="info-grid">
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="info-content">
                <span class="info-label">Ad Soyad</span>
                <span class="info-value"><?php echo htmlspecialchars(($user['name'] ?? '') . ' ' . ($user['surname'] ?? '')); ?></span>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="info-content">
                <span class="info-label">TC Kimlik No</span>
                <span class="info-value"><?php echo htmlspecialchars($user['tckn'] ?? '-'); ?></span>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div class="info-content">
                <span class="info-label">Telefon</span>
                <span class="info-value"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></span>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="info-content">
                <span class="info-label">Doğum Tarihi</span>
                <span class="info-value"><?php echo $user['birth_date'] ? date('d.m.Y', strtotime($user['birth_date'])) : '-'; ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Education History -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-graduation-cap"></i>
            Aldığım Eğitimler
        </h2>
    </div>
    
    <div class="education-list">
        <?php if (empty($userCourses)): ?>
            <div style="text-align: center; padding: 2rem; color: #8b9cbc;">
                <p>Henüz eğitim kaydınız bulunmuyor</p>
            </div>
        <?php else: ?>
            <?php foreach ($userCourses as $course):
                $statusClass = $course['completed_at'] ? 'completed' : 'in-progress';
                $statusIcon = $course['completed_at'] ? 'fa-check-circle' : 'fa-spinner fa-pulse';
                $statusText = $course['completed_at'] ? 'Tamamlandı' : 'Devam Ediyor';
            ?>
            <div class="education-item">
                <div class="education-item-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="education-item-content">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="education-date">
                        <i class="fas fa-calendar"></i> Başlangıç: <?php echo date('d.m.Y', strtotime($course['enrollment_date'])); ?>
                    </p>
                </div>
                <div class="education-item-status <?php echo $statusClass; ?>">
                    <i class="fas <?php echo $statusIcon; ?>"></i>
                    <span><?php echo $statusText; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Certificates -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-file-alt"></i>
            Sertifikalarım
        </h2>
    </div>
    
    <div class="certificates-grid">
        <?php if (empty($userCertificates)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #8b9cbc;">
                <p>Henüz sertifikanız bulunmuyor</p>
            </div>
        <?php else: ?>
            <?php foreach ($userCertificates as $cert): ?>
            <div class="certificate-card">
                <div class="certificate-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="certificate-content">
                    <h3><?php echo htmlspecialchars($cert['course_title']); ?> Sertifikası</h3>
                    <p class="certificate-date">
                        <i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($cert['issue_date'])); ?>
                    </p>
                </div>
                <div class="certificate-actions">
                    <button class="btn-download" onclick="window.open('sertifika-indir.php?id=<?php echo $cert['id']; ?>', '_blank')">
                        <i class="fas fa-download"></i> İndir
                    </button>
                    <button class="btn-view" onclick="window.open('sertifika-goster.php?id=<?php echo $cert['id']; ?>', '_blank')">
                        <i class="fas fa-eye"></i> Görüntüle
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Payment History -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-credit-card"></i>
            Ödeme Geçmişi
        </h2>
    </div>
    
    <div class="table-responsive">
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Eğitim</th>
                    <th>Tutar</th>
                    <th>Fatura</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($userPayments)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: #8b9cbc;">
                        Henüz ödeme kaydınız bulunmuyor
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($userPayments as $payment): ?>
                    <tr>
                        <td><?php echo date('d.m.Y', strtotime($payment['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['course_title']); ?></td>
                        <td>₺<?php echo number_format($payment['amount'], 2, ',', '.'); ?></td>
                        <td>
                            <button class="btn-invoice" onclick="window.open('fatura-goster.php?payment_id=<?php echo $payment['id']; ?>', '_blank')">
                                <i class="fas fa-file-invoice"></i> İndir
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Registration Documents -->
<div class="section">
    <div class="section-header">
        <h2>
            <i class="fas fa-file-alt"></i>
            Kayıt Belgeleri
        </h2>
    </div>
    
    <div class="documents-list">
        <?php if (empty($userCourses)): ?>
            <div style="text-align: center; padding: 2rem; color: #8b9cbc;">
                <p>Henüz belgeniz bulunmuyor</p>
            </div>
        <?php else: ?>
            <?php foreach ($userCourses as $course): ?>
            <div class="document-card">
                <div class="document-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="document-content">
                    <h3>Kayıt Formu</h3>
                    <p class="document-info">
                        <span><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($course['enrollment_date'])); ?></span>
                        <span class="document-education"><?php echo htmlspecialchars($course['title']); ?></span>
                    </p>
                </div>
                <button class="btn-download-doc" onclick="window.open('belge-indir.php?course_id=<?php echo $course['course_id']; ?>', '_blank')">
                    <i class="fas fa-download"></i> İndir
                </button>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>