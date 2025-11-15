<aside class="sidebar" id="sidebar">
    <!-- Close Button (Mobil) -->
    <button class="sidebar-close" onclick="toggleSidebar()">
        <i class="fas fa-times"></i>
    </button>
    
    <!-- Logo -->
    <div class="sidebar-logo">
        <?php
        $logoUrl = getLogoUrl();
        $siteName = getSiteName();
        if ($logoUrl):
        ?>
            <img src="<?php echo $logoUrl; ?>" alt="<?php echo $siteName; ?>">
        <?php else: ?>
            <div style="color: #3b82f6; font-size: 2rem;"><i class="fas fa-graduation-cap"></i></div>
        <?php endif; ?>
        <!-- Notification Icon (Desktop only) -->
        <div class="sidebar-notification-icon" id="sidebarNotificationIcon">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Ana Sayfa</span>
        </a>
        
        <a href="egitimler.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'egitimler.php' ? 'active' : ''; ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Eğitimlerim</span>
        </a>
        
        <a href="sertifikalarim.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'sertifikalarim.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i>
            <span>Sertifikalarım</span>
        </a>
        
        <a href="kayit.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'kayit.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Yeni Eğitim Al</span>
        </a>
        
        <a href="profil.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i>
            <span>Profilim</span>
        </a>
    </nav>
    
    <!-- User Info (Mobil için) -->
    <div class="sidebar-user">
        <div class="user-info-horizontal">
            <div class="user-avatar-small">
                <?php if ($user['avatar']): ?>
                    <img src="<?php echo $user['avatar']; ?>" alt="Profil">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <div class="user-details-compact">
                <p class="user-name"><?php echo isset($user['name']) ? $user['name'] . ' ' . ($user['surname'] ?? '') : 'Kullanıcı'; ?></p>
                <p class="user-phone"><?php echo $user['phone'] ?? ''; ?></p>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Çıkış Yap</span>
        </a>
    </div>
</aside>
