# Product Requirements Document (PRD)

Project: TOEFL Piksi (v10)
Date: 2026-05-19
Author: Rangga

## 1. Overview
Purpose: Menyediakan platform latihan dan simulasi TOEFL berbasis web, dengan fitur exam/practice flows, AI-driven suggestions, dan panel admin untuk manajemen soal.

Scope:
- API untuk client (web/mobile) versied `/api/v1/*`.
- Web UI untuk `Student` dan `Admin` (Blade + Alpine + Tailwind).
- AI suggestion pipeline (job-based background processing).

Stakeholders:

## 2. Personas & Key User Stories
Personas:
- Student: latihan, lihat hasil, menerima rekomendasi AI
- Admin: mengelola soal, melihat statistik
- Integrator: pihak ketiga yang butuh API

Top User Stories:
- Sebagai Student saya bisa memulai latihan, mengirim jawaban, dan melihat review plus rekomendasi AI.
- Sebagai Student saya bisa memulai ujian simulasi berdurasi 120 menit dan sistem menyimpan sesi jika disconnect.
- Sebagai Admin saya bisa CRUD soal, impor/ekspor, dan memulai ujian kelas.
- Sebagai Integrator saya bisa mengakses API versi v1 dengan token Bearer.

## 3. Functional Requirements
Authentication & Authorization
- Role-based auth: `Admin`, `Student`.
- API token (Bearer) untuk integrator and API testing.

Exam Flows
- Admin: create exam settings (`ExamSetting`) dan start exam.
- Student: create `ExamSession`, resume, submit, or abandon.
- Server: scoring via `ScoringService` and persist `Result` model.

Practice Flows
- Student dapat memulai practice, jawab, dan submit; service `PracticeFlowService` menghasilkan `PracticeResult` dan memicu `GenerateAiSuggestionJob` untuk rekomendasi.

Question Management (Admin)
- CRUD endpoint & blade UI for question content, categories.
- Validasi kategori (listening, structure, reading) — implemented in `Admin\QuestionController`.

AI Suggestions
- Background job `GenerateAiSuggestionJob` enqueues model processing.
- Services: `TrendDataBuilder`, `AnalysisMetaBuilder`, `ResponseFormatter`.
- Endpoint `/api/v1/student/suggestion/dashboard` returns tolerant payload even if AI parse missing.

Dashboard & Leaderboard
- `StudentStatsAggregator` builds per-student dashboard.
- `LeaderboardAggregator` caches top students with TTL.

API
- Versioning under `/api/v1/*`.
- Standard response format: JSON {"data":..., "message":..., "errors":...}.
- Error handling consistent across controllers.

## 4. Non-Functional Requirements
Performance
- Target: 95th percentile latency < 500ms for read endpoints (tune later).
Scalability
- AI processing via queue workers; horizontal scale workers as load increases.
Reliability
- Aim 99.9% uptime for core API; graceful degradation for AI suggestions.
Security
- HTTPS required in production; sanitize inputs; rate limiting on public endpoints.
Maintainability
- Config-driven exam rules (`config/exam.php`), smaller services (builders/aggregators), tests coverage prerequisite.

## 5. Data Model & Schemas (summary)
Primary models: `User`, `Question`, `PracticeResult`, `Result`, `ExamSession`, `ExamSetting`.
Include representative JSON examples in appendices (see `File_Structure.md` and generated OpenAPI).

## 6. API Documentation & Developer Experience
- Scribe-generated docs available at `/docs` (HTML) and OpenAPI in `storage/app/scribe/openapi.yaml`.
- Postman collection at `storage/app/scribe/collection.json`.
- Setup and requirements in `docs/requirements.md`.

## 7. Acceptance Criteria
- All existing PHPUnit tests pass (currently 57).
- Practice submit flow creates `PracticeResult` and returns 200.
- Dashboard suggestion endpoint returns 200 for student and tolerates missing parsed AI payloads.
- Admin question CRUD operations function and category validation enforced.
- Scribe docs build without errors and include auth examples.

## 8. Success Metrics
- DAU (Daily Active Students) growth: target +10% / quarter after launch.
- Error rate: <1% of API calls per week.
- Latency: 95th percentile under target defined above.
- AI suggestion usefulness: collect feedback/rating > 3.5/5.

## 9. Milestones & Roadmap
M1 — Stabilize (2 weeks)
- Ensure tests green, fix critical bugs, finalize `config/exam.php` settings.
M2 — API docs & Auth (1 week)
- Scribe docs in repo, example tokens, Postman collection.
M3 — Service refactor & tests (3 weeks)
- Finish decomposition, add integration tests for AI pipeline.
M4 — Release & monitor (2 weeks)
- Public/internal release, set up monitoring, collect metrics.

## 10. Risks & Mitigations
- AI pipeline fails or slow — mitigation: use circuit breaker, fallback to last good suggestion or empty suggestion with graceful message.
- DB migrations fail — mitigation: run migrations in a maintenance window, backup DB.
- Token leakage — mitigation: rotate tokens, store encrypted, restrict scopes.

## 11. Operational & Deployment
- Env vars: list core envs in `docs/requirements.md`.
- Backup strategy: daily DB dump, hourly transaction logs (if supported).
- Rollback: keep previous release artifact and DB migration rollback scripts.
- Monitoring: application logs, Sentry for errors, optionally Prometheus + Grafana for metrics.

## 12. Appendices & Links
- Architecture & file map: `File_Structure.md` (root)
- Exam config: `config/exam.php`
- Generated API docs: `/docs` and `storage/app/scribe/openapi.yaml`
- Tests: `php artisan test` (57 tests expected)

---