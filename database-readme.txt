📊 VERİTABANI SÜTUNLARI - AÇIKLAMALAR
1. admins:
id: Otomatik artan birincil anahtar
password: bcrypt şifrelenmiş admin şifresi
email: Admin e-posta adresi
full_name: Admin adı soyadı
role: Admin rolü (Yönetici/Personel)
last_login: Son giriş zamanı
created_at: Kayıt tarihi
2. admin_permissions:
id: Otomatik artan birincil anahtar
admin_id: Hangi admin (foreign key → admins.id)
permission: Sayfa yetkisi (sales/customers/whatsapp vb)
created_at: Kayıt tarihi
3. users:
id: Otomatik artan birincil anahtar
tckn: TC Kimlik No (unique, 11 karakter)
name: Ad
surname: Soyad
phone: Telefon numarası
birth_date: Doğum tarihi
password:bcrypt şifrelenmiş kullanıcı şifresi (TCKN numarası bcrypt ile şifrelenir, kullanıcı TCKN ile giriş yapar)
company: Şirket adı
created_at: Kayıt tarihi
4. courses:
id: Otomatik artan birincil anahtar
title: Eğitim başlığı
short_title: Kısa başlık/kısaltma
duration_hours: Eğitim süresi (saat, DECIMAL)
video_url: Video linki
price: Normal fiyat
discounted_price: İndirimli fiyat
certificate_days: Sertifika yüklenecek iş günü sayısı
certificate_time: Sertifika yüklenecek günün saati (TIME: 18:30:00)
order_index: Kursun sıralama numarası (listede gösterilme sırası)
created_at: Oluşturulma tarihi
updated_at: Güncellenme tarihi
5. test_questions:
id: Otomatik artan birincil anahtar
course_id: Hangi kursa ait (foreign key → courses.id)
question_text: Soru metni
question_order: Soru sırası
created_at: Oluşturulma tarihi
6. test_options:
id: Otomatik artan birincil anahtar
question_id: Hangi soruya ait (foreign key → test_questions.id)
option_text: Şık metni
option_order: Şık sırası (A, B, C, D)
is_correct: Doğru mu? (0/1)
created_at: Oluşturulma tarihi
7. enrollments:
id: Otomatik artan birincil anahtar
user_id: Hangi kullanıcı (foreign key → users.id)
course_id: Hangi kurs (foreign key → courses.id)
company: Şirket adı (users.company'den otomatik çekilir)
amount: Ödenen tutar
payment_method: Ödeme yöntemi (online/havale/firma)
payment_token: online ise Ödeme gateway token
video_watched: Video izlendi mi? (0/1)
test_completed: Test tamamlandı mı? (0/1)
registration_document: Kayıt belgesi dosya yolu
invoice: Fatura dosya yolu
certificate: Sertifika dosya yolu (NULL ise yüklenmemiş, dosya yolu varsa yüklenmiş)
certificate_issue_date: Sertifika veriliş tarihi
enrolled_at: Kayıt tarihi
completed_at: Tamamlanma tarihi
8. support_tickets:
id: Otomatik artan birincil anahtar
name: Ad
surname: Soyad
tckn: TC Kimlik No
phone: Telefon
birth_date: Doğum tarihi
category: Kategori (bulk/transfer/cancel/technical/contact) 
company_name: Şirket adı
document_count: Belge sayısı
courses: Kurslar (TEXT - talep edilen kurs ID'leri virgülle ayrılmış, örn: "1,2,5")
source: Nereden geldi (whatsapp/telefon/web) 
receipt: Dekont dosya yolu
message: Mesaj
status: Durum
created_at: Oluşturulma tarihi
updated_at: Güncellenme tarihi
9. support_notes:
id: Otomatik artan birincil anahtar
ticket_id: Hangi talep (foreign key → support_tickets.id)
admin_id: Hangi admin yazdı (foreign key → admins.id)
note: Not metni
status_change: Durum değişikliği
created_at: Oluşturulma tarihi
10. callback_requests:
id: Otomatik artan birincil anahtar
name: Ad soyad
phone: Telefon numarası
reason: Arama nedeni
source: Kaynak (web/phone/whatsapp/meta/other)
status: Durum
priority: Öncelik
ai_analyzed: AI analiz edildi mi? (0/1)
ai_score: AI skoru
attempt_count: Arama denemesi sayısı
max_attempts: Maksimum deneme sayısı
scheduled_date: Planlanmış arama tarihi
called_at: Arandığı tarih
notes: Notlar
created_at: Oluşturulma tarihi
updated_at: Güncellenme tarihi
11. callback_rules:
id: Otomatik artan birincil anahtar
name: Kural adı
action_time: Kaç gün sonra ara (INT)
retry_days: Tekrar deneme gün sayısı
max_attempts: Maksimum deneme sayısı
call_start: Arama başlangıç saati (09:00:00)
call_end: Arama bitiş saati (18:00:00)
prompt: AI için prompt metni
is_active: Aktif mi? (0/1)
created_at: Oluşturulma tarihi
updated_at: Güncellenme tarihi
12. phone_calls:
id: Otomatik artan birincil anahtar
phone: Telefon numarası
type: Arama tipi (incoming/outgoing/missed)
call_start: Arama başlangıç zamanı
call_end: Arama bitiş zamanı
recording: Ses kaydı dosya yolu
created_at: Kayıt tarihi
13. phone_transcripts:
id: Otomatik artan birincil anahtar
call_id: Hangi arama (foreign key → phone_calls.id)
speaker: Konuşan (user/agent)
text: Konuşma metni
timestamp: Ne zaman söylendi
created_at: Kayıt tarihi
14. whatsapp_chat:
id: Otomatik artan birincil anahtar
chat_id: WhatsApp sohbet ID (unique)
name: Kişi adı
last_message_at: Son mesaj zamanı
updated_at: Güncellenme zamanı
bot_active: Bot aktif mi? (0/1)
15. whatsapp_messages:
id: Otomatik artan birincil anahtar
chat_id: Hangi sohbet
direction: Yön (incoming/outgoing)
body: Mesaj metni
message_at: Mesaj zamanı
created_at: Kayıt tarihi
16. meta_chat:
id: Otomatik artan birincil anahtar
platform: Platform (facebook/instagram)
chat_id: Sohbet ID
name: Kişi adı
last_message_at: Son mesaj zamanı
updated_at: Güncellenme zamanı
bot_active: Bot aktif mi? (0/1)
17. meta_messages:
id: Otomatik artan birincil anahtar
platform: Platform (facebook/instagram)
chat_id: Hangi sohbet
direction: Yön (incoming/outgoing)
body: Mesaj metni
message_at: Mesaj zamanı
created_at: Kayıt tarihi
18. ip_tracking:
id: Otomatik artan birincil anahtar
ip_address: IP adresi
user_id: Kullanıcı ID (foreign key → users.id)
user_agent: Tarayıcı bilgisi
page_url: Ziyaret edilen sayfa
referrer: Nereden geldi
country: Ülke
city: Şehir
device_type: Cihaz tipi (desktop/mobile/tablet/other)
browser: Tarayıcı
created_at: Ziyaret zamanı
19. ip_blacklist:
id: Otomatik artan birincil anahtar
ip_address: IP adresi (unique)
reason: Engelleme nedeni
blocked_by: Hangi admin engelledi (foreign key → admins.id)
is_blocked: Bloklandı mı? (0=Şüpheli, 1=Engellendi)
created_at: Eklenme tarihi
20. ip_whitelist:
id: Otomatik artan birincil anahtar
ip_address: IP adresi (unique)
description: Açıklama (Ofis, Ev vb)
added_by: Hangi admin ekledi (foreign key → admins.id)
created_at: Eklenme tarihi
21. settings:
Tablo Yapısı:
id: Otomatik artan birincil anahtar
setting_key: Ayar anahtarı (unique)
setting_value: Ayar değeri
setting_type: Veri tipi (text/number/boolean/json/password)
category: Kategori grubu
description: Açıklama
updated_at: Güncellenme tarihi
GENEL AYARLAR (category: general)
site_name: Site adı
site_title: Meta başlık (SEO)
site_description: Meta açıklama (SEO)
site_url: Site URL
site_email: İletişim e-posta
site_phone: İletişim telefon
whatsapp_phone: WhatsApp numarası
instagram_username: Instagram kullanıcı adı
facebook_username: Facebook kullanıcı adı
site_logo_url: Site logosu URL
site_favicon_url: Favicon URL
SEO & ANALİZ (category: seo)
google_analytics_code: Google Analytics kodu
google_tag_manager_code: Google Tag Manager kodu
yandex_metrika_code: Yandex Metrika kodu
bing_webmaster_code: Bing Webmaster kodu
API - PARAŞÜT FATURA (category: api_parasut)
parasut_account_id: Paraşüt Account ID
parasut_company_id: Paraşüt Company ID
parasut_kdv: KDV Oranı
parasut_username: Paraşüt kullanıcı adı
parasut_password: Paraşüt şifre (AES-256 şifreli)
parasut_client_id: Paraşüt Client ID
parasut_client_secret: Paraşüt Client Secret (AES-256 şifreli)
API - NETGSM SMS (category: api_sms)
netgsm_username: NetGSM kullanıcı adı
netgsm_password: NetGSM şifre (AES-256 şifreli)
netgsm_header: SMS başlık
sms_welcome_active: Hoşgeldin SMS aktif (0/1)
sms_welcome_template: Hoşgeldin SMS şablonu
sms_certificate_active: Sertifika SMS aktif (0/1)
sms_certificate_template: Sertifika SMS şablonu
API - PAYTR ÖDEME (category: api_paytr)
paytr_merchant_id: PayTR Merchant ID
paytr_merchant_key: PayTR Merchant Key (AES-256 şifreli)
paytr_merchant_salt: PayTR Merchant Salt (AES-256 şifreli)
paytr_notification_url: PayTR bildirim URL
API - META (Facebook/Instagram) (category: api_meta)
meta_app_id: Facebook App ID
meta_app_secret: Facebook App Secret (AES-256 şifreli)
meta_access_token: Meta Access Token (AES-256 şifreli)
meta_page_id: Facebook Page ID
meta_ig_account_id: Instagram Business Account ID
meta_webhook_token: Webhook Verify Token
TELEFON - SIP TRUNK (category: api_phone)
sip_server: SIP sunucu
sip_port: SIP port
sip_username: SIP kullanıcı adı
sip_password: SIP şifre (AES-256 şifreli)
sip_netmask: SIP netmask
sip_outbound_protocol: Outbound protocol (udp/tcp/tls)
allow_inbound_calls: Gelen aramalara izin (0/1)
allow_outbound_calls: Giden aramalara izin (0/1)
enable_options_ping: Options ping aktif (0/1)
AI - OPENAI (Telefon) (category: ai_phone)
phone_openai_api_key: OpenAI API Key (AES-256 şifreli)
phone_openai_model: OpenAI Model (gpt-4o)
AI - AZURE SPEECH (category: ai_phone)
azure_speech_key: Azure Speech Key (AES-256 şifreli)
azure_speech_region: Azure Speech Region
azure_stt_language: STT dil kodu (tr-TR)
azure_tts_voice: TTS ses adı (tr-TR-AhmetNeural)
AI - VOICE AGENT AYARLARI (category: ai_phone)
voice_speech_rate: Ses hızı (slow/medium/fast)
voice_pitch: Ses tonu (low/medium/high)
response_delay: Yanıt gecikmesi (ms)
silence_timeout: Sessizlik süresi (ms)
max_speech_duration: Maks konuşma süresi (saniye)
voice_volume: Ses seviyesi (0-100)
enable_background_noise: Arka plan gürültüsü filtresi (0/1)
enable_echo_cancellation: Eko iptali (0/1)
WHATSAPP CLOUD API (category: api_whatsapp)
whatsapp_phone_number_id: WhatsApp Phone Number ID
whatsapp_access_token: WhatsApp Access Token (AES-256 şifreli)
whatsapp_business_id: Business Account ID
whatsapp_client_id: WhatsApp Client ID
whatsapp_client_secret: WhatsApp Client Secret (AES-256 şifreli)
whatsapp_webhook_url: Webhook URL
whatsapp_verify_token: Verify Token
AI - OPENAI (WhatsApp) (category: ai_whatsapp)
whatsapp_openai_key: OpenAI API Key (AES-256 şifreli)
whatsapp_openai_model: OpenAI Model (gpt-4o)
whatsapp_max_tokens: Maksimum token
whatsapp_temperature: Temperature (yaratıcılık: 0-2)
22. ai_instructions:
id: Otomatik artan birincil anahtar
platform: Platform (phone/whatsapp)
system_prompt: AI sistem talimatı
greeting_message: Karşılama mesajı
farewell_message: Veda mesajı
fallback_message: Anlayamadım mesajı
is_active: Aktif mi? (0/1)
created_at: Oluşturulma tarihi
updated_at: Güncellenme tarihi
23. sms_history:
id: Otomatik artan birincil anahtar
user_id: Hangi kullanıcı (foreign key → users.id)
company: Şirket adı (users.company'den otomatik çekilir)
phone: Telefon numarası
message: SMS metni
status: Durum (pending/sent/failed/delivered)
sent_at: Gönderilme zamanı
delivered_at: Teslim edilme zamanı
error_message: Hata mesajı
created_at: Kayıt tarihi
