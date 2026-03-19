<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek agar tidak crash jika sudah ada (safety guard)
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'language')) {
                $table->string('language', 10)->default('id')->after('photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'language']);
        });
    }
};