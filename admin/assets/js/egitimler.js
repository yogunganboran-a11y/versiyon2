// Eğitimler Sayfası - JavaScript

let currentStep = 1;
let questionCount = 0;

// Modal aç
function openAddCourseModal() {
    document.getElementById('addCourseModal').classList.add('active');
    currentStep = 1;
    updateSteps();
}

// Modal kapat
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Adımlar arası geçiş
function nextStep() {
    if (currentStep === 1) {
        if (!validateStep1()) return;
        currentStep = 2;
        updateSteps();
    }
}

function prevStep() {
    if (currentStep === 2) {
        currentStep = 1;
        updateSteps();
    }
}

function updateSteps() {
    document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.toggle('active', index + 1 === currentStep);
    });
    document.querySelectorAll('.form-step').forEach((step, index) => {
        step.classList.toggle('active', index + 1 === currentStep);
    });
    document.querySelector('.btn-prev').style.display = currentStep === 1 ? 'none' : 'inline-flex';
    document.querySelector('.btn-next').style.display = currentStep === 2 ? 'none' : 'inline-flex';
    document.querySelector('.btn-complete').style.display = currentStep === 2 ? 'inline-flex' : 'none';
}

function validateStep1() {
    const title = document.getElementById('courseTitle').value.trim();
    const shortName = document.getElementById('courseShortName').value.trim();
    if (!title || !shortName) {
        alert('Lütfen zorunlu alanları doldurun');
        return false;
    }
    return true;
}

function handleVideoUpload(event) {
    const file = event.target.files[0];
    if (file) {
        document.getElementById('videoPreview').innerHTML = `<p style="color: #10b981;">✓ ${file.name} seçildi</p>`;
    }
}

function addQuestion() {
    questionCount++;
    const html = `<div class="question-item" data-question-id="${questionCount}">
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <h4>Soru ${questionCount}</h4>
            <button type="button" onclick="removeQuestion(${questionCount})" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">Sil</button>
        </div>
        <div class="form-group">
            <label>Soru Metni *</label>
            <input type="text" class="form-control question-text" required>
        </div>
        <div class="form-row">
            <div class="form-group"><label>A Şıkkı</label><input type="text" class="form-control option-a"></div>
            <div class="form-group"><label>B Şıkkı</label><input type="text" class="form-control option-b"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>C Şıkkı</label><input type="text" class="form-control option-c"></div>
            <div class="form-group"><label>D Şıkkı</label><input type="text" class="form-control option-d"></div>
        </div>
        <div class="form-group">
            <label>Doğru Cevap</label>
            <select class="form-control correct-answer">
                <option value="">Seçiniz</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>
    </div>`;
    document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', html);
}

function removeQuestion(id) {
    document.querySelector(`[data-question-id="${id}"]`).remove();
}

function completeCourse() {
    const formData = new FormData();
    formData.append('action', 'add_course');
    formData.append('title', document.getElementById('courseTitle').value);
    formData.append('short_name', document.getElementById('courseShortName').value);
    formData.append('duration', document.getElementById('courseDuration').value || 0);
    formData.append('price', document.getElementById('coursePrice').value || 0);
    formData.append('discount_price', document.getElementById('courseOldPrice').value || 0);
    formData.append('certificate_days', document.getElementById('certificateDays').value || 0);
    formData.append('certificate_time', document.getElementById('certificateTime').value || '00:00');

    fetch('ajax-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ Eğitim eklendi');
            closeModal('addCourseModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('✗ ' + (data.message || 'Hata oluştu'));
        }
    })
    .catch(() => alert('✗ Bağlantı hatası'));
}

function editCourse(id) {
    alert('ℹ Düzenleme yakında eklenecek');
}

function deleteCourse(id) {
    if (!confirm('Silmek istediğinizden emin misiniz?')) return;
    const formData = new FormData();
    formData.append('action', 'delete_course');
    formData.append('id', id);
    fetch('ajax-handler.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ Silindi');
            location.reload();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => updateSteps());
