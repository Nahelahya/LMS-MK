# 🚀 Rekomendasi Fitur LMS-MK

> Dokumen ini berisi rekomendasi fitur tambahan untuk pengembangan LMS-MK berdasarkan analisis struktur kode yang ada.

---

## ✅ Fitur yang Sudah Ada

### Autentikasi & User Management
- [x] Login/Register dengan OTP verification
- [x] Google OAuth integration
- [x] Forgot/Reset Password
- [x] Role-based access (admin, staff, student)

### Materi & Course
- [x] Upload, Edit, Delete materi (role-based)
- [x] Download materi
- [x] Course management dasar

### Student Features
- [x] Submit tugas/jawaban
- [x] Progress tracking (persentase, skor, status adaptif)
- [x] Activity logs
- [x] Dashboard student dengan daftar course

### Admin/Staff Features
- [x] Dashboard dengan statistik siswa
- [x] Monitoring siswa beresiko
- [x] Average score tracking
- [x] Chart data (dummy)

---

## 🔧 Rekomendasi Fitur Tambahan

### 👑 ADMIN (Full Access)

| Kategori | Fitur | Prioritas | Status |
|----------|-------|-----------|--------|
| **User Management** | CRUD user (create, edit, delete, suspend) | 🔴 High | ⬜ |
| | Manajemen role & permissions | 🔴 High | ⬜ |
| | Bulk import user (Excel/CSV) | 🟡 Medium | ⬜ |
| **Course Management** | CRUD course lengkap | 🔴 High | ⬜ |
| | Kategori/topic course | 🟡 Medium | ⬜ |
| | Prerequisite course | 🟢 Low | ⬜ |
| **Materi** | Organisasi materi by module/section | 🔴 High | ⬜ |
| | Video streaming support | 🟡 Medium | ⬜ |
| | Quiz/Assessment builder | 🔴 High | ⬜ |
| **Analytics** | Advanced reporting & analytics | 🟡 Medium | ⬜ |
| | Export report (PDF/Excel) | 🟡 Medium | ⬜ |
| | AI-powered insights | 🟢 Low | ⬜ |
| **System** | Pengaturan umum (logo, tema, email) | 🟡 Medium | ⬜ |
| | Backup & restore database | 🟢 Low | ⬜ |
| | Activity logs sistem | 🟡 Medium | ⬜ |

---

### 👔 STAFF (Instructor/Dosen)

| Kategori | Fitur | Prioritas | Status |
|----------|-------|-----------|--------|
| **Course** | Manage assigned courses | 🔴 High | ⬜ |
| | Create & manage modules | 🔴 High | ⬜ |
| | Publish/unpublish course | 🔴 High | ⬜ |
| **Materi** | Upload berbagai format (PDF, Video, PPT) | 🔴 High | ⬜ |
| | Organize materi by week/module | 🟡 Medium | ⬜ |
| | Add resource links (external) | 🟡 Medium | ⬜ |
| **Assessment** | Create quiz & assignments | 🔴 High | ⬜ |
| | Question bank management | 🟡 Medium | ⬜ |
| | Auto-grading untuk objective questions | 🔴 High | ⬜ |
| | Manual grading untuk essay | 🔴 High | ⬜ |
| **Student Monitoring** | View student progress per course | 🔴 High | ⬜ |
| | Submission management | 🔴 High | ⬜ |
| | Gradebook/penilaian | 🔴 High | ⬜ |
| | Feedback & comments on submissions | 🟡 Medium | ⬜ |
| **Communication** | Announcements/broadcast | 🟡 Medium | ⬜ |
| | Discussion forum per course | 🟡 Medium | ⬜ |
| | Direct messaging ke student | 🟢 Low | ⬜ |

---

### 🎓 STUDENT

| Kategori | Fitur | Prioritas | Status |
|----------|-------|-----------|--------|
| **Dashboard** | Overview semua course yang diambil | 🔴 High | ⬜ |
| | Progress bar per course | 🔴 High | ⬜ |
| | Deadline notifications | 🔴 High | ⬜ |
| | Kalender akademik | 🟡 Medium | ⬜ |
| **Course Access** | Akses materi berdasarkan module | 🔴 High | ⬜ |
| | Mark as complete/selesai | 🔴 High | ⬜ |
| | Download materi untuk offline | 🟡 Medium | ⬜ |
| **Assessment** | Take quiz & exam | 🔴 High | ⬜ |
| | Submit assignment/tugas | 🔴 High | ⬜ |
| | View grades & feedback | 🔴 High | ⬜ |
| | Retry quiz (jika diizinkan) | 🟡 Medium | ⬜ |
| **Progress** | Detailed progress report | 🔴 High | ⬜ |
| | Achievement/badges system | 🟢 Low | ⬜ |
| | Learning path visualization | 🟡 Medium | ⬜ |
| **Communication** | Discussion forum participation | 🟡 Medium | ⬜ |
| | Ask questions pada materi | 🟡 Medium | ⬜ |
| | Notifications (email/push) | 🟡 Medium | ⬜ |
| **Certificate** | Download e-certificate setelah selesai | 🟡 Medium | ⬜ |

