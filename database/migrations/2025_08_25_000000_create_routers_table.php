<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip');
            $table->string('gps')->nullable();              // "lat,lng" en una sola línea
            $table->string('api_user')->nullable();
            $table->string('api_pass')->nullable();
            $table->string('speed_control')->default('simple_queues'); // Colas simples (Estática)
            $table->string('model')->nullable();           // se completará por API
            $table->string('version')->nullable();         // se completará por API
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
