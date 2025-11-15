<?php
require_once 'includes/auth-check.php';

$page_title = 'Eğitim Satın Al';
$page_css = 'assets/css/kayit.css';

$userId = $_SESSION['user_id'];

// Kullanıcının almadığı eğitimleri çek
$egitimler = fetchAll("
    SELECT
        c.id,
        c.title as baslik,
        c.price as fiyat,
        c.discount_price as eski_fiyat,
        c.duration_hours
    FROM courses c
    WHERE c.id NOT IN (
        SELECT course_id FROM user_courses WHERE user_id = ?
    )
    ORDER BY c.created_at DESC
", [$userId]);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1>
        <i class="fas fa-shopping-cart"></i>
        Satın Almadıklarınız
    </h1>
</div>

<!-- Education Grid -->
<div class="education-grid">
    <?php foreach($egitimler as $egitim): ?>
    <div class="education-card">
        <div class="card-header">
            <div class="education-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3><?php echo $egitim['baslik']; ?></h3>
        </div>
        
        <div class="education-features">
            <div class="feature">
                <i class="fas fa-university"></i>
                <span>Üniversite Onaylı</span>
            </div>
            <div class="feature">
                <i class="fas fa-shield-alt"></i>
                <span>E-Devlet'te Görünür</span>
            </div>
        </div>
        
        <div class="card-footer">
            <div class="price-section">
                <?php if($egitim['eski_fiyat']): ?>
                <span class="discount-badge-mobile">
                    %<?php echo round((1 - $egitim['fiyat'] / $egitim['eski_fiyat']) * 100); ?> indirim
                </span>
                <span class="old-price"><?php echo number_format($egitim['eski_fiyat'], 2); ?> ₺</span>
                <?php endif; ?>
                <span class="price"><?php echo number_format($egitim['fiyat'], 2); ?> ₺</span>
            </div>
            
            <div class="btn-buy-wrapper">
                <?php if($egitim['eski_fiyat']): ?>
                <div class="discount-badge">
                    %<?php echo round((1 - $egitim['fiyat'] / $egitim['eski_fiyat']) * 100); ?> indirim
                </div>
                <?php endif; ?>
                <a href="odeme-islem.php?egitim_id=<?php echo $egitim['id']; ?>&egitim_adi=<?php echo urlencode($egitim['baslik']); ?>&fiyat=<?php echo $egitim['fiyat']; ?>" class="btn-buy">
                    <i class="fas fa-shopping-cart"></i>
                    Satın Al
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Info Box -->
<div class="info-box">
    <div class="info-icon">
        <i class="fas fa-info-circle"></i>
    </div>
    <div class="info-content">
        <h3>Satın Alma Bilgileri</h3>
        <ul>
            <li>Tüm eğitimler <strong>Üniversite Onaylıdır</strong> ve <strong>E-Devlet üzerinde görünür</strong>.</li>
            <li>Ödeme sonrası eğitime <strong>anında erişim</strong> sağlanır.</li>
            <li>İşlem güvenliğiniz için <strong>güvenli ödeme</strong> altyapısı kullanılmaktadır.</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>