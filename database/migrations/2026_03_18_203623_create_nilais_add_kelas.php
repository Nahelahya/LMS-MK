<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel nilais (nilai tugas/quiz/ulangan siswa) ──────────
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mata_pelajaran');
            $table->string('judul')->nullable();
            $table->enum('tipe', ['tugas', 'quiz', 'ulangan', 'uas'])->default('tugas');
            $table->decimal('nilai', 5, 2)->default(0); // 0.00 – 100.00
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'mata_pelajaran']);
            $table->index('created_at');
        });

        // ── Tambah kolom kelas ke tabel users ─────────────────────
        // (tabel users sudah punya: id, name, email, role, status, ...)
        Schema::table('users', function (Blueprint $table) {
            $table->string('kelas')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kelas');
        });
    }
};
