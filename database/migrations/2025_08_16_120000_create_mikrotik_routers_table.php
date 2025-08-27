<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mikrotik_routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('ip');                 // IPv4 del router
            $table->string('api_user');
            $table->text('api_password');        // la guardamos encriptada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotik_routers');
    }
};
