<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['enviado','rebotado','fallido','pendiente'])->default('enviado');
            $table->string('provider')->nullable(); // ej: smtp, ses, mailgun
            $table->json('meta')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('email_logs'); }
};
