<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ClientBalanceService
{
    /** Helper: devuelve el primer nombre de columna existente en $table */
    private static function pick(string $table, array $candidates): ?string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col)) return $col;
        }
        return null;
    }

    /** Helper: devuelve la primera tabla existente */
    private static function pickTable(array $candidates): ?string
    {
        foreach ($candidates as $t) {
            if (Schema::hasTable($t)) return $t;
        }
        return null;
    }

    /**
     * Recalcula y guarda el saldo del cliente.
     * Saldo = pagos acreditados - facturas (no anuladas).
     */
    public static function recalc(int $clientId): float
    {
        // ====== PAGOS ======
        $payTable  = self::pickTable(['payments', 'pagos']);
        $paid      = 0.0;

        if ($payTable) {
            $payAmount = self::pick($payTable, ['amount','monto','importe','total']);
            $payClient = self::pick($payTable, ['client_id','cliente_id','customer_id']);
            $payStatus = self::pick($payTable, ['status','estado']);

            if ($payAmount && $payClient) {
                $q = DB::table($payTable)->where($payClient, $clientId);
                if ($payStatus) {
                    // Solo pagos acreditados
                    $q->where($payStatus, 'acreditado');
                }
                $paid = (float) ($q->sum($payAmount) ?? 0);
            }
        }

        // ====== FACTURAS ======
        $invTable  = self::pickTable(['invoices', 'facturas']);
        $billed    = 0.0;

        if ($invTable) {
            $invAmount = self::pick($invTable, ['total','importe','monto','amount','bruto','neto']);
            $invClient = self::pick($invTable, ['client_id','cliente_id','customer_id']);
            $invStatus = self::pick($invTable, ['status','estado']);

            if ($invAmount && $invClient) {
                $q = DB::table($invTable)->where($invClient, $clientId);

                // Excluir anuladas/borradores si hay columna de estado
                if ($invStatus) {
                    $q->whereNotIn($invStatus, ['anulada','cancelada','void','anulado','borrador','draft']);
                }

                $billed = (float) ($q->sum($invAmount) ?? 0);
            }
        }

        $saldo = $paid - $billed;

        DB::table('clients')->where('id', $clientId)->update(['saldo' => $saldo]);

        return $saldo;
    }

    /** Recalcula saldos para todos los clientes. */
    public static function recalcAll(): void
    {
        $ids = DB::table('clients')->pluck('id');
        foreach ($ids as $id) {
            self::recalc((int) $id);
        }
    }
}
