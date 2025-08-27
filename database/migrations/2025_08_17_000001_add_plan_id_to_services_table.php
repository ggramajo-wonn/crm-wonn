<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('services', 'plan_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreignId('plan_id')->nullable()->after('client_id')->constrained('plans')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('services', 'plan_id')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropConstrainedForeignId('plan_id');
            });
        }
    }
};
