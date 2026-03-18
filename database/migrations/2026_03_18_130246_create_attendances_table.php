<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alfa'])->default('alfa');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'kelas_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};