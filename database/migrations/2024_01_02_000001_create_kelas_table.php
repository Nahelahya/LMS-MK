<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_kelas');
            $table->string('mata_pelajaran');
            $table->string('kode_kelas', 8)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kelas'); }
};
