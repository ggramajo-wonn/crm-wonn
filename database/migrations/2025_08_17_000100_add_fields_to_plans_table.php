<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'down_mbps')) {
                $table->unsignedInteger('down_mbps')->default(0)->after('price');
            }
            if (!Schema::hasColumn('plans', 'up_mbps')) {
                $table->unsignedInteger('up_mbps')->default(0)->after('down_mbps');
            }
            if (!Schema::hasColumn('plans', 'description')) {
                $table->text('description')->nullable()->after('up_mbps');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'description')) $table->dropColumn('description');
            if (Schema::hasColumn('plans', 'up_mbps'))     $table->dropColumn('up_mbps');
            if (Schema::hasColumn('plans', 'down_mbps'))   $table->dropColumn('down_mbps');
        });
    }
};
