<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'ftth')) {
                $table->boolean('ftth')->default(false)->after('status');
            }
            if (!Schema::hasColumn('services', 'nap_id')) {
                $table->unsignedBigInteger('nap_id')->nullable()->after('ftth')->index();
            }
            if (!Schema::hasColumn('services', 'nap_port')) {
                $table->unsignedInteger('nap_port')->nullable()->after('nap_id');
            }

            // FK suave; si no existe la tabla naps, se ignora en tiempo de migración.
            try {
                if (Schema::hasTable('naps')) {
                    $table->foreign('nap_id')->references('id')->on('naps')->nullOnDelete();
                }
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'nap_port')) $table->dropColumn('nap_port');
            if (Schema::hasColumn('services', 'nap_id')) {
                try { $table->dropForeign(['nap_id']); } catch (\Throwable $e) {}
                $table->dropColumn('nap_id');
            }
            if (Schema::hasColumn('services', 'ftth')) $table->dropColumn('ftth');
        });
    }
};
