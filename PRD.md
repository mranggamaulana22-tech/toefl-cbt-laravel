# Product Requirements Document (PRD)

## 1. Ringkasan Aplikasi
Aplikasi ini adalah platform berbasis web untuk manajemen ujian, latihan, dan analisis hasil TOEFL bagi mahasiswa dan admin. Dibangun menggunakan Laravel, aplikasi ini menyediakan fitur administrasi soal, pelaksanaan ujian, latihan, serta analisis hasil berbasis AI.

## 2. Aktor Utama
- **Admin**: Mengelola soal, ujian, data mahasiswa, dan melihat statistik.
- **Mahasiswa/Student**: Mengikuti ujian, latihan, dan melihat hasil serta analisis AI.

## 3. Fitur Utama
### Untuk Admin
- Manajemen soal ujian dan latihan (CRUD, ekspor CSV)
- Kontrol sesi ujian (mulai/tutup)
- Melihat dan ekspor gradebook
- Melihat riwayat latihan mahasiswa
- Manajemen data mahasiswa
- Statistik dashboard (total mahasiswa, soal, ujian)

### Untuk Mahasiswa
- Mengikuti ujian dan latihan
- Melihat hasil ujian dan latihan
- Mendapatkan analisis dan saran AI
- Dashboard personal (statistik, hasil terbaru, skor terbaik)

## 4. Struktur Model & Database
- **User**: name, email, password, npm, class, role, profile_photo_path
- **Question**: Soal ujian/praktik
- **Result**: Hasil ujian
- **PracticeResult**: Hasil latihan
- **ExamSetting**: Pengaturan ujian aktif
- **PracticeQuestion, PracticeProgress, PracticeReviewUsage, PracticeResultItem**: Terkait fitur latihan

## 5. Struktur Controller
- **Admin**: ExamControlController, GradebookController, PracticeHistoryController, PracticeQuestionController, QuestionController, StudentController
- **Student**: ExamController, ResultController, AiSuggestionController, PracticeReviewController
- **Auth**: AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, PasswordController, PasswordResetLinkController, RegisteredUserController, VerifyEmailController
- **DashboardController, ProfileController**

## 6. Service, Policy, Job
- **Services**: AiSuggestionParser, OpenRouterService, PracticeReviewService
- **Policies**: PracticeResultPolicy, ResultPolicy
- **Job**: GenerateAiSuggestionJob

## 7. Routing (Contoh)
- `/dashboard` (DashboardController@index, auth, verified)
- `/admin/exam/start-session` (ExamControlController@startSession, admin)
- `/admin/gradebook` (GradebookController@index, admin)
- `/students` (StudentController@index, admin)
- `/exam` (ExamController, student)
- `/results` (ResultController, student)
- `/ai-suggestion` (AiSuggestionController, student)

## 8. Blade & Frontend
- Blade views untuk admin, student, auth, komponen UI (button, modal, dsb)
- Dashboard, hasil ujian, latihan, analisis AI

## 9. Kebutuhan Bisnis
- Memudahkan administrasi dan pelaksanaan ujian TOEFL
- Memberikan insight berbasis AI untuk hasil ujian/latihan
- Otomatisasi ekspor data dan analisis
- Akses terpisah untuk admin dan mahasiswa

## 10. Integrasi & Teknologi
- Laravel, Tailwind CSS, Vite
- AI Suggestion (OpenRouterService)
- Ekspor data (CSV)

---

_Dokumen ini dihasilkan otomatis dari struktur dan kode proyek. Silakan lengkapi dengan kebutuhan bisnis dan detail lain sesuai kebutuhan._
