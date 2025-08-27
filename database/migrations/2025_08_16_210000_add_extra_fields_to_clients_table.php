<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Nuevos campos
            $table->string('cel1')->nullable()->after('phone');     // reemplaza "Teléfono"
            $table->string('cel2')->nullable()->after('cel1');
            $table->string('localidad')->nullable()->after('address');
            $table->string('cp', 10)->nullable()->after('localidad');
            $table->decimal('gps_lat', 10, 7)->nullable()->after('cp');
            $table->decimal('gps_lng', 10, 7)->nullable()->after('gps_lat');
            $table->string('dni', 20)->nullable()->after('email');
            $table->index('dni');
        });

        // Copiamos los valores de phone -> cel1 (por compatibilidad)
        try {
            DB::statement('UPDATE clients SET cel1 = phone WHERE (cel1 IS NULL OR cel1 = "") AND phone IS NOT NULL');
        } catch (\Throwable $e) {
            // no pasa nada si falla
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['dni']);
            $table->dropColumn(['cel1','cel2','localidad','cp','gps_lat','gps_lng','dni']);
        });
    }
};
