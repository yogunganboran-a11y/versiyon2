<?php
require_once 'entegrasyon/config.php';
checkAdminAuth();

$page_title = 'Telefon';

// Telefon aramalarını veritabanından çek
$calls = fetchAll("
    SELECT
        pc.*,
        u.name,
        u.surname,
        u.phone
    FROM phone_calls pc
    LEFT JOIN users u ON pc.user_id = u.id
    ORDER BY pc.created_at DESC
    LIMIT 200
");

// İstatistikler
$total_calls = fetchOne("SELECT COUNT(*) as count FROM phone_calls")['count'] ?? 0;
$incoming_calls = fetchOne("SELECT COUNT(*) as count FROM phone_calls WHERE direction = 'incoming'")['count'] ?? 0;
$outgoing_calls = fetchOne("SELECT COUNT(*) as count FROM phone_calls WHERE direction = 'outgoing'")['count'] ?? 0;

// Süreyi formatla
function formatDuration($seconds) {
    if (!$seconds) return '0:00';
    $minutes = floor($seconds / 60);
    $secs = $seconds % 60;
    return sprintf('%d:%02d', $minutes, $secs);
}
?>

<link rel="stylesheet" href="assets/css/telefon.css">

<div class="page-header">
    <h1><i class="fas fa-phone"></i> Telefon Aramaları</h1>
    <p class="page-subtitle">Gelen ve giden çağrı kayıtları</p>
</div>

<!-- Filtreler -->
<div class="phone-filters">
    <div class="filter-group">
        <label>Ara:</label>
        <input type="text" id="callSearch" class="form-control" placeholder="Telefon numarası..." onkeyup="applyFilters()">
    </div>
    
    <div class="filter-group">
        <label>Tip:</label>
        <select id="typeFilter" class="form-control" onchange="applyFilters()">
            <option value="all">Tümü</option>
            <option value="incoming">Gelen</option>
            <option value="outgoing">Giden</option>
        </select>
    </div>
    
    <div class="filter-group">
        <label>Tarih:</label>
        <input type="date" id="dateFrom" class="form-control">
        <span style="color: #8b9cbc;">-</span>
        <input type="date" id="dateTo" class="form-control">
    </div>
    
    <button class="btn btn-primary btn-sm" onclick="applyFilters()">
        <i class="fas fa-filter"></i> Filtrele
    </button>
    
    <button class="btn btn-secondary btn-sm" onclick="clearFilters()">
        <i class="fas fa-times"></i> Temizle
    </button>
</div>

<!-- İstatistikler -->
<div class="phone-stats">
    <div class="phone-stat-card total">
        <div class="stat-header">
            <div>
                <div class="stat-label">Toplam Arama</div>
                <div class="stat-value" id="totalCalls"><?php echo $total_calls; ?></div>
            </div>
            <div class="stat-icon-circle">
                <i class="fas fa-phone"></i>
            </div>
        </div>
    </div>
    
    <div class="phone-stat-card incoming">
        <div class="stat-header">
            <div>
                <div class="stat-label">Gelen Aramalar</div>
                <div class="stat-value" id="incomingCalls"><?php echo $incoming_calls; ?></div>
            </div>
            <div class="stat-icon-circle">
                <i class="fas fa-phone"></i>
            </div>
        </div>
    </div>
    
    <div class="phone-stat-card outgoing">
        <div class="stat-header">
            <div>
                <div class="stat-label">Giden Aramalar</div>
                <div class="stat-value" id="outgoingCalls"><?php echo $outgoing_calls; ?></div>
            </div>
            <div class="stat-icon-circle">
                <i class="fas fa-phone"></i>
            </div>
        </div>
    </div>
</div>

<!-- Arama Tablosu -->
<div class="calls-table">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>TARİH</th>
                    <th>TELEFON</th>
                    <th>TİP</th>
                    <th>SÜRE</th>
                    <th>KAYIT</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($calls)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #8b9cbc;">
                        Henüz telefon araması kaydı bulunmuyor
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($calls as $call):
                        $typeLabels = ['incoming' => 'Gelen', 'outgoing' => 'Giden', 'missed' => 'Cevapsız'];
                        $typeLabel = $typeLabels[$call['direction']] ?? 'Bilinmiyor';
                    ?>
                    <tr data-type="<?php echo $call['direction']; ?>" onclick="showCallDetails(<?php echo $call['id']; ?>)">
                        <td><?php echo date('d.m.Y H:i', strtotime($call['created_at'])); ?></td>
                        <td>
                            <span class="phone-number"><?php echo htmlspecialchars($call['phone'] ?? 'Bilinmiyor'); ?></span>
                        </td>
                        <td>
                            <span class="call-type <?php echo $call['direction']; ?>">
                                <i class="fas fa-<?php echo $call['direction'] === 'incoming' ? 'phone-alt' : ($call['direction'] === 'outgoing' ? 'phone' : 'phone-slash'); ?>"></i>
                                <?php echo $typeLabel; ?>
                            </span>
                        </td>
                        <td>
                            <span class="duration-badge">
                                <i class="far fa-clock"></i>
                                <?php echo formatDuration($call['duration'] ?? 0); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($call['recording_url'])): ?>
                            <i class="fas fa-microphone" style="color: #10b981;" title="Kayıt mevcut"></i>
                            <?php else: ?>
                            <i class="fas fa-times" style="color: #ef4444;" title="Kayıt yok"></i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
        <button class="pagination-btn" onclick="changePage(1)" id="firstPage">
            <i class="fas fa-angle-double-left"></i>
        </button>
        <button class="pagination-btn" onclick="changePage('prev')" id="prevPage">
            <i class="fas fa-angle-left"></i>
        </button>
        
        <div class="pagination-numbers" id="paginationNumbers"></div>
        
        <button class="pagination-btn" onclick="changePage('next')" id="nextPage">
            <i class="fas fa-angle-right"></i>
        </button>
        <button class="pagination-btn" onclick="changePage('last')" id="lastPage">
            <i class="fas fa-angle-double-right"></i>
        </button>
        
        <span class="pagination-info">
            Sayfa <span id="currentPage">1</span> / <span id="totalPages">4</span>
        </span>
    </div>
</div>

<!-- Arama Detay Modal -->
<div class="modal call-detail-modal" id="callDetailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Arama Detayları</h2>
            <button class="close-modal" onclick="closeModal('callDetailModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="callDetailContent"></div>
    </div>
</div>

<script src="assets/js/telefon.js"></script>

<?php include 'includes/footer.php'; ?>