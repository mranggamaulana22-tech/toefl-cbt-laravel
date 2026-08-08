# Project File Structure

.
├── .editorconfig
├── .env
├── .env.example
├── .eslintignore
├── .eslintrc.cjs
├── .gitattributes
├── .github/
├── .gitignore
├── .phpunit.result.cache
├── .prettierignore
├── .prettierrc.json
├── artisan
├── blade_unused_report.csv
├── clear
├── composer.json
├── composer.lock
├── File_Structure.md
├── grep
├── lighthouse-report-v2.json
├── lighthouse-report.json
├── package-lock.json
├── package.json
├── phpunit.xml
├── postcss.config.cjs
├── PRD.md
├── README.md
├── STUDENT_AUDIT_REPORT.md
├── stylelint.config.cjs
├── tailwind.config.js
├── test_query.php
├── tools/
│   └── scan_blade_usage.ps1
├── vite.config.js
├── app/
│   ├── Console/ (empty)
│   ├── Enums/
│   │   ├── AiStatus.php
│   │   ├── QuestionCategory.php
│   │   └── UserRole.php
│   ├── Exceptions/ (empty)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ExamControlController.php
│   │   │   │   ├── GradebookController.php
│   │   │   │   ├── PracticeHistoryController.php
│   │   │   │   ├── PracticeQuestionController.php
│   │   │   │   ├── QuestionController.php
│   │   │   │   └── StudentController.php
│   │   │   ├── Auth/
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── ConfirmablePasswordController.php
│   │   │   │   ├── EmailVerificationNotificationController.php
│   │   │   │   ├── EmailVerificationPromptController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   └── VerifyEmailController.php
│   │   │   ├── Student/
│   │   │   │   ├── AiSuggestionController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── PracticeController.php
│   │   │   │   ├── PracticeProgressController.php
│   │   │   │   ├── PracticeReviewController.php
│   │   │   │   └── ResultController.php
│   │   │   ├── Controller.php
│   │   │   ├── DashboardController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── EncryptCookies.php
│   │   │   ├── EnsureAdmin.php
│   │   │   ├── EnsureStudent.php
│   │   │   ├── PreventRequestsDuringMaintenance.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── TrimStrings.php
│   │   │   ├── TrustHosts.php
│   │   │   ├── TrustProxies.php
│   │   │   ├── UpdateStreakMiddleware.php
│   │   │   ├── ValidateSignature.php
│   │   │   └── VerifyCsrfToken.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   └── LoginRequest.php
│   │       ├── ProfileUpdateRequest.php
│   │       ├── StoreQuestionRequest.php
│   │       ├── SubmitExamRequest.php
│   │       ├── SubmitPracticeRequest.php
│   │       └── UpdateQuestionRequest.php
│   ├── Jobs/
│   │   └── GenerateAiSuggestionJob.php
│   ├── Models/
│   │   ├── ExamSession.php
│   │   ├── ExamSetting.php
│   │   ├── PracticeProgress.php
│   │   ├── PracticeQuestion.php
│   │   ├── PracticeQuestionReview.php
│   │   ├── PracticeResult.php
│   │   ├── PracticeResultItem.php
│   │   ├── PracticeReviewUsage.php
│   │   ├── Question.php
│   │   ├── Result.php
│   │   └── User.php
│   ├── Policies/
│   │   ├── PracticeResultPolicy.php
│   │   └── ResultPolicy.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   ├── Repositories/
│   │   ├── QuestionRepository.php
│   │   └── QuestionRepositoryInterface.php
│   ├── Services/
│   │   ├── AiSuggestionParser.php
│   │   ├── AiSuggestionService.php
│   │   ├── AnalysisMetaBuilder.php
│   │   ├── BaseOpenRouterService.php
│   │   ├── CertificateService.php
│   │   ├── DashboardService.php
│   │   ├── ExamControlService.php
│   │   ├── ExamFlowService.php
│   │   ├── LeaderboardAggregator.php
│   │   ├── OpenRouterService.php
│   │   ├── PracticeFlowService.php
│   │   ├── PracticeProgressService.php
│   │   ├── PracticeReviewFlowService.php
│   │   ├── PracticeReviewService.php
│   │   ├── ProfilePhotoService.php
│   │   ├── QuestionExportService.php
│   │   ├── QuestionSelectionService.php
│   │   ├── ResponseFormatter.php
│   │   ├── ScoringService.php
│   │   ├── StudentDirectoryService.php
│   │   ├── StudentResultService.php
│   │   ├── StudentStatsAggregator.php
│   │   └── TrendDataBuilder.php
│   └── View/
│       ├── Components/ (empty)
│       ├── AppLayout.php
│       └── GuestLayout.php
├── bootstrap/
│   ├── cache/
│   │   ├── packages.php
│   │   └── services.php
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── broadcasting.php
│   ├── cache.php
│   ├── cors.php
│   ├── database.php
│   ├── exam.php
│   ├── filesystems.php
│   ├── hashing.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── scribe.php
│   ├── services.php
│   ├── session.php
│   └── view.php
├── database/
│   ├── .gitignore
│   ├── data/
│   │   ├── Listening.csv
│   │   ├── Reading.csv
│   │   └── Structure.csv
│   ├── factories/
│   │   ├── ExamSettingFactory.php
│   │   ├── QuestionFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_reset_tokens_table.php
│   │   ├── 2019_08_19_000000_create_failed_jobs_table.php
│   │   ├── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   ├── 2026_04_03_000000_create_questions_table.php
│   │   ├── 2026_04_03_014650_create_results_table.php
│   │   ├── 2026_04_04_000100_create_exam_settings_table.php
│   │   ├── 2026_04_04_000110_add_exam_cycle_to_results_table.php
│   │   ├── 2026_04_04_000120_add_tracking_timestamps_to_results.php
│   │   ├── 2026_04_09_044335_create_practice_questions_table.php
│   │   ├── 2026_04_09_050735_create_practice_results_table.php
│   │   ├── 2026_04_10_000000_add_ai_suggestion_to_results_tables.php
│   │   ├── 2026_04_12_000001_add_ai_model_used_to_results_tables.php
│   │   ├── 2026_04_12_052457_create_jobs_table.php
│   │   ├── 2026_04_12_120000_create_practice_progresses_table.php
│   │   ├── 2026_04_12_130000_add_ai_generation_status_to_results_tables.php
│   │   ├── 2026_04_12_140000_add_ai_parsed_fields_to_results_tables.php
│   │   ├── 2026_04_12_151000_add_rank_query_index_to_results_table.php
│   │   ├── 2026_04_12_180000_create_practice_review_tables.php
│   │   ├── 2026_04_12_190000_add_audio_transcript_to_questions_tables.php
│   │   ├── 2026_04_15_000100_add_profile_photo_path_to_users_table.php
│   │   ├── 2026_04_24_223754_add_streak_system_to_users_table.php
│   │   ├── 2026_05_02_000001_remove_duplicate_data_from_practice_result_items.php
│   │   └── 2026_05_02_000002_create_exam_sessions_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── PracticeQuestionSeeder.php
│   │   ├── QuestionSeeder.php
│   │   └── UserSeeder.php
├── docs/
│   ├── Activity_Diagram.md
│   ├── Class_Diagram.md
│   ├── ERD.md
│   ├── PRD.md
│   ├── requirements.md
│   ├── usecase.md
│   └── usecase.puml
├── node_modules/
├── public/
│   ├── .htaccess
│   ├── animations/
│   │   └── fire.json
│   ├── build/
│   │   ├── assets/
│   │   │   ├── app-6c3478b3.js
│   │   │   └── app-e20d259b.css
│   │   └── manifest.json
│   ├── favicon.ico
│   ├── hot
│   ├── images/
│   │   ├── classroom_dark.webp
│   │   ├── classroom_light.webp
│   │   ├── gedung.webp
│   │   ├── logo.webp
│   │   └── mascot.webp
│   ├── index.php
│   ├── robots.txt
│   └── storage/
│       ├── .gitignore
│       ├── profile-photos/
│       │   ├── 5801b9f7-2882-4579-ab1c-985152e77c95.png
│       │   ├── 85902451-5a05-4f5e-a6c9-2d6acff71a5d.png
│       │   ├── 94b53403-f7f4-4580-9301-ecbcb6e9a035.png
│       │   └── probe.txt
│       └── questions/
│           └── audio/
│               └── HuIIqmKb7AVyZQ9hpwu5IF2wmJaLi1ojK3o45ZVb.mp3
├── resources/
│   ├── ARCHITECTURE.md
│   ├── css/
│   │   ├── app.css
│   │   ├── theme-background.css
│   │   ├── base/
│   │   │   └── tokens.css
│   │   ├── components/
│   │   │   ├── backgrounds.css
│   │   │   ├── surfaces.css
│   │   │   └── utilities.css
│   │   ├── pages/
│   │   │   └── student/
│   │   │       ├── ai-analysis.css
│   │   │       ├── certificate.css
│   │   │       ├── profile.css
│   │   │       ├── results-history.css
│   │   │       ├── results-index.css
│   │   │       ├── review-index.css
│   │   │       └── review-show.css
│   │   └── themes/
│   │       ├── admin-shell.css
│   │       └── student-shell.css
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js
│   │   ├── components/
│   │   ├── modules/
│   │   │   ├── crud-modal.js
│   │   │   ├── exam-session.js
│   │   │   ├── practice-session.js
│   │   │   ├── student-theme.js
│   │   │   ├── test-session-base.js
│   │   │   └── theme-storage.js
│   │   ├── services/
│   │   └── stores/
│   │       ├── app.js
│   │       └── bootstrap.js
│   ├── prompts/
│   │   └── openrouter/
│   │       ├── ai_analysis_v1.txt
│   │       └── practice_review_v1.txt
│   └── views/
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── gradebook/
│       │   │   ├── index.blade.php
│       │   │   └── partials/
│       │   │       └── results.blade.php
│       │   ├── practice-history/
│       │   │   └── index.blade.php
│       │   ├── practice-questions/
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   ├── index.blade.php
│       │   │   ├── show.blade.php
│       │   │   └── partials/
│       │   │       ├── edit-form.blade.php
│       │   │       ├── row.blade.php
│       │   │       └── show-detail.blade.php
│       │   ├── questions/
│       │   │   ├── create.blade.php
│       │   │   ├── edit.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── partials/
│       │   │       ├── edit-form.blade.php
│       │   │       ├── row.blade.php
│       │   │       └── show-detail.blade.php
│       │   └── students/
│       │       ├── index.blade.php
│       │       └── partials/
│       │           └── results.blade.php
│       ├── auth/
│       │   ├── confirm-password.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── reset-password.blade.php
│       │   └── verify-email.blade.php
│       ├── components/
│       │   ├── application-logo.blade.php
│       │   ├── auth-session-status.blade.php
│       │   ├── category-badge.blade.php
│       │   ├── confirm-modal.blade.php
│       │   ├── danger-button.blade.php
│       │   ├── dropdown-link.blade.php
│       │   ├── dropdown.blade.php
│       │   ├── filter-skeleton.blade.php
│       │   ├── form-actions.blade.php
│       │   ├── form-field.blade.php
│       │   ├── form-skeleton.blade.php
│       │   ├── info-card.blade.php
│       │   ├── input-error.blade.php
│       │   ├── input-label.blade.php
│       │   ├── modal.blade.php
│       │   ├── nav-link.blade.php
│       │   ├── page-banner.blade.php
│       │   ├── primary-button.blade.php
│       │   ├── quick-link.blade.php
│       │   ├── responsive-nav-link.blade.php
│       │   ├── row-actions.blade.php
│       │   ├── secondary-button.blade.php
│       │   ├── skeleton.blade.php
│       │   ├── stat-card-icon.blade.php
│       │   ├── stat-card.blade.php
│       │   └── text-input.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── guest.blade.php
│       │   └── navigation.blade.php
│       ├── profile/
│       │   ├── edit.blade.php
│       │   └── partials/
│       │       ├── delete-user-form.blade.php
│       │       ├── update-password-form.blade.php
│       │       └── update-profile-information-form.blade.php
│       ├── scribe/
│       │   └── index.blade.php
│       ├── student/
│       │   ├── ai-analysis/
│       │   │   ├── index.blade.php
│       │   │   └── partials/
│       │   │       ├── content.blade.php
│       │   │       ├── navigation.blade.php
│       │   │       ├── scripts.blade.php
│       │   │       ├── skeleton.blade.php
│       │   │       └── styles.blade.php
│       │   ├── dashboard.blade.php
│       │   ├── dashboard/
│       │   │   └── partials/
│       │   │       ├── dashboard-bg.blade.php
│       │   │       ├── dashboard-hero.blade.php
│       │   │       ├── dashboard-modals.blade.php
│       │   │       ├── dashboard-stats.blade.php
│       │   │       └── dashboard-styles.blade.php
│       │   ├── exam/
│       │   │   ├── partials/
│       │   │   │   ├── exam-content.blade.php
│       │   │   │   ├── exam-header.blade.php
│       │   │   │   ├── exam-modals.blade.php
│       │   │   │   ├── exam-scripts.blade.php
│       │   │   │   ├── exam-sidebar.blade.php
│       │   │   │   ├── exam-styles.blade.php
│       │   │   │   ├── result-skeleton.blade.php
│       │   │   │   └── start-skeleton.blade.php
│       │   │   ├── result.blade.php
│       │   │   ├── start.blade.php
│       │   │   └── test.blade.php
│       │   ├── leaderboard/
│       │   │   ├── index.blade.php
│       │   │   └── partials/
│       │   │       ├── leaderboard-bg.blade.php
│       │   │       ├── leaderboard-hero.blade.php
│       │   │       ├── leaderboard-list.blade.php
│       │   │       └── leaderboard-styles.blade.php
│       │   ├── practice/
│       │   │   ├── partials/
│       │   │   │   ├── practice-content.blade.php
│       │   │   │   ├── practice-header.blade.php
│       │   │   │   ├── practice-modals.blade.php
│       │   │   │   ├── practice-scripts.blade.php
│       │   │   │   ├── practice-sidebar.blade.php
│       │   │   │   ├── practice-styles.blade.php
│       │   │   │   ├── result-skeleton.blade.php
│       │   │   │   └── start-skeleton.blade.php
│       │   │   ├── result.blade.php
│       │   │   ├── start.blade.php
│       │   │   └── test.blade.php
│       │   ├── results/
│       │   │   ├── certificate-pdf.blade.php
│       │   │   ├── certificate.blade.php
│       │   │   ├── exam-history.blade.php
│       │   │   ├── index.blade.php
│       │   │   └── practice-history.blade.php
│       │   └── review/
│       │       ├── index.blade.php
│       │       ├── partials/
│       │       │   ├── index-scripts.blade.php
│       │       │   ├── index-styles.blade.php
│       │       │   ├── review-bg.blade.php
│       │       │   ├── show-scripts.blade.php
│       │       │   ├── show-skeleton.blade.php
│       │       │   └── show-styles.blade.php
│       │       └── show.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── auth.php
│   ├── channels.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── Api/
│   │   │   └── ApiRoutesTest.php
│   │   ├── Auth/
│   │   │   ├── AuthenticationTest.php
│   │   │   ├── EmailVerificationTest.php
│   │   │   ├── PasswordConfirmationTest.php
│   │   │   ├── PasswordResetTest.php
│   │   │   ├── PasswordUpdateTest.php
│   │   │   └── RegistrationTest.php
│   │   ├── ExamSessionTest.php
│   │   ├── ExampleTest.php
│   │   ├── ProfileTest.php
│   │   └── QuestionRepositoryTest.php
│   ├── Unit/
│   │   ├── EnumsTest.php
│   │   └── ExampleTest.php
│   ├── CreatesApplication.php
│   └── TestCase.php
└── vendor/