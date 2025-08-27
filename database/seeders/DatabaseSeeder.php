<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Company;
use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ───────────────────────────
         *  Usuario administrador
         * ─────────────────────────── */
        User::firstOrCreate(
            ['email' => 'admin@wonn.ar'],
            ['name' => 'Admin', 'password' => Hash::make('GDGc0n334')]
        );

        /* ───────────────────────────
         *  Empresa demo
         * ─────────────────────────── */
        Company::firstOrCreate(
            ['name' => 'WONN internet'],
            ['cuit' => null, 'logo_path' => null]
        );

        /* ───────────────────────────
         *  Catálogo de planes (Servicios de Internet)
         * ─────────────────────────── */
        if (class_exists(Plan::class) && Plan::count() === 0) {
            Plan::insert([
                [
                    'name'        => 'Internet 50 Mb',
                    'price'       => 9000,
                    'mb_down'     => 50,
                    'mb_up'       => 10,
                    'description' => 'Plan residencial básico.',
                    'status'      => 'activo',
                    'created_at'  => now(), 'updated_at' => now(),
                ],
                [
                    'name'        => 'Internet 100 Mb',
                    'price'       => 15000,
                    'mb_down'     => 100,
                    'mb_up'       => 20,
                    'description' => 'Plan recomendado para hogares.',
                    'status'      => 'activo',
                    'created_at'  => now(), 'updated_at' => now(),
                ],
                [
                    'name'        => 'Internet 300 Mb',
                    'price'       => 28000,
                    'mb_down'     => 300,
                    'mb_up'       => 50,
                    'description' => 'Alto rendimiento.',
                    'status'      => 'inactivo',
                    'created_at'  => now(), 'updated_at' => now(),
                ],
            ]);
        }

        /* ───────────────────────────
         *  Clientes demo
         *   (con campos extendidos: DNI, cel1/cel2, localidad, CP, GPS)
         * ─────────────────────────── */
        $c1 = Client::firstOrCreate(
            ['email' => 'ggrama@gmail.com'],
            [
                'name'      => 'GRAMAJO GUSTAVO DANIEL',
                'dni'       => '34721181',
                'cel1'      => '3872548277',
                'cel2'      => '3878670037',
                'address'   => 'Dorrego 426',
                'localidad' => 'ORAN',
                'cp'        => '4530',
                'gps_lat'   => -23.126381,
                'gps_lng'   => -64.323846,
                'status'    => 'activo',
            ]
        );

        $c2 = Client::firstOrCreate(
            ['email' => 'c2@example.com'],
            [
                'name'      => 'Cliente Dos',
                'cel1'      => '381-111111',
                'localidad' => 'Salta',
                'status'    => 'activo',
            ]
        );

        $c3 = Client::firstOrCreate(
            ['email' => 'vjvg@example.com'],
            [
                'name'      => 'VERONICA JOHANNA VILLAFUERTE GALARZA',
                'cel1'      => '381-222222',
                'localidad' => 'Salta',
                'status'    => 'activo',
            ]
        );

        /* ───────────────────────────
         *  Servicios contratados (por cliente)
         *  (la tabla services no usa plan_id, cargamos nombre/precio directo)
         * ─────────────────────────── */
        Service::firstOrCreate(
            ['client_id' => $c1->id, 'name' => 'Internet 100Mb'],
            ['price' => 15000, 'status' => 'activo', 'started_at' => now()->subMonths(2)]
        );

        Service::firstOrCreate(
            ['client_id' => $c1->id, 'name' => 'TV Play'],
            ['price' => 7000, 'status' => 'suspendido', 'started_at' => now()->subMonths(3), 'suspended_at' => now()->subDays(10)]
        );

        Service::firstOrCreate(
            ['client_id' => $c3->id, 'name' => 'Internet 100Mb'],
            ['price' => 15000, 'status' => 'activo', 'started_at' => now()->subMonth()]
        );

        /* ───────────────────────────
         *  Facturas + Pagos de ejemplo
         * ─────────────────────────── */

        // Cliente 1: una factura vencida con pago parcial + saldos a acreditar (y un duplicado)
        $inv1 = Invoice::create([
            'client_id' => $c1->id,
            'total'     => 20000,
            'issued_at' => now()->subDays(15),
            'due_at'    => now()->subDays(5),
            'status'    => 'vencida',
        ]);

        // Pago parcial acreditado
        Payment::create([
            'invoice_id' => $inv1->id,
            'client_id'  => $c1->id,
            'amount'     => 5000,
            'paid_at'    => now()->subDays(3),
            'source'     => 'manual',
            'status'     => 'acreditado',
            'reference'  => 'A-' . Str::upper(Str::random(6)),
        ]);

        // Saldo a acreditar (pendiente, sin factura)
        Payment::create([
            'invoice_id' => null,
            'client_id'  => $c1->id,
            'amount'     => 8000,
            'paid_at'    => now()->subDays(1),
            'source'     => 'efectivo',
            'status'     => 'pendiente',
            'reference'  => '151515',
        ]);

        // Duplicado de la misma referencia
        Payment::create([
            'invoice_id' => null,
            'client_id'  => $c1->id,
            'amount'     => 8000,
            'paid_at'    => now()->subDays(1),
            'source'     => 'efectivo',
            'status'     => 'duplicado',
            'reference'  => '151515',
        ]);

        // Cliente 2: factura pagada completamente
        $inv2 = Invoice::create([
            'client_id' => $c2->id,
            'total'     => 15000,
            'issued_at' => now()->subDays(7),
            'due_at'    => now()->addDays(7),
            'status'    => 'emitida',
        ]);

        Payment::create([
            'invoice_id' => $inv2->id,
            'client_id'  => $c2->id,
            'amount'     => 15000,
            'paid_at'    => now()->subDays(2),
            'source'     => 'manual',
            'status'     => 'acreditado',
            'reference'  => 'P-' . Str::upper(Str::random(6)),
        ]);
        $inv2->update(['status' => 'pagada']);

        // Cliente 3: solo un saldo a acreditar
        Payment::create([
            'invoice_id' => null,
            'client_id'  => $c3->id,
            'amount'     => 15000,
            'paid_at'    => now()->subDays(4),
            'source'     => 'manual',
            'status'     => 'pendiente',
            'reference'  => 'CRED-' . Str::upper(Str::random(5)),
        ]);
    }
}
