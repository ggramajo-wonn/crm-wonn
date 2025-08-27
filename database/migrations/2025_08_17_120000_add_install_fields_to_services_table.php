<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Relación con planes (catálogo)
            if (!Schema::hasColumn('services', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->after('client_id')
                      ->constrained('plans')->nullOnDelete();
            }

            // Datos de instalación
            if (!Schema::hasColumn('services', 'address'))      $table->string('address')->nullable()->after('name');
            if (!Schema::hasColumn('services', 'locality'))     $table->string('locality')->nullable()->after('address');
            if (!Schema::hasColumn('services', 'postal_code'))  $table->string('postal_code', 20)->nullable()->after('locality');
            if (!Schema::hasColumn('services', 'gps'))          $table->string('gps', 100)->nullable()->after('postal_code'); // "-23.12,-64.32"
            if (!Schema::hasColumn('services', 'ip'))           $table->string('ip', 45)->nullable()->after('gps');           // IPv4/IPv6
            if (!Schema::hasColumn('services', 'router'))       $table->string('router')->nullable()->after('ip');            // etiqueta del router
            // started_at ya existe como “fecha instalación”
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services','router'))      $table->dropColumn('router');
            if (Schema::hasColumn('services','ip'))          $table->dropColumn('ip');
            if (Schema::hasColumn('services','gps'))         $table->dropColumn('gps');
            if (Schema::hasColumn('services','postal_code')) $table->dropColumn('postal_code');
            if (Schema::hasColumn('services','locality'))    $table->dropColumn('locality');
            if (Schema::hasColumn('services','address'))     $table->dropColumn('address');
            if (Schema::hasColumn('services','plan_id'))     $table->dropConstrainedForeignId('plan_id');
        });
    }
};