---

## 📊 Skema Database yang Disarankan

### Tabel Tambahan

```php
// 1. Tabel Modules (untuk organisasi materi)
Schema::create('modules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description')->nullable();
    $table->integer('order')->default(0);
    $table->boolean('is_published')->default(false);
    $table->timestamps();
});

// 2. Tabel Quizzes
Schema::create('quizzes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description')->nullable();
    $table->integer('time_limit')->nullable(); // in minutes
    $table->integer('max_attempts')->default(1);
    $table->integer('passing_score')->default(60);
    $table->boolean('is_published')->default(false);
    $table->timestamps();
});

// 3. Tabel Questions
Schema::create('questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
    $table->enum('type', ['multiple_choice', 'true_false', 'essay', 'fill_blank']);
    $table->text('question');
    $table->json('options')->nullable(); // for MCQ
    $table->text('correct_answer')->nullable();
    $table->integer('points')->default(1);
    $table->integer('order')->default(0);
    $table->timestamps();
});

// 4. Tabel QuizAttempts
Schema::create('quiz_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->integer('score')->nullable();
    $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
    $table->timestamp('started_at');
    $table->timestamp('completed_at')->nullable();
    $table->integer('attempt_number')->default(1);
    $table->timestamps();
});

// 5. Tabel QuizAnswers
Schema::create('quiz_answers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
    $table->foreignId('question_id')->constrained()->onDelete('cascade');
    $table->text('answer');
    $table->boolean('is_correct')->nullable();
    $table->integer('points_earned')->default(0);
    $table->timestamps();
});

// 6. Tabel Submissions (tugas)
Schema::create('submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('materi_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('file_path')->nullable();
    $table->text('content')->nullable(); // for text submission
    $table->timestamp('submitted_at');
    $table->integer('grade')->nullable();
    $table->text('feedback')->nullable();
    $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('graded_at')->nullable();
    $table->enum('status', ['submitted', 'graded', 'late'])->default('submitted');
    $table->timestamps();
});

// 7. Tabel Announcements
Schema::create('announcements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('content');
    $table->boolean('is_pinned')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});

// 8. Tabel Discussions
Schema::create('discussions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('parent_id')->nullable()->constrained('discussions')->onDelete('cascade');
    $table->string('title')->nullable();
    $table->text('content');
    $table->boolean('is_pinned')->default(false);
    $table->timestamps();
});

// 9. Tabel Certificates
Schema::create('certificates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('course_id')->constrained()->onDelete('cascade');
    $table->string('certificate_number')->unique();
    $table->string('file_path');
    $table->timestamp('issued_at');
    $table->timestamps();
});

// 10. Tabel Notifications
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('type'); // quiz_due, assignment_graded, announcement, etc.
    $table->string('title');
    $table->text('message');
    $table->string('link')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});

// 11. Tabel Settings (konfigurasi sistem)
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type')->default('string'); // string, integer, boolean, json
    $table->string('group')->default('general'); // general, appearance, email, etc.
    $table->timestamps();
});
```

---

## 🎯 Prioritas Pengembangan

### Phase 1 (MVP - Wajib Ada)
- [ ] Quiz & Assessment System (Admin/Staff buat, Student kerjakan)
- [ ] Gradebook & Penilaian
- [ ] Organisasi Materi by Module
- [ ] Student Progress yang lebih detail

### Phase 2 (Important)
- [ ] User Management (Admin CRUD user)
- [ ] Discussion Forum
- [ ] Announcements
- [ ] Notifications

### Phase 3 (Nice to Have)
- [ ] Certificate Generator
- [ ] Advanced Analytics
- [ ] Mobile-responsive improvement
- [ ] Gamification (badges, leaderboard)

---

## 📝 Catatan Pengembangan

- **Dibuat pada:** 15 Maret 2026
- **Status:** Dokumentasi awal, belum diimplementasikan
- **Next Step:** Diskusi dengan tim untuk menentukan prioritas fitur

---

*Dokumen ini dapat diupdate sesuai perkembangan project.*
