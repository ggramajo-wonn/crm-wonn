<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('email_logs', 'client_id')) $table->unsignedBigInteger('client_id')->nullable()->after('id');
            if (!Schema::hasColumn('email_logs', 'to')) $table->string('to')->nullable()->after('client_id');
            if (!Schema::hasColumn('email_logs', 'subject')) $table->string('subject')->nullable()->after('to');
            if (!Schema::hasColumn('email_logs', 'body')) $table->longText('body')->nullable()->after('subject');
            if (!Schema::hasColumn('email_logs', 'status')) $table->string('status', 30)->nullable()->after('body');
            if (!Schema::hasColumn('email_logs', 'error')) $table->text('error')->nullable()->after('status');
            if (!Schema::hasColumn('email_logs', 'sent_at')) $table->timestamp('sent_at')->nullable()->after('error');

            if (!Schema::hasColumn('email_logs', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            // No quitamos columnas por seguridad en rollbacks
        });
    }
};
