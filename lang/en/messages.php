<?php

return [

    // =========================================================
    // SIDEBAR & GLOBAL NAVIGATION
    // =========================================================
    'dashboard'          => 'Dashboard',
    'materi'             => 'Learning Materials',
    'kelas'              => 'My Classes',
    'progres'            => 'Learning Progress',
    'presensi'           => 'Attendance',
    'manajemen'          => 'Management',
    'siswa'              => 'Students Data',
    'settings'           => 'Settings',
    'light_mode'         => 'Light Mode',
    'dark_mode'          => 'Dark Mode',
    'mode_gelap'         => 'Dark Mode',   // alias – fix bug 'messages.mode_gelap'
    'mode_terang'        => 'Light Mode',  // alias – fix bug 'messages.mode_terang'
    'keluar'             => 'Logout',

    // =========================================================
    // SETTINGS / PROFILE PAGE
    // =========================================================
    'settings_desc'        => 'Manage your profile, security, and account preferences.',
    'profile'              => 'Profile',
    'profile_desc'         => 'Your name, email, and profile photo.',
    'profile_photo'        => 'Profile Photo',
    'choose_photo'         => 'Choose Photo',
    'full_name'            => 'Full Name',
    'email'                => 'Email',
    'save_profile'         => 'Save Profile',
    'change_password'      => 'Change Password',
    'password_instruction' => 'Password must be at least 8 characters.',
    'current_password'     => 'Current Password',
    'new_password'         => 'New Password',
    'update_password'      => 'Update Password',
    'placeholder_current_password' => 'Enter your current password',
    'placeholder_new_password'     => 'Enter a new password (min. 8 characters)',
    'preferences'          => 'Preferences',
    'preferences_desc'     => 'Customize your experience.',
    'interface_language'   => 'Interface Language',
    'delete'               => 'Delete',
    'placeholder_confirm_password' => 'Re-enter your new password',
    'password_updated'   => 'Password updated successfully.',
    'password_incorrect' => 'Your current password is incorrect.',

    // =========================================================
    // PROGRES KELAS PAGE (TEACHER / STAFF ROLE)
    // =========================================================
    'progres_kelas_judul'       => 'Class Progress',
    'progres_kelas_desc'        => 'Monitor the development of all students',
    'siswa_aktif'               => ':count active students',
    'rata_rata_kelas'           => 'Class Average',
    'dari_semua_mapel'          => 'From all subjects',
    'lulus_kkm_label'           => 'Passed KKM',
    'pct_lulus_kkm'             => ':pct% of students passed KKM :kkm',
    'kehadiran_rata_label'      => 'Avg Attendance',
    'rata_seluruh_kelas'        => 'Average across all classes',
    'tugas_masuk_label'         => 'Assignments In',
    'total_tugas_dikumpulkan'   => 'total assignments submitted',
    'distribusi_nilai_judul'    => 'Grade Distribution',
    'distribusi_count'          => ':count students (:pct%)',
    'perhatian_khusus_judul'    => 'Needs Special Attention',
    'semua_siswa_baik'          => 'All students are doing well! 🎉',
    'perhatian_info'            => 'Average :avg · Attendance :kehadiran',
    'trend_turun'               => 'Declining',
    'peringkat_siswa_judul'     => 'Student Rankings',
    'belum_ada_data_siswa'      => 'No student data yet.',
    'kolom_nama_siswa'          => 'Student Name',
    'kolom_rata_rata'           => 'Average',
    'kolom_tugas'               => 'Assignments',
    'kolom_kehadiran'           => 'Attendance',
    'kolom_progress'            => 'Progress',
    'kolom_trend'               => 'Trend',
    'trend_naik_title'          => 'Increasing',
    'trend_turun_title'         => 'Declining',
    'trend_stabil_title'        => 'Stable',
    'rata_per_mapel_judul'      => 'Average Score per Subject',
    'mapel_lulus_kkm'           => ':lulus/:total passed KKM',

    // =========================================================
    // KELAS DETAIL PAGE (STUDENT ROLE)
    // =========================================================
    'tugas_overdue'             => ':count assignment(s) have passed the deadline and not yet submitted.',
    'materi_kelas'              => 'Class Materials',
    'jumlah_file'               => ':count file(s)',
    'tenggat_hari_ini'          => 'Due today!',
    'tenggat_hari_lagi'         => ':days day(s) left',
    'tenggat_tanggal'           => 'Due :date',
    'tutup'                     => 'Closed',
    'ganti_jawaban'             => 'Replace Answer',
    'upload_jawaban'            => 'Upload Answer',
    'format_jawaban'            => 'PDF, Word, or image (max 20MB)',
    'kirim'                     => 'Send',
    'belum_ada_materi_kelas'    => 'No materials from teacher yet.',
    'nilai_label'               => 'Grade',
    'tingkatkan_belajar'        => 'Increase your study intensity!',
    'belum_ada_penilaian'       => 'Teacher has not given a grade yet.',
    'status_pengumpulan'        => 'Submission Status',
    'progress_kumpul'           => ':done/:total submitted',
    'tenggat_mendatang'         => 'Upcoming Deadlines',
    'hari_ini_label'            => 'Today',
    'hari_label'                => ':days day(s)',

    // =========================================================
    // STUDENTS DATA PAGE
    // =========================================================
    'siswa_desc'                => 'List of all students registered in the system.',
    'cari_siswa_placeholder'    => 'Search student name or email...',
    'semua_status'              => 'All Status',
    'status_aktif'              => 'Active',
    'status_tidak_aktif'        => 'Inactive',
    'filter'                    => 'Filter',
    'reset'                     => 'Reset',
    'tidak_ada_siswa'           => 'No student data found.',
    'coba_kata_lain'            => 'Try a different keyword.',
    'kolom_siswa'               => 'Student',
    'kolom_email'               => 'Email',
    'kolom_status'              => 'Status',
    'kolom_bergabung'           => 'Joined',
    'lihat_detail'              => 'View Detail',
    'hapus_siswa'               => 'Delete Student',
    'konfirmasi_hapus_siswa'    => 'Delete student :name? This action cannot be undone.',
    'pagination_info'           => 'Showing :from–:to of :total students',

    // =========================================================
    // DASHBOARD PAGE (TEACHER / ADMIN)
    // =========================================================
    'total_siswa'          => 'Total Students',
    'rata_rata_skor'       => 'Average Score',
    'lulus_ai'             => 'AI Pass Rate',
    'analisis_performa'    => 'Class Performance Analysis',
    'hari_terakhir'        => 'Last Days',
    'siswa_beresiko'       => 'At-Risk Students',
    'ai_prediction'        => 'AI Prediction',
    'semua_aman'           => 'All students are monitored safe by AI.',
    'daftar_murid_terkini' => 'Latest Student List',
    'nama_murid'           => 'Student Name',
    'progress_belajar'     => 'Learning Progress',
    'skor_terakhir'        => 'Last Score',
    'status_adaptif'       => 'Adaptive Status',
    'aksi'                 => 'Action',

    // =========================================================
    // DASHBOARD PAGE (STUDENT ROLE)
    // =========================================================
    'halo_student'              => 'Hello, :name 👋',
    'semangat_belajar'          => 'Keep up the great work today!',
    'streak_belajar'            => 'Study Streak',
    'hari_berturut'             => 'consecutive days',
    'course_diikuti'            => 'Enrolled Courses',
    'course_aktif'              => 'active course(s)',
    'waktu_minggu_ini'          => 'This Week\'s Time',
    'jam_belajar'               => 'study hours',
    'course_saya'               => 'My Courses',
    'jumlah_course'             => ':count course(s)',
    'status_berjalan'           => 'In Progress',
    'status_selesai'            => 'Completed',
    'status_belum_mulai'        => 'Not Started',
    'aktivitas_minggu_ini'      => 'This Week\'s Learning Activity',
    'quiz_adaptif'              => 'Adaptive Quiz',
    'quiz_adaptif_desc'         => 'Questions tailored to your learning level',
    'mulai_sekarang'            => 'Start Now',
    'log_aktivitas'             => 'Activity Log',
    'belum_ada_aktivitas'       => 'No activity yet.',
    'level_advance'             => 'Advance',
    'level_intermediate'        => 'Intermediate',
    'level_beginner'            => 'Beginner',

    // =========================================================
    // MY CLASSES PAGE (TEACHER)
    // =========================================================
    'kelas_desc'           => 'Manage your classes and learning materials',
    'buat_kelas'           => 'Create Class',
    'kelola'               => 'Manage',
    'jumlah_siswa'         => ':count students',
    'jumlah_materi'        => ':count materials',
    'hapus_kelas'          => 'Delete Class',
    'konfirmasi_hapus'     => 'Are you sure you want to delete this class?',

    // =========================================================
    // MY CLASSES PAGE (STUDENT ROLE)
    // =========================================================
    'kelas_student_desc'        => 'Join a class with the code from your teacher',
    'masukkan_kode_kelas'       => 'Enter Class Code',
    'placeholder_kode_kelas'    => 'EXAMPLE: AB12CD',
    'tombol_join'               => 'Join',
    'kelas_diikuti'             => 'Enrolled Classes',
    'masuk_kelas'               => 'Enter Class',
    'keluar_kelas'              => 'Leave Class',
    'jumlah_materi_student'     => ':count material(s)',
    'belum_join_kelas'          => 'No classes joined yet',
    'petunjuk_join_kelas'       => 'Ask your teacher for the class code and enter it above',
    'konfirmasi_keluar_kelas'   => 'Leave class :name?',

    // =========================================================
    // EDIT MATERI PAGE (TEACHER ROLE)
    // =========================================================
    'edit_materi'               => 'Edit Material',
    'kembali'                   => 'Back',
    'ganti_file_label'          => 'Replace File (optional)',
    'file_saat_ini'             => 'Current file',
    'pilih_file_baru'           => 'Choose a new file to replace the current one',
    'simpan_perubahan'          => 'Save Changes',
    'batal'                     => 'Cancel',

    // =========================================================
    // LEARNING MATERIALS PAGE (TEACHER)
    // =========================================================
    'materi_judul_halaman'  => 'Materials',
    'upload_materi'         => 'Upload Learning Material',
    'judul_materi'          => 'Material Title',
    'placeholder_judul'     => 'Enter material title',
    'upload_file'           => 'Upload File',
    'format_file'           => 'PDF, Word, Excel, Video, JPG (max 50MB)',
    'deskripsi'             => 'Description',
    'placeholder_deskripsi' => 'Add material description',
    'tombol_upload'         => 'Upload Material',
    'daftar_materi'         => 'Materials List',
    'materi_tersedia'       => ':count material(s) available',
    'unduh'                 => 'Download',
    'edit'                  => 'Edit',
    'hapus'                 => 'Delete',
    'tenggat_lewat'         => 'Overdue',
    'kumpul_tugas'          => 'Submit Assignment',
    'konfirmasi_hapus_materi' => 'Delete this material?',
    'belum_ada_materi_empty'  => 'No materials yet. Contact your teacher!',

    // =========================================================
    // LEARNING MATERIALS PAGE (STUDENT ROLE)
    // =========================================================
    'sudah_dikumpul'            => 'Submitted',
    'belum_dikumpul'            => 'Not Submitted',
    'ganti_file'                => 'Replace',
    'kumpulkan'                 => 'Submit',
    'tenggat'                   => 'Deadline',
    'tidak_ada_materi'          => 'No materials available.',

    // =========================================================
    // DASHBOARD PAGE (STUDENT ROLE) – ADDITIONAL KEYS
    // =========================================================
    'course_label'              => 'course(s)',
    'jam_unit'                  => 'hr',       // "2 hr remaining"
    'mnt_unit'                  => 'min',      // "30 min remaining"
    'tersisa'                   => 'remaining',
    'jam_singkat'               => 'h',        // short label in bar chart: "2h"
    'perlu_perhatian'           => 'Needs Attention',
    'belum_ada_course'          => 'No courses enrolled yet.',
    'perhatian_diperlukan'      => 'Attention Required',
    'pesan_at_risk'             => 'Your progress needs more attention. Let\'s increase your study intensity!',

    // =========================================================
    // LEARNING PROGRESS PAGE (STUDENT ROLE)
    // =========================================================
    'progres_belajar_judul'     => 'Learning Progress',
    'hai_student'               => 'Hi, :name 👋',
    'ringkasan_progres'         => 'Here is a summary of your learning progress',
    'semester_genap'            => 'Even Semester :from/:to',
    'semester_ganjil'           => 'Odd Semester :from/:to',
    'rata_rata'                 => 'Average',
    'grade'                     => 'Grade :letter',
    'tugas'                     => 'Assignments',
    'tugas_dikumpulkan'         => 'assignment(s) submitted',
    'kehadiran'                 => 'Attendance',
    'kehadiran_hari'            => ':done of :total day(s)',
    'streak'                    => 'Streak',
    'hari_berturut_aktif'       => 'consecutive active days 🔥',
    'hari_singkat'              => 'd',        // short unit next to streak number: "14 d"
    'jumlah_aktivitas'          => ':count activities',
    'aktivitas_terbaru'         => 'Recent Activity',
    'belum_ada_aktivitas_tercatat' => 'No activity recorded yet.',
    'streak_tidak_aktif'        => 'Inactive',
    'streak_sedikit'            => 'Light',
    'streak_intensif'           => 'Intensive',
    'progres_per_mapel'         => 'Progress per Subject',
    'belum_ada_nilai'           => 'No grade data yet. Start completing your assignments!',
    'nilai_7_hari'              => 'Last 7 Days\' Scores',
    'belum_ada_aktivitas_minggu'=> 'No activity this week.',
    'streak_belajar_4minggu'    => 'Study Streak (4 Weeks)',

    // =========================================================
    // ATTENDANCE PAGE (STUDENT ROLE)
    // =========================================================
    'presensi_judul'            => 'Attendance',
    'belum_terdaftar_kelas'     => 'You are not enrolled in any class yet.',
    'belum_presensi'            => 'Not Yet Submitted',
    'sudah_presensi'            => 'Submitted',
    'hadir'                     => 'Present',
    'sakit'                     => 'Sick',
    'izin'                      => 'Excused',
    'alfa'                      => 'Absent',
    'keterangan_opsional'       => 'Note (optional)',
    'kirim_presensi'            => 'Submit Attendance',
    'riwayat_presensi'          => 'Attendance History',
    'tidak_ada_riwayat'         => 'No attendance history yet.',
    'badge_hadir'               => 'PRESENT',
    'badge_sakit'               => 'SICK',
    'badge_izin'                => 'EXCUSED',
    'badge_alfa'                => 'ABSENT',

    

];