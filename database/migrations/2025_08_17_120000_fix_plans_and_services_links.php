<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Normaliza columnas de PLANS
        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                // Agregar si faltan
                if (!Schema::hasColumn('plans', 'mb_down')) {
                    $table->integer('mb_down')->nullable()->after('price');
                }
                if (!Schema::hasColumn('plans', 'mb_up')) {
                    $table->integer('mb_up')->nullable()->after('mb_down');
                }

                // (Opcional) borrar restos viejos si existen
                if (Schema::hasColumn('plans', 'down_mbps')) {
                    $table->dropColumn('down_mbps');
                }
                if (Schema::hasColumn('plans', 'up_mbps')) {
                    $table->dropColumn('up_mbps');
                }
            });
        }

        // Asegura el vínculo plan_id en SERVICES (solo si no existe)
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (!Schema::hasColumn('services', 'plan_id')) {
                    $table->foreignId('plan_id')->nullable()->after('client_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (Schema::hasColumn('services', 'plan_id')) {
                    $table->dropColumn('plan_id');
                }
            });
        }

        if (Schema::hasTable('plans')) {
            Schema::table('plans', function (Blueprint $table) {
                if (Schema::hasColumn('plans', 'mb_up'))   $table->dropColumn('mb_up');
                if (Schema::hasColumn('plans', 'mb_down')) $table->dropColumn('mb_down');
                // No recreamos down_mbps/up_mbps en el down por simplicidad
            });
        }
    }
};
