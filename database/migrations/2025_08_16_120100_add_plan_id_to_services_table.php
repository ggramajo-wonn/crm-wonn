<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('client_id')
                  ->constrained('plans')->nullOnDelete();
        });

        // Migración de datos simple:
        // Creamos un plan por cada nombre de servicio distinto y linkeamos.
        $rows = DB::table('services')->select('id','name','price')->get();
        $cache = []; // name => plan_id

        foreach ($rows as $row) {
            if (!$row->name) continue;

            if (!isset($cache[$row->name])) {
                $planId = DB::table('plans')->where('name', $row->name)->value('id');
                if (!$planId) {
                    $planId = DB::table('plans')->insertGetId([
                        'name' => $row->name,
                        'price' => $row->price ?? 0,
                        'down_mbps' => null,
                        'up_mbps' => null,
                        'description' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $cache[$row->name] = $planId;
            }

            DB::table('services')->where('id', $row->id)->update(['plan_id' => $cache[$row->name]]);
        }
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
        });
    }
};
