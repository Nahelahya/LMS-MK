<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel soal yang diinput staff
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->string('mata_pelajaran');
            $table->text('pertanyaan');
            $table->enum('tipe', ['pilihan_ganda', 'essay']);
            $table->enum('level', ['mudah', 'sedang', 'sulit'])->default('sedang');
            // Untuk pilihan ganda
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->string('jawaban_benar')->nullable(); // 'a','b','c','d' atau null untuk essay
            $table->text('pembahasan')->nullable();
            $table->timestamps();
        });

        // Tabel sesi quiz siswa
        Schema::create('quiz_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('mata_pelajaran');
            $table->enum('level_awal', ['mudah', 'sedang', 'sulit']);
            $table->integer('total_soal')->default(10);
            $table->integer('soal_ke')->default(0);
            $table->integer('benar')->default(0);
            $table->integer('salah')->default(0);
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->decimal('skor_akhir', 5, 2)->nullable();
            $table->json('soal_ids')->nullable(); // array ID soal yang sudah ditampilkan
            $table->timestamps();
        });

        // Tabel jawaban per soal dalam sesi
        Schema::create('quiz_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('quiz_sessions')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('jawaban_siswa');
            $table->boolean('is_benar')->default(false);
            $table->text('feedback_ai')->nullable(); // feedback dari Groq untuk essay
            $table->enum('level_soal', ['mudah', 'sedang', 'sulit']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_jawabans');
        Schema::dropIfExists('quiz_sessions');
        Schema::dropIfExists('soals');
    }
};