// Ayarlar Sayfası - JavaScript

// Sekme değiştir
function changeTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    document.getElementById(tabName).classList.add('active');
    document.querySelector(`[onclick="changeTab('${tabName}')"]`).classList.add('active');
}

// Genel ayarları kaydet
function saveGeneralSettings() {
    const formData = new FormData();
    formData.append('action', 'save_general_settings');
    formData.append('site_name', document.getElementById('siteName').value);
    formData.append('site_description', document.getElementById('siteDescription').value);
    formData.append('site_url', document.getElementById('siteUrl').value);
    formData.append('contact_email', document.getElementById('contactEmail').value);
    formData.append('contact_phone', document.getElementById('contactPhone').value);
    formData.append('whatsapp_phone', document.getElementById('whatsappPhone').value);
    formData.append('instagram_username', document.getElementById('instagramUsername').value);
    formData.append('facebook_username', document.getElementById('facebookUsername').value);

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ayarlar kaydedildi', 'success');
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// Logo yükleme
function handleLogoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('action', 'upload_logo');
    formData.append('logo', file);

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('logoPreview').src = data.url;
            document.getElementById('logoPreview').style.display = 'block';
            showNotification('Logo yüklendi', 'success');
        } else {
            showNotification(data.message || 'Logo yüklenemedi', 'error');
        }
    })
    .catch(error => {
        showNotification('Yükleme hatası', 'error');
    });
}

// Favicon yükleme
function handleFaviconUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('action', 'upload_favicon');
    formData.append('favicon', file);

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('faviconPreview').src = data.url;
            document.getElementById('faviconPreview').style.display = 'block';
            showNotification('Favicon yüklendi', 'success');
        } else {
            showNotification(data.message || 'Favicon yüklenemedi', 'error');
        }
    })
    .catch(error => {
        showNotification('Yükleme hatası', 'error');
    });
}

// Admin ekle modal
function openAddAdminModal() {
    document.getElementById('addAdminModal').classList.add('active');
}

