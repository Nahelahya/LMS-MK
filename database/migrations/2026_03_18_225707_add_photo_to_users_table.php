<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    $hasPhoto    = Schema::hasColumn('users', 'photo');
    $hasLanguage = Schema::hasColumn('users', 'language');

    if (!$hasPhoto || !$hasLanguage) {
        Schema::table('users', function (Blueprint $table) use ($hasPhoto, $hasLanguage) {
            if (!$hasPhoto)    $table->string('photo')->nullable()->after('email');
            if (!$hasLanguage) $table->string('language')->default('id')->after('photo');
        });
    }
}

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'language']);
        });
    }
};