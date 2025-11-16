<?php
require_once 'includes/auth-check.php';

$page_title = 'Eğitim Testi';
$page_css = 'assets/css/egitim-test.css';

// Eğitim ID'sini al
$courseId = intval($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];

// Eğitim bilgilerini çek
$course = fetchOne("
    SELECT
        c.*,
        uc.video_watched,
        uc.test_completed
    FROM courses c
    JOIN user_courses uc ON uc.course_id = c.id
    WHERE c.id = ? AND uc.user_id = ?
", [$courseId, $userId]);

if (!$course) {
    header('Location: egitimler.php');
    exit;
}

// Video izlenmemiş ise teste izin verme
if (!$course['video_watched']) {
    header('Location: egitim-video.php?id=' . $courseId);
    exit;
}

$egitim_adi = $course['title'];

// Soruları veritabanından çek
$questions = fetchAll("
    SELECT *
    FROM course_test_questions
    WHERE course_id = ?
    ORDER BY id ASC
", [$courseId]);

// Eski format için dönüştür
$sorular = [];
foreach ($questions as $q) {
    $sorular[] = [
        'id' => $q['id'],
        'soru' => $q['question_text'],
        'secenekler' => [
            'A' => $q['option_a'],
            'B' => $q['option_b'],
            'C' => $q['option_c'],
            'D' => $q['option_d']
        ],
        'dogru' => $q['correct_answer']
    ];
}

include 'includes/header.php';
?>

<!-- Test Container -->
<div class="test-container">
    <!-- Question Card -->
    <div class="question-card" id="questionCard">
        <div class="question-header">
            <span class="question-number" id="questionNumber">Soru 1/5</span>
        </div>
        <h3 class="question-text" id="questionText"></h3>
        <div class="answers" id="answersContainer"></div>
    </div>
    
    <!-- Navigation -->
    <div class="test-navigation">
        <button class="btn-prev" id="prevBtn" style="display: none;">
            <i class="fas fa-arrow-left"></i> Önceki Soru
        </button>
        <button class="btn-next" id="nextBtn" style="display: none;">
            Sonraki Soru <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<script>
const sorular = <?php echo json_encode($sorular); ?>;
let currentQuestion = 0;
let dogruSayisi = 0;
let yanlisSayisi = 0;
let cevaplar = [];

function showQuestion() {
    const soru = sorular[currentQuestion];
    
    document.getElementById('questionNumber').textContent = `Soru ${currentQuestion + 1}/${sorular.length}`;
    document.getElementById('questionText').textContent = soru.soru;
    
    const answersContainer = document.getElementById('answersContainer');
    answersContainer.innerHTML = '';
    
    // Show previous answers if any
    const previousAnswer = cevaplar[currentQuestion];
    
    Object.entries(soru.secenekler).forEach(([key, value]) => {
        const label = document.createElement('label');
        label.className = 'answer-option';
        
        // Apply previous state if navigating back
        if (previousAnswer) {
            if (key === soru.dogru) {
                label.classList.add('correct');
            } else if (previousAnswer.yanlis.includes(key)) {
                label.classList.add('wrong');
            }
        }
        
        label.innerHTML = `
            <span class="option-letter">${key}</span>
            <span class="option-text">${value}</span>
        `;
        
        if (!previousAnswer) {
            label.onclick = () => checkAnswer(key, label);
        }
        
        answersContainer.appendChild(label);
    });
    
    // Show/hide navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    prevBtn.style.display = 'flex';
    nextBtn.style.display = 'flex';
    
    if (currentQuestion === 0) {
        prevBtn.disabled = true;
    } else {
        prevBtn.disabled = false;
    }
    
    if (cevaplar[currentQuestion] && currentQuestion < sorular.length - 1) {
        nextBtn.disabled = false;
    } else {
        nextBtn.disabled = true;
    }
}

function checkAnswer(selected, labelElement) {
    const soru = sorular[currentQuestion];
    
    if (!cevaplar[currentQuestion]) {
        cevaplar[currentQuestion] = {
            dogru: false,
            yanlis: []
        };
    }
    
    if (selected === soru.dogru) {
        // Doğru cevap
        labelElement.classList.add('correct');
        if (!cevaplar[currentQuestion].dogru) {
            dogruSayisi++;
            cevaplar[currentQuestion].dogru = true;
        }
        
        // Enable next button immediately
        if (currentQuestion < sorular.length - 1) {
            document.getElementById('nextBtn').disabled = false;
        } else {
            // Small delay before finishing to show correct answer
            setTimeout(() => {
                finishTest();
            }, 500);
        }
    } else {
        // Yanlış cevap
        labelElement.classList.add('wrong');
        if (!cevaplar[currentQuestion].yanlis.includes(selected)) {
            yanlisSayisi++;
            cevaplar[currentQuestion].yanlis.push(selected);
        }
    }
}

document.getElementById('prevBtn').addEventListener('click', () => {
    if (currentQuestion > 0) {
        currentQuestion--;
        showQuestion();
    }
});

document.getElementById('nextBtn').addEventListener('click', () => {
    if (currentQuestion < sorular.length - 1) {
        currentQuestion++;
        showQuestion();
    }
});

function finishTest() {
    const puan = Math.round((dogruSayisi / sorular.length) * 100);
    window.location.href = `egitim-test-sonuc.php?dogru=${dogruSayisi}&yanlis=${yanlisSayisi}&puan=${puan}`;
}

// İlk soruyu göster
showQuestion();
</script>

<?php include 'includes/footer.php'; ?>