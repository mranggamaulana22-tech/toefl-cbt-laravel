# Student Halaman Audit Report
**Tanggal:** 9 April 2026  
**Scope:** Controllers, Models, Routes, Views, Frontend JS

---

## 🔴 CRITICAL ISSUES (High Priority)

### 1. Missing Role-Based Middleware on Student Routes
**Severity:** HIGH  
**File:** `routes/web.php`  
**Issue:**
- Student routes (exam.*, practice.*, student.results.*) hanya punya middleware `auth`, tidak ada enforcing role `student`.
- Admin bisa akses semua student routes dan mengerjakan ujian/latihan dengan identitas mereka.
- Tidak ada middleware seperti `EnsureStudent` (ada `EnsureAdmin` untuk admin).

**Impact:** 
- Admin bisa submit ujian dan nilai mereka masked sebagai student hasil.
- Potensi data pollution di leaderboard/gradebook.

**Recommendation:**
```php
// app/Http/Middleware/EnsureStudent.php
public function handle(Request $request, Closure $next): Response
{
    if (!$request->user() || $request->user()->role !== 'student') {
        abort(403);
    }
    return $next($request);
}

// routes/web.php - tambahkan middleware
Route::middleware(['auth', 'verified', 'student'])->group(function () {
    Route::get('/exam/start', ...);
    Route::get('/exam/test', ...);
    // ... semua student routes
});
```

---

### 2. Weak User Answer Validation
**Severity:** HIGH  
**File:** `ExamController@submit()`, `ExamController@practiceSubmit()`  
**Issue:**
- Tidak ada validation pada `$userAnswers = $request->input('answers', [])`.
- Client side bisa submit answers dengan index invalid atau value tidak sesuai dengan question list.
- Comparison menggunakan `==` bukan `===`:
  ```php
  if (isset($userAnswers[$index]) && $userAnswers[$index] == $q->correct_answer) {
  ```
  Ini rentan type juggling (string "1" === int 1 bisa kabur).

**Impact:**
- Arbitrary answers bisa diterima, skor tidak akurat.
- Student bisa manipulasi index/jawaban via developer console.

**Recommendation:**
```php
// Tambahkan FormRequest validation
// app/Http/Requests/SubmitExamRequest.php
public function rules()
{
    return [
        'answers' => 'required|array',
        'answers.*' => 'required|in:A,B,C,D',
    ];
}

// Di controller, validasi ulang question set
foreach ($questionIds as $index) {
    if (!isset($userAnswers[$index]) || !in_array($userAnswers[$index], ['A', 'B', 'C', 'D'], true)) {
        return redirect()->route('exam.start')->with('error', 'Jawaban tidak valid.');
    }
}
```

---

### 3. Potential Race Condition on Exam Submit
**Severity:** HIGH  
**File:** `ExamController@submit()`  
**Issue:**
- Check `if ($existingResult->submitted_at)` lalu update record tanpa transaction/lock.
- Dua request submit bisa lolos check dan both update `submitted_at`.
- Tidak ada database-level constraint untuk prevent double submit.

**Impact:**
- Duplikasi submission data.
- Skor bisa overwritten.

**Recommendation:**
```php
// Gunakan transaction + pessimistic lock
DB::transaction(function () use ($existingResult, ...) {
    $locked = Result::lockForUpdate()->find($existingResult->id);
    
    if ($locked->submitted_at) {
        throw new Exception('Sudah di-submit');
    }
    
    $locked->update([...]);
});

// Atau tambahkan unique constraint di migrations
$table->unique(['user_id', 'exam_cycle', 'submitted_at']);
```

---

### 4. Type Casting Issue in Score Calculation
**Severity:** MEDIUM-HIGH  
**File:** `ExamController@submit()`, `ExamController@practiceSubmit()`  
**Issue:**
```php
// Line di practiceSubmit:
$category = strtolower((string) $q->category);
if (array_key_exists($category, $correct)) {
    $correct[$category]++;
}
```
- `$q->category` bisa nullable atau unexpected value.
- `strtolower()` casting but no validation bahwa hasilnya valid key di `$correct`.
- Di `submit()`, tidak ada casting, langsung: `$correct[$q->category]++` - bisa NotFound error jika category tidak expect.

**Impact:**
- Potential undefined array key error.
- Skor mungkin miscounted jika category tidak match.

**Recommendation:**
```php
$validCategories = ['listening', 'structure', 'reading'];
foreach ($questions as $index => $q) {
    $category = strtolower((string) ($q->category ?? ''));
    
    if (!in_array($category, $validCategories, true)) {
        continue; // Skip atau log error
    }
    
    if (isset($userAnswers[$index]) && $userAnswers[$index] === $q->correct_answer) {
        $correct[$category]++;
    }
}
```

---

## 🟡 MEDIUM PRIORITY ISSUES

### 5. No FormRequest Validation Layer
**Severity:** MEDIUM  
**File:** `ExamController@submit()`, `ExamController@practiceSubmit()`  
**Issue:**
- Direktly menggunakan `$request->input()` tanpa FormRequest class.
- Tidak ada centralized validation rules.
- CSRF token di-verify otomatis oleh middleware, tapi tidak ada explicit validation.

**Recommendation:**
```php
// app/Http/Requests/SubmitExamRequest.php
class SubmitExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth middleware sudah check
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array',
            'answers.*' => 'required|string|in:A,B,C,D',
        ];
    }
}

// Di controller
public function submit(SubmitExamRequest $request): ...
{
    $userAnswers = $request->validated()['answers'];
    ...
}
```

---

### 6. Client-Side localStorage Vulnerable to Tampering
**Severity:** MEDIUM  
**File:** `resources/views/student/exam/test.blade.php`, `practice/test.blade.php`  
**Issue:**
```javascript
const saved = localStorage.getItem(this.progressKey);
// ... langsung parse tanpa validation
this.answers = parsed.answers || {};
this.timeLeft = Number.isInteger(parsed.timeLeft) ? parsed.timeLeft : this.timeLeft;
```
- Student bisa buka developer console, ubah `timeLeft` ke nilai besar.
- Timer bisa di-spoof.
- Answers bisa di-inject.

**Impact:**
- Unfair advantage (artificial time extension).
- Bukan security breach tapi integrity issue.

**Note:**
- Server-side timer track via `started_at` - jadi final backend submission aman.
- Tapi user experience jadi ambiguous jika localStorage di-manipulate.

**Recommendation:**
```javascript
// Tambahkan server-side time validation di submit
// Backend: banding waktu submit vs started_at
$elapsedSeconds = now()->diffInSeconds($existingResult->started_at);
if ($elapsedSeconds > 7200) { // 120 * 60 + buffer
    // Reject atau warn
}
```

---

### 7. Inefficient Question Generation Queries
**Severity:** MEDIUM  
**File:** `ExamController@generateOrderedQuestionIds()`  
**Issue:**
```php
foreach (self::SECTION_ORDER as $section) {
    $target = self::SECTION_TARGETS[$section] ?? 0;
    $sectionIds = Question::where('category', $section)
        ->inRandomOrder()
        ->limit($target)
        ->pluck('id')
        ->all();
    $selectedIds = array_merge($selectedIds, $sectionIds);
}

// Then loop again for remaining
foreach (self::SECTION_ORDER as $section) {
    if ($remaining <= 0) break;
    // ... another query
}
```
- Multiple queries per question generation (6-7 queries).
- `inRandomOrder()` tanpa seed bisa inconsistent.
- `array_merge()` dalam loop bisa slow untuk large arrays.

**Impact:**
- Slower initial load (tapi tidak critical karena cached di session).

**Recommendation:**
```php
// Single query approach
$allQuestions = Question::whereIn('category', self::SECTION_ORDER)
    ->get()
    ->groupBy('category');

$selectedIds = [];
foreach (self::SECTION_ORDER as $section) {
    $target = self::SECTION_TARGETS[$section] ?? 0;
    $ids = $allQuestions[$section]->random(min($target, $allQuestions[$section]->count()))
        ->pluck('id')
        ->all();
    $selectedIds = [...$selectedIds, ...$ids];
}
```

---

### 8. No Result Authorization Check di ExamController Methods
**Severity:** MEDIUM  
**File:** `ExamController@start()`, `ExamController@test()`  
**Issue:**
- `start()` dan `test()` hanya check session/ExamSetting, tidak validate user ownership di Result.
- Jika session corrupted atau user visit URL langsung, tidak ada explicit check.
- `ResultController@certificate()` punya check `if ($result->user_id !== auth()->id())`, tapi exam/practice routes tidak.

**Impact:**
- Inconsistent authorization pattern.
- Potential info leak jika session bypass.

**Recommendation:**
```php
// Di test() method
if (!$setting->is_open) { ... }

// Add explicit ownership check jika Result sudah ada
if ($existingResult && $existingResult->user_id !== auth()->id()) {
    abort(Response::HTTP_FORBIDDEN);
}
```

---

## 🟢 LOW PRIORITY / CODE QUALITY

### 9. Code Duplication Between Exam & Practice Controllers
**Severity:** LOW  
**File:** `ExamController` (mix exam + practice methods)  
**Issue:**
- `submit()` dan `practiceSubmit()` 90% identical code.
- Question generation logic duplicated.
- Score calculation duplicated.

**Impact:**
- Hard to maintain.
- Bug fix di satu tempat lupa di tempat lain.

**Recommendation:**
```php
// Create PracticeController atau extract traits
// app/Http/Controllers/Student/PracticeController.php
class PracticeController extends Controller { ... }

// Or use trait untuk shared logic
trait CalculatesScore { ... }
```

---

### 10. Missing Type Hints & Inconsistent Naming
**Severity:** LOW  
**File:** Various  
**Issue:**
- Route names inconsistent: `exam.start`, `exam.test` vs `practice.start`, `practice.test`.
  - URLs: `/exam/start`, `/exam/test` vs `/practice/start`, `/practice/test`.
- Controller methods tidak punya return type hint di praktik result view (hanya `View|RedirectResponse`).

**Impact:**
- IDE autocomplete tidak akurat.
- Code harder to read.

**Recommendation:**
```php
// Add type hints
public function submit(Request $request): View|RedirectResponse { ... }

// Standardize naming
Route::get('/exam/start', ...)->name('exam.start');
Route::get('/exam/test', ...)->name('exam.test');
Route::get('/practice/start', ...)->name('practice.start');
Route::get('/practice/test', ...)->name('practice.test');
```

---

### 11. Missing Database Constraints
**Severity:** LOW  
**File:** Migrations  
**Issue:**
- Result migration tidak punya constraint untuk prevent:
  - Multiple submitted_at untuk same user+cycle.
  - Negative scores.
  - Missing user_id (FK).

**Recommendation:**
```php
// In Result migration
$table->unique(['user_id', 'exam_cycle', function (Fluent $index) {
    $index->where('submitted_at', '!=', null);
}]);
$table->unsignedInteger('score_total')->nullable()->min(0)->max(120);
$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
```

---

### 12. XSS Risk in Certificate View (Low Risk, but check)
**Severity:** LOW  
**File:** `resources/views/student/results/certificate.blade.php`  
**Issue:**
```blade
<p class="mt-2 text-sm text-slate-500">NPM: {{ auth()->user()->npm ?? '-' }}</p>
```
- `npm` field tidak di-escape, bisa HTML/JS injection jika user data tidak sanitize di input.
- Tapi user data harusnya dari form validation di profile.edit, assumed safe.

**Status:** Low risk karena user data di-sanitize di ProfileController update.

---

## 📊 Severity Summary

| Level | Count | Items |
|-------|-------|-------|
| 🔴 Critical | 4 | 1. Missing role middleware, 2. Weak answer validation, 3. Race condition, 4. Type casting |
| 🟡 Medium | 4 | 5. No FormRequest, 6. localStorage tampering, 7. Query inefficiency, 8. Missing auth check |
| 🟢 Low | 4 | 9. Code duplication, 10. Missing type hints, 11. Missing DB constraints, 12. XSS (low risk) |

---

## 🎯 Priority Fix Order

1. **Immediately (Before Production):**
   - Add EnsureStudent middleware to all student routes
   - Add answer validation & use strict comparison (===)
   - Add transaction/lock untuk prevent race condition

2. **Within Sprint:**
   - Create FormRequest validation classes
   - Add authorization checks di exam/practice start/test methods
   - Fix type casting di score calculation

3. **Next Release:**
   - Refactor duplicate code (extract PracticeController / traits)
   - Add missing type hints
   - Add database constraints

---

## Views & Frontend Assessment

### Positive:
✅ Blade templates properly escaped ({{ }} auto-escape)  
✅ localStorage used with try-catch error handling  
✅ Timer implementation robust with auto-submit fallback  
✅ Proper use of Alpine.js directives  
✅ Language consistency fixed (all Indonesian)  

### Needs Attention:
⚠️ No offline fallback jika localStorage/session lost  
⚠️ Timer sync bisa drift jika page tabbed away  

---

## Overall Assessment

**Grade: B- (Good with Critical Issues)**

- **Pros:** Well-structured controllers, proper routing, good form handling, decent security baseline
- **Cons:** Missing role enforcement, weak input validation, potential race conditions
- **Maintainability:** Fair (code duplication, inconsistent patterns)
- **Scalability:** Fair (query optimization needed, no caching)

**Recommendation:** Fix critical middleware + validation issues ASAP before full student rollout.
