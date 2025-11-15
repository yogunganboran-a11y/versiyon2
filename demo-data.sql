-- Demo Veriler - Test için örnek kayıtlar
-- Not: Bu SQL dosyası Türkçe karakter uyumlu UTF-8 formatındadır

-- SMS bildirimleri (sms_history tablosu)
INSERT INTO sms_history (phone, message, status, created_at) VALUES
('05321234567', 'Testiniz başarıyla tamamlandı. Python Web Geliştirme eğitiminde 92 puan aldınız.', 'sent', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
('05321234567', 'Yeni video içeriği eklendi: React ile Modern Web Uygulamaları. Hemen izlemeye başlayabilirsiniz.', 'sent', DATE_SUB(NOW(), INTERVAL 1 DAY)),
('05321234567', 'Sertifikanız hazır! Python Web Geliştirme sertifikanızı artık indirebilirsiniz.', 'sent', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('05331234567', 'Ödemeniz başarıyla alındı. Docker & Kubernetes eğitimine kayıt işleminiz tamamlanmıştır.', 'sent', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
('05341234567', 'Eğitim hatırlatması: Veritabanı Yönetimi dersine devam etmeyi unutmayın.', 'sent', DATE_SUB(NOW(), INTERVAL 5 HOUR));

-- WhatsApp mesajları (whatsapp_conversations ve whatsapp_messages)
INSERT INTO whatsapp_conversations (user_id, status, last_activity, created_at) VALUES
(1, 'active', NOW(), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'active', DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_SUB(NOW(), INTERVAL 5 DAY));

INSERT INTO whatsapp_messages (conversation_id, direction, message, is_read, created_at) VALUES
(1, 'incoming', 'Merhaba, Python eğitimi hakkında bilgi alabilir miyim?', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'outgoing', 'Merhaba! Tabii ki. Python Web Geliştirme eğitimimiz 40 saat sürüyor ve üniversite onaylı sertifika veriyoruz.', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'incoming', 'Fiyatı nedir?', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'outgoing', 'Normal fiyatı 1499₺ ama şu an %40 indirimli 899₺', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'incoming', 'Docker eğitimine kayıt oldum ama videoya erişemiyorum', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(2, 'outgoing', 'Hemen kontrol ediyorum, 2 dakika içinde çözüme kavuşturacağım.', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR));

-- Telefon aramaları (phone_calls)
INSERT INTO phone_calls (user_id, phone, direction, duration, status, recording_url, created_at) VALUES
(1, '05321234567', 'outgoing', 245, 'completed', NULL, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, '05331234567', 'incoming', 180, 'completed', NULL, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(3, '05341234567', 'outgoing', 0, 'missed', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, '05321234567', 'outgoing', 420, 'completed', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Meta (Facebook/Instagram) mesajları (meta_messages)
INSERT INTO meta_messages (user_id, platform, message, direction, is_read, last_activity, created_at) VALUES
(1, 'facebook', 'Kayıt olmak istiyorum', 'incoming', 1, NOW(), DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(2, 'instagram', 'Sertifika süreci ne kadar sürer?', 'incoming', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'facebook', 'Toplu firma kaydı yapmak istiyoruz', 'incoming', 0, DATE_SUB(NOW(), INTERVAL 30 MINUTE), DATE_SUB(NOW(), INTERVAL 30 MINUTE));

-- IP takip kayıtları (ip_logs)
INSERT INTO ip_logs (ip_address, user_agent, country, city, created_at) VALUES
('85.34.123.45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'Turkey', 'Istanbul', NOW()),
('85.34.123.45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'Turkey', 'Istanbul', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
('176.88.45.12', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15', 'Turkey', 'Ankara', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
('94.54.12.98', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) Safari/604.1', 'Turkey', 'Izmir', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
('85.34.123.45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0', 'Turkey', 'Istanbul', DATE_SUB(NOW(), INTERVAL 5 HOUR));

-- Geri arama listesi (callbacks)
INSERT INTO callbacks (user_id, phone, source, scheduled_time, status, rule_name, priority, attempts, max_attempts, created_at) VALUES
(1, '05321234567', 'whatsapp', DATE_ADD(NOW(), INTERVAL 15 MINUTE), 'scheduled', 'WhatsApp Satın Almadı', 'high', 0, 3, NOW()),
(2, '05331234567', 'phone', DATE_ADD(NOW(), INTERVAL 1 DAY), 'scheduled', 'Arandı Satın Almadı', 'medium', 1, 2, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, '05341234567', 'phone', DATE_ADD(NOW(), INTERVAL 3 DAY), 'scheduled', 'Kararsız Kaldı', 'low', 0, 4, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(4, '05351234567', 'whatsapp', NOW(), 'completed', 'WhatsApp Satın Almadı', 'high', 2, 3, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, '05361234567', 'phone', DATE_SUB(NOW(), INTERVAL 2 HOUR), 'failed', 'Arandı Satın Almadı', 'medium', 3, 3, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Firma ödemeleri (payments tablosu için)
INSERT INTO payments (user_id, course_id, amount, payment_method, status, company_name, created_at) VALUES
(10, 1, 13500.00, 'company', 'completed', 'ABC Teknoloji A.Ş.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(11, 2, 8000.00, 'company', 'completed', 'XYZ Yazılım Ltd.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(12, 1, 4500.00, 'company', 'completed', 'Mega Danışmanlık', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 3, 899.00, 'online', 'completed', NULL, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 4, 1299.00, 'transfer', 'completed', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Destek talepleri (support_requests)
INSERT INTO support_requests (user_id, category, status, message, source, created_at) VALUES
(1, 'iletisim', 'pending', 'Python eğitimi hakkında detaylı bilgi almak istiyorum', 'whatsapp', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 'teknik', 'in_progress', 'Video oynatıcı çalışmıyor, sürekli yükleniyor yazısı kalıyor', 'phone', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(3, 'havale', 'pending', '1500₺ havale yaptım, dekont ekte', 'whatsapp', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 'iptal', 'completed', 'Docker eğitimini iptal etmek istiyorum', 'web', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 'toplu', 'pending', '20 kişilik toplu kayıt için teklif istiyoruz', 'phone', DATE_SUB(NOW(), INTERVAL 3 HOUR));

-- Test soruları örneği (course_test_questions)
INSERT INTO course_test_questions (course_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES
(1, 'Python hangi yıl geliştirilmeye başlanmıştır?', '1989', '1991', '1995', '2000', 'B'),
(1, 'Python''da liste oluşturmak için hangi sembol kullanılır?', '()', '[]', '{}', '<>', 'B'),
(1, 'Python''da yorum satırı yazmak için hangi işaret kullanılır?', '//', '/*', '#', '--', 'C'),
(2, 'Docker container nedir?', 'Sanal makine', 'İzole edilmiş uygulama ortamı', 'Bulut sunucu', 'Dosya sistemi', 'B'),
(2, 'Kubernetes ne işe yarar?', 'Database yönetimi', 'Container orkestrasyon', 'Web sunucu', 'DNS yönetimi', 'B');

-- Faturalar (invoices)
INSERT INTO invoices (user_id, course_id, file_path, created_at) VALUES
(1, 1, '/invoices/2025/invoice_1_20250115.pdf', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 2, '/invoices/2025/invoice_2_20250114.pdf', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(3, 3, '/invoices/2025/invoice_3_20250113.pdf', DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Örnek kullanıcı güncellemesi (users tablosuna birth_date ekle)
UPDATE users SET birth_date = '1995-05-15' WHERE id = 1;
UPDATE users SET birth_date = '1992-08-20' WHERE id = 2;
UPDATE users SET birth_date = '1998-03-10' WHERE id = 3;

-- Not: Bu SQL demo verilerdir. Gerçek sistemde bunların yerine gerçek veriler olacaktır.
-- Türkçe karakterler UTF-8 encoding ile düzgün çalışacaktır.
