<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('naps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('olt_id')->index();
            $table->string('name');
            $table->string('ubicacion')->nullable();
            $table->string('gps')->nullable();
            $table->unsignedSmallInteger('puertos')->default(8);
            $table->text('detalles')->nullable();
            $table->timestamps();

            $table->foreign('olt_id')->references('id')->on('olts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('naps');
    }
};
