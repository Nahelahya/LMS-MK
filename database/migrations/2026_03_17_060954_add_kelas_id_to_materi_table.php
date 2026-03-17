<?php
// Jalankan: php artisan make:migration add_kelas_id_to_materi_table
// Lalu isi dengan ini:

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            // nullable agar materi lama tidak error
            $table->foreignId('kelas_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('kelas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};