<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('mt_name')->nullable()->after('logo_path');
            $table->string('mt_host')->nullable()->after('mt_name');
            $table->string('mt_user')->nullable()->after('mt_host');
            $table->text('mt_pass')->nullable()->after('mt_user'); // la guardamos cifrada
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['mt_name', 'mt_host', 'mt_user', 'mt_pass']);
        });
    }
};
