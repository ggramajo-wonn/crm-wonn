<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ClientBalanceService;

class RecalcSaldos extends Command
{
    /**
     * Recalcula el saldo (pagos acreditados - facturas).
     * Usos:
     *  php artisan wonn:recalc-saldos
     *  php artisan wonn:recalc-saldos --client_id=123
     */
    protected $signature = 'wonn:recalc-saldos {--client_id=}';

    protected $description = 'Recalcula el saldo de todos los clientes o de un cliente puntual';

    public function handle(): int
    {
        $id = $this->option('client_id');

        if ($id !== null && $id !== '') {
            $saldo = ClientBalanceService::recalc((int) $id);
            $this->info("Saldo recalculado para cliente #{$id}: {$saldo}");
        } else {
            $this->info('Recalculando saldos para todos los clientes...');
            ClientBalanceService::recalcAll();
            $this->info('Listo.');
        }

        return self::SUCCESS;
    }
}
