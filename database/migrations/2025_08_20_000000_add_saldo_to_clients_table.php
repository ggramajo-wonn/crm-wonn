<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'saldo')) {
                // ajusta la posición (after) si tu tabla no tiene 'estado'
                $table->decimal('saldo', 12, 2)->default(0)->index()->after('estado');
            }
        });

        // --- Backfill: calcula saldo = pagos acreditados - facturas ---
        if (Schema::hasTable('clients')) {
            // Detectar columnas reales (amount/monto/importe, client_id/cliente_id, status/estado)
            $payAmount = Schema::hasColumn('payments', 'amount') ? 'amount'
                      : (Schema::hasColumn('payments', 'monto') ? 'monto'
                      : (Schema::hasColumn('payments', 'importe') ? 'importe' : null));
            $payClient = Schema::hasColumn('payments', 'client_id') ? 'client_id'
                      : (Schema::hasColumn('payments', 'cliente_id') ? 'cliente_id' : null);
            $payStatus = Schema::hasColumn('payments', 'status') ? 'status'
                      : (Schema::hasColumn('payments', 'estado') ? 'estado' : null);

            $invAmount = Schema::hasColumn('invoices', 'amount') ? 'amount'
                      : (Schema::hasColumn('invoices', 'monto') ? 'monto'
                      : (Schema::hasColumn('invoices', 'importe') ? 'importe' : null));
            $invClient = Schema::hasColumn('invoices', 'client_id') ? 'client_id'
                      : (Schema::hasColumn('invoices', 'cliente_id') ? 'cliente_id' : null);

            $clients = DB::table('clients')->select('id')->get();
            foreach ($clients as $c) {
                $paid = 0; $billed = 0;

                if ($payAmount && $payClient) {
                    $q = DB::table('payments')->where($payClient, $c->id);
                    if ($payStatus) { $q->where($payStatus, 'acreditado'); }
                    $paid = (float) ($q->sum($payAmount) ?? 0);
                }

                if ($invAmount && $invClient) {
                    $billed = (float) (DB::table('invoices')->where($invClient, $c->id)->sum($invAmount) ?? 0);
                }

                DB::table('clients')->where('id', $c->id)->update([
                    'saldo' => $paid - $billed,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'saldo')) {
                $table->dropColumn('saldo');
            }
        });
    }
};
