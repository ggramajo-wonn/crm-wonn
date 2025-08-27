<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Si querés registrar comandos manualmente, listalos acá.
     * (Además, en commands() se cargan automáticamente los de app/Console/Commands)
     */
    protected $commands = [
        \App\Console\Commands\RecalcSaldos::class,
    ];

    /**
     * Definí tareas programadas acá (opcional).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ejemplos:
        // $schedule->command('inspire')->hourly();

        // Recalcular saldos todas las noches a las 02:00 (opcional)
        // $schedule->command('wonn:recalc-saldos')->dailyAt('02:00');
    }

    /**
     * Registrá los comandos de la aplicación.
     */
    protected function commands(): void
    {
        // Carga automática de comandos en app/Console/Commands
        $this->load(__DIR__ . '/Commands');

        // Ruta para definir closures de consola si los usás
        require base_path('routes/console.php');
    }
}
