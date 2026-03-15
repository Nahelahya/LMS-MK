<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_course');
            $table->string('kode_course')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    
    // 2. Tabel Student Progress (Data Dinamis untuk AI & Dashboard)
        Schema::create('student_progress', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('course_id');
        $table->float('last_score')->default(0);
        $table->integer('completion_percentage')->default(0);
        $table->enum('status_adaptif', ['Remedial', 'Normal', 'Advance'])->default('Normal');
        $table->boolean('is_at_risk')->default(false); // Flag untuk Siswa Beresiko
        $table->timestamps();
        });
// 3. Tabel Activity Logs (Jejak Digital Murid)
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('activity'); // Contoh: "Mengerjakan Quiz Logika"
        $table->integer('duration_minutes')->default(0);
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_system_tables');
    }
};
