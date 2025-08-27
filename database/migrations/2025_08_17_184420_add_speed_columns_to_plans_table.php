<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'mb_down')) {
                $table->unsignedInteger('mb_down')->nullable()->after('price');
            }
            if (!Schema::hasColumn('plans', 'mb_up')) {
                $table->unsignedInteger('mb_up')->nullable()->after('mb_down');
            }
            // (opcional) si quieres mantener también los nombres viejos mientras migras:
            // if (!Schema::hasColumn('plans', 'down_mbps')) $table->unsignedInteger('down_mbps')->nullable()->after('mb_up');
            // if (!Schema::hasColumn('plans', 'up_mbps')) $table->unsignedInteger('up_mbps')->nullable()->after('down_mbps');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'mb_down')) $table->dropColumn('mb_down');
            if (Schema::hasColumn('plans', 'mb_up'))   $table->dropColumn('mb_up');
        });
    }
};
