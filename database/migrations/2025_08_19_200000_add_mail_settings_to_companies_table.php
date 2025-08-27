<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add `mail_settings` column to companies table.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Usamos longText para ser compatibles con cualquier versión de MySQL.
            // Si querés y tu DB lo soporta (MySQL 5.7+), podés cambiar por: $table->json('mail_settings')->nullable();
            $table->longText('mail_settings')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('mail_settings');
        });
    }
};
