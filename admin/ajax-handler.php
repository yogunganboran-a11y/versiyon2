<?php
require_once 'entegrasyon/config.php';

// Admin kontrolü
checkAdminAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        // ==================== AYARLAR ====================
        case 'save_general_settings':
            $siteName = clean($_POST['site_name'] ?? '');
            $siteDescription = clean($_POST['site_description'] ?? '');
            $siteUrl = clean($_POST['site_url'] ?? '');
            $contactEmail = clean($_POST['contact_email'] ?? '');
            $contactPhone = clean($_POST['contact_phone'] ?? '');
            $whatsappPhone = clean($_POST['whatsapp_phone'] ?? '');
            $instagramUsername = clean($_POST['instagram_username'] ?? '');
            $facebookUsername = clean($_POST['facebook_username'] ?? '');

            updateSetting('site_name', $siteName);
            updateSetting('site_description', $siteDescription);
            updateSetting('site_url', $siteUrl);
            updateSetting('contact_email', $contactEmail);
            updateSetting('contact_phone', $contactPhone);
            updateSetting('whatsapp_phone', $whatsappPhone);
            updateSetting('instagram_username', $instagramUsername);
            updateSetting('facebook_username', $facebookUsername);

            echo json_encode(['success' => true, 'message' => 'Ayarlar kaydedildi']);
            break;

        case 'upload_logo':
            if (isset($_FILES['logo'])) {
                $result = uploadFile($_FILES['logo'], ASSETS_PATH . '/images', ['jpg', 'jpeg', 'png', 'gif']);
                if ($result['success']) {
                    $logoUrl = ASSETS_URL . 'images/' . $result['filename'];
                    updateSetting('site_logo_url', $logoUrl);
                    echo json_encode(['success' => true, 'url' => $logoUrl]);
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Dosya yüklenmedi']);
            }
            break;

        case 'upload_favicon':
            if (isset($_FILES['favicon'])) {
                $result = uploadFile($_FILES['favicon'], ASSETS_PATH . '/images', ['ico', 'png']);
                if ($result['success']) {
                    $faviconUrl = ASSETS_URL . 'images/' . $result['filename'];
                    updateSetting('site_favicon_url', $faviconUrl);
                    echo json_encode(['success' => true, 'url' => $faviconUrl]);
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Dosya yüklenmedi']);
            }
            break;

        case 'save_api_settings':
            $apiType = $_POST['api_type'] ?? '';
            $settings = $_POST['settings'] ?? [];

            foreach ($settings as $key => $value) {
                updateSetting($apiType . '_' . $key, $value);
            }

            echo json_encode(['success' => true, 'message' => 'API ayarları kaydedildi']);
            break;

        // ==================== ADMİN YÖNETİMİ ====================
        case 'add_admin':
            $name = clean($_POST['name'] ?? '');
            $email = clean($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = clean($_POST['role'] ?? 'moderator');
            $permissions = $_POST['permissions'] ?? [];

            if (empty($name) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Tüm alanları doldurun']);
                break;
            }

            // Email kontrolü
            $existing = fetchOne("SELECT id FROM admins WHERE email = ?", [$email]);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Bu email zaten kullanılıyor']);
                break;
            }

            $hashedPassword = hashPassword($password);
            $permissionsJson = json_encode($permissions);

            $id = insert("INSERT INTO admins (full_name, email, password, role, permissions, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
                [$name, $email, $hashedPassword, $role, $permissionsJson]);

            echo json_encode(['success' => true, 'message' => 'Admin eklendi', 'id' => $id]);
            break;

        case 'update_admin':
            $id = intval($_POST['id'] ?? 0);
            $name = clean($_POST['name'] ?? '');
            $email = clean($_POST['email'] ?? '');
            $role = clean($_POST['role'] ?? 'moderator');
            $permissions = $_POST['permissions'] ?? [];

            if (empty($name) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Tüm alanları doldurun']);
                break;
            }

            $permissionsJson = json_encode($permissions);

            query("UPDATE admins SET full_name = ?, email = ?, role = ?, permissions = ? WHERE id = ?",
                [$name, $email, $role, $permissionsJson, $id]);

            echo json_encode(['success' => true, 'message' => 'Admin güncellendi']);
            break;

        case 'delete_admin':
            $id = intval($_POST['id'] ?? 0);

            // Kendini silmeyi engelle
            if ($id == $_SESSION['admin_id']) {
                echo json_encode(['success' => false, 'message' => 'Kendi hesabınızı silemezsiniz']);
                break;
            }

            query("DELETE FROM admins WHERE id = ?", [$id]);
            echo json_encode(['success' => true, 'message' => 'Admin silindi']);
            break;

        case 'get_admins':
            $admins = fetchAll("SELECT id, full_name, email, role, last_login, created_at FROM admins ORDER BY created_at DESC");
            echo json_encode(['success' => true, 'admins' => $admins]);
            break;

        // ==================== EĞİTİMLER ====================
        case 'add_course':
            $title = clean($_POST['title'] ?? '');
            $shortName = clean($_POST['short_name'] ?? '');
            $duration = intval($_POST['duration'] ?? 0);
            $price = floatval($_POST['price'] ?? 0);
            $discountPrice = floatval($_POST['discount_price'] ?? 0);
            $certificateDays = intval($_POST['certificate_days'] ?? 0);
            $certificateTime = clean($_POST['certificate_time'] ?? '00:00');
            $videoUrl = clean($_POST['video_url'] ?? '');

            if (empty($title) || empty($shortName)) {
                echo json_encode(['success' => false, 'message' => 'Eğitim adı ve kısaltması zorunludur']);
                break;
            }

            $id = insert("INSERT INTO courses (title, short_name, duration_hours, price, discount_price, certificate_release_days, certificate_release_time, video_url, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [$title, $shortName, $duration, $price, $discountPrice, $certificateDays, $certificateTime, $videoUrl]);

            echo json_encode(['success' => true, 'message' => 'Eğitim eklendi', 'id' => $id]);
            break;

        case 'update_course':
            $id = intval($_POST['id'] ?? 0);
            $title = clean($_POST['title'] ?? '');
            $shortName = clean($_POST['short_name'] ?? '');
            $duration = intval($_POST['duration'] ?? 0);
            $price = floatval($_POST['price'] ?? 0);
            $discountPrice = floatval($_POST['discount_price'] ?? 0);

            query("UPDATE courses SET title = ?, short_name = ?, duration_hours = ?, price = ?, discount_price = ? WHERE id = ?",
                [$title, $shortName, $duration, $price, $discountPrice, $id]);

            echo json_encode(['success' => true, 'message' => 'Eğitim güncellendi']);
            break;

        case 'delete_course':
            $id = intval($_POST['id'] ?? 0);
            query("DELETE FROM courses WHERE id = ?", [$id]);
            echo json_encode(['success' => true, 'message' => 'Eğitim silindi']);
            break;

        case 'get_courses':
            $courses = fetchAll("SELECT c.*, (SELECT COUNT(*) FROM user_courses WHERE course_id = c.id) as student_count FROM courses c ORDER BY created_at DESC");
            echo json_encode(['success' => true, 'courses' => $courses]);
            break;

        case 'add_test_question':
            $courseId = intval($_POST['course_id'] ?? 0);
            $question = clean($_POST['question'] ?? '');
            $optionA = clean($_POST['option_a'] ?? '');
            $optionB = clean($_POST['option_b'] ?? '');
            $optionC = clean($_POST['option_c'] ?? '');
            $optionD = clean($_POST['option_d'] ?? '');
            $correctAnswer = clean($_POST['correct_answer'] ?? '');

            $id = insert("INSERT INTO course_test_questions (course_id, question_text, option_a, option_b, option_c, option_d, correct_answer)
                VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$courseId, $question, $optionA, $optionB, $optionC, $optionD, $correctAnswer]);

            echo json_encode(['success' => true, 'message' => 'Soru eklendi', 'id' => $id]);
            break;

        // ==================== KULLANICILAR ====================
        case 'get_users':
            $users = fetchAll("SELECT id, tckn, name, surname, phone, birth_date, created_at FROM users ORDER BY created_at DESC");
            echo json_encode(['success' => true, 'users' => $users]);
            break;

        case 'add_user':
            $tckn = clean($_POST['tckn'] ?? '');
            $name = clean($_POST['name'] ?? '');
            $surname = clean($_POST['surname'] ?? '');
            $phone = clean($_POST['phone'] ?? '');
            $birthDate = clean($_POST['birth_date'] ?? '');

            // TCKN kontrolü
            $existing = fetchOne("SELECT id FROM users WHERE tckn = ?", [$tckn]);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Bu TCKN zaten kayıtlı']);
                break;
            }

            $id = insert("INSERT INTO users (tckn, name, surname, phone, birth_date, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
                [$tckn, $name, $surname, $phone, $birthDate]);

            echo json_encode(['success' => true, 'message' => 'Kullanıcı eklendi', 'id' => $id]);
            break;

        case 'update_user':
            $id = intval($_POST['id'] ?? 0);
            $name = clean($_POST['name'] ?? '');
            $surname = clean($_POST['surname'] ?? '');
            $phone = clean($_POST['phone'] ?? '');
            $birthDate = clean($_POST['birth_date'] ?? '');

            query("UPDATE users SET name = ?, surname = ?, phone = ?, birth_date = ? WHERE id = ?",
                [$name, $surname, $phone, $birthDate, $id]);

            echo json_encode(['success' => true, 'message' => 'Kullanıcı güncellendi']);
            break;

        case 'delete_user':
            $id = intval($_POST['id'] ?? 0);
            query("DELETE FROM users WHERE id = ?", [$id]);
            echo json_encode(['success' => true, 'message' => 'Kullanıcı silindi']);
            break;

        case 'assign_course':
            $userId = intval($_POST['user_id'] ?? 0);
            $courseId = intval($_POST['course_id'] ?? 0);

            // Zaten kayıtlı mı kontrol et
            $existing = fetchOne("SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?", [$userId, $courseId]);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Kullanıcı bu eğitimi zaten almış']);
                break;
            }

            $id = insert("INSERT INTO user_courses (user_id, course_id, enrollment_date, status) VALUES (?, ?, NOW(), 'active')",
                [$userId, $courseId]);

            echo json_encode(['success' => true, 'message' => 'Eğitim atandı', 'id' => $id]);
            break;

        // ==================== DESTEK TALEPLERİ ====================
        case 'get_support_requests':
            $requests = fetchAll("SELECT sr.*, u.name, u.surname FROM support_requests sr
                LEFT JOIN users u ON sr.user_id = u.id
                ORDER BY sr.created_at DESC");
            echo json_encode(['success' => true, 'requests' => $requests]);
            break;

        case 'update_support_status':
            $id = intval($_POST['id'] ?? 0);
            $status = clean($_POST['status'] ?? '');

            query("UPDATE support_requests SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $id]);
            echo json_encode(['success' => true, 'message' => 'Durum güncellendi']);
            break;

        case 'add_support_note':
            $requestId = intval($_POST['request_id'] ?? 0);
            $note = clean($_POST['note'] ?? '');
            $adminId = $_SESSION['admin_id'];

            insert("INSERT INTO support_notes (request_id, admin_id, note, created_at) VALUES (?, ?, ?, NOW())",
                [$requestId, $adminId, $note]);

            echo json_encode(['success' => true, 'message' => 'Not eklendi']);
            break;

        // ==================== FATURALAR ====================
        case 'get_invoices':
            $invoices = fetchAll("SELECT i.*, u.name, u.surname FROM invoices i
                LEFT JOIN users u ON i.user_id = u.id
                ORDER BY i.created_at DESC");
            echo json_encode(['success' => true, 'invoices' => $invoices]);
            break;

        // ==================== İSTATİSTİKLER ====================
        case 'get_dashboard_stats':
            $totalUsers = fetchOne("SELECT COUNT(*) as count FROM users")['count'];
            $totalCourses = fetchOne("SELECT COUNT(*) as count FROM courses")['count'];
            $totalRevenue = fetchOne("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'")['total'] ?? 0;
            $pendingSupport = fetchOne("SELECT COUNT(*) as count FROM support_requests WHERE status = 'pending'")['count'];

            echo json_encode([
                'success' => true,
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_courses' => $totalCourses,
                    'total_revenue' => $totalRevenue,
                    'pending_support' => $pendingSupport
                ]
            ]);
            break;

        case 'get_recent_sales':
            $sales = fetchAll("SELECT p.*, u.name, u.surname, c.title as course_title
                FROM payments p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN courses c ON p.course_id = c.id
                WHERE p.status = 'completed'
                ORDER BY p.created_at DESC
                LIMIT 10");
            echo json_encode(['success' => true, 'sales' => $sales]);
            break;

        // ==================== IP YÖNETİMİ ====================
        case 'add_ip_whitelist':
            $ip = clean($_POST['ip'] ?? '');
            $description = clean($_POST['description'] ?? '');

            insert("INSERT INTO ip_whitelist (ip_address, description, created_at) VALUES (?, ?, NOW())", [$ip, $description]);
            echo json_encode(['success' => true, 'message' => 'IP eklendi']);
            break;

        case 'delete_ip_whitelist':
            $id = intval($_POST['id'] ?? 0);
            query("DELETE FROM ip_whitelist WHERE id = ?", [$id]);
            echo json_encode(['success' => true, 'message' => 'IP silindi']);
            break;

        case 'add_ip_blocklist':
            $ip = clean($_POST['ip'] ?? '');
            $reason = clean($_POST['reason'] ?? '');

            insert("INSERT INTO ip_blocklist (ip_address, reason, created_at) VALUES (?, ?, NOW())", [$ip, $reason]);
            echo json_encode(['success' => true, 'message' => 'IP engellendi']);
            break;

        case 'delete_ip_blocklist':
            $id = intval($_POST['id'] ?? 0);
            query("DELETE FROM ip_blocklist WHERE id = ?", [$id]);
            echo json_encode(['success' => true, 'message' => 'Engel kaldırıldı']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Geçersiz işlem']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
?>
