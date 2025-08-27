<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipv4_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('network');          // '192.168.0.0'
            $table->unsignedTinyInteger('cidr'); // 0..32
            $table->unsignedBigInteger('router_id')->nullable()->index();
            $table->string('type')->default('ESTATICO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipv4_networks');
    }
};