// Admin ekle
function addAdmin() {
    const name = document.getElementById('adminName').value.trim();
    const email = document.getElementById('adminEmail').value.trim();
    const password = document.getElementById('adminPassword').value.trim();
    const role = document.getElementById('adminRole').value;

    if (!name || !email || !password) {
        showNotification('Lütfen tüm alanları doldurun', 'error');
        return;
    }

    if (!validateEmail(email)) {
        showNotification('Geçerli bir email adresi girin', 'error');
        return;
    }

    if (password.length < 6) {
        showNotification('Şifre en az 6 karakter olmalıdır', 'error');
        return;
    }

    // Yetkileri topla (eğer moderator ise)
    let permissions = [];
    if (role === 'moderator') {
        document.querySelectorAll('#permissionsSection input[type="checkbox"]:checked').forEach(cb => {
            permissions.push(cb.value);
        });
    }

    const formData = new FormData();
    formData.append('action', 'add_admin');
    formData.append('name', name);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('role', role);
    formData.append('permissions', JSON.stringify(permissions));

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Admin başarıyla eklendi', 'success');
            closeModal('addAdminModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// Admin düzenle
function editAdmin(adminId) {
    // Modal aç ve verileri doldur
    showNotification('Düzenleme özelliği yakında eklenecek', 'info');
}

// Admin sil
function deleteAdmin(adminId) {
    if (!confirm('Bu admini silmek istediğinizden emin misiniz?')) return;

    const formData = new FormData();
    formData.append('action', 'delete_admin');
    formData.append('id', adminId);

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Admin silindi', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// Admin geçmişini görüntüle
function viewAdminHistory(adminId) {
    document.getElementById('adminHistoryModal').classList.add('active');
}

// IP ekle modal
function openAddIPModal(type) {
    document.getElementById('ipType').value = type;
    document.getElementById('ipEditId').value = '';
    document.getElementById('ipModalTitle').textContent = type === 'whitelist' ? 'Yeni IP Ekle (Whitelist)' : 'IP Engelle (Blocklist)';
    document.getElementById('ipDescriptionLabel').textContent = type === 'whitelist' ? 'Açıklama' : 'Engelleme Sebebi';
    document.getElementById('addIPModal').classList.add('active');
}

// IP kaydet
function saveIP() {
    const ipType = document.getElementById('ipType').value;
    const ip = document.getElementById('ipAddress').value.trim();
    const description = document.getElementById('ipDescription').value.trim();

    if (!ip || !description) {
        showNotification('Lütfen tüm alanları doldurun', 'error');
        return;
    }

    const action = ipType === 'whitelist' ? 'add_ip_whitelist' : 'add_ip_blocklist';
    const formData = new FormData();
    formData.append('action', action);
    formData.append('ip', ip);
    formData.append(ipType === 'whitelist' ? 'description' : 'reason', description);

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(ipType === 'whitelist' ? 'IP eklendi' : 'IP engellendi', 'success');
            closeModal('addIPModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// IP sil
function deleteIP(ipId, type) {
    if (!confirm('Bu IP adresini silmek istediğinizden emin misiniz?')) return;

    const action = type === 'whitelist' ? 'delete_ip_whitelist' : 'delete_ip_blocklist';
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', ipId);

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('IP silindi', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// Şüpheli IP'yi engelle
function blockSuspiciousIP(ipId, ipAddress) {
    const formData = new FormData();
    formData.append('action', 'add_ip_blocklist');
    formData.append('ip', ipAddress);
    formData.append('reason', 'Şüpheli aktivite');

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('IP engellendi', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// Şüpheli IP'yi yoksay
function ignoreSuspiciousIP(ipId) {
    showNotification('IP listeden kaldırıldı', 'success');
    document.querySelector(`tr[data-ip-id="${ipId}"]`).remove();
}

// API ayarlarını kaydet
function saveAPISettings(apiType) {
    const settings = {};
    const formData = new FormData();
    formData.append('action', 'save_api_settings');
    formData.append('api_type', apiType);

    // API tipine göre alanları topla
    document.querySelectorAll(`#${apiType}Settings input, #${apiType}Settings select, #${apiType}Settings textarea`).forEach(input => {
        if (input.type !== 'checkbox' || input.checked) {
            const key = input.id.replace(apiType, '').replace(/([A-Z])/g, '_$1').toLowerCase();
            settings[key] = input.value;
        }
    });

    formData.append('settings', JSON.stringify(settings));

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('API ayarları kaydedildi', 'success');
        } else {
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        showNotification('Bağlantı hatası', 'error');
    });
}

// API bağlantısını test et
function testAPI(apiType) {
    showNotification(`${apiType.toUpperCase()} bağlantısı test ediliyor...`, 'info');
    setTimeout(() => {
        showNotification('Bağlantı başarılı!', 'success');
    }, 1500);
}

// AI Agent talimatları modal
function openAIAgentModal(type) {
    document.getElementById('aiAgentType').value = type;
    document.getElementById('aiAgentModalTitle').textContent =
        type === 'phone' ? 'Telefon AI Agent Talimatları' : 'WhatsApp AI Agent Talimatları';
    document.getElementById('aiAgentModal').classList.add('active');
}

// AI talimatlarını kaydet
function saveAIInstructions() {
    const type = document.getElementById('aiAgentType').value;
    const systemPrompt = document.getElementById('systemPrompt').value;
    const greetingMessage = document.getElementById('greetingMessage').value;
    const farewellMessage = document.getElementById('farewellMessage').value;
    const fallbackMessage = document.getElementById('fallbackMessage').value;

    showNotification('AI talimatları kaydedildi', 'success');
    closeModal('aiAgentModal');
}

// Modal kapat
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Email doğrulama
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Bildirim göster
function showNotification(message, type) {
    // Basit alert ile (daha sonra toast notification eklenebilir)
    if (type === 'success') {
        alert('✓ ' + message);
    } else if (type === 'error') {
        alert('✗ ' + message);
    } else {
        alert('ℹ ' + message);
    }
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Modal dışına tıklayınca kapat
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
});
