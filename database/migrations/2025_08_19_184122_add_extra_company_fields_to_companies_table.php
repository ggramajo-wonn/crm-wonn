<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'fantasy_name')) {
                $table->string('fantasy_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('companies', 'address')) {
                $table->string('address')->nullable()->after('fantasy_name');
            }
            if (!Schema::hasColumn('companies', 'locality')) {
                $table->string('locality')->nullable()->after('address');
            }
            if (!Schema::hasColumn('companies', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('locality');
            }
            if (!Schema::hasColumn('companies', 'phones')) {
                $table->string('phones')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('companies', 'website')) {
                $table->string('website')->nullable()->after('phones');
            }
            if (!Schema::hasColumn('companies', 'google_maps_key')) {
                $table->string('google_maps_key')->nullable()->after('website');
            }
            if (!Schema::hasColumn('companies', 'logo_path')) {
                $table->string('logo_path')->nullable()->after('google_maps_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'fantasy_name')) {
                $table->dropColumn('fantasy_name');
            }
            if (Schema::hasColumn('companies', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('companies', 'locality')) {
                $table->dropColumn('locality');
            }
            if (Schema::hasColumn('companies', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('companies', 'phones')) {
                $table->dropColumn('phones');
            }
            if (Schema::hasColumn('companies', 'website')) {
                $table->dropColumn('website');
            }
            if (Schema::hasColumn('companies', 'google_maps_key')) {
                $table->dropColumn('google_maps_key');
            }
            if (Schema::hasColumn('companies', 'logo_path')) {
                $table->dropColumn('logo_path');
            }
        });
    }
};
