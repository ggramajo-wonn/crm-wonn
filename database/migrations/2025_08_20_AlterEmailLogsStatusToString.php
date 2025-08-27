<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite no soporta modificar CHECK fácilmente: recreamos la tabla sin CHECK.
            DB::beginTransaction();
            // Crear tabla temporal con el esquema deseado (status como VARCHAR sin CHECK)
            DB::statement('CREATE TABLE email_logs_tmp (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                client_id INTEGER NULL,
                "to" VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                body TEXT NULL,
                status VARCHAR(32) NOT NULL DEFAULT "pending",
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )');

            // Copiar datos existentes
            DB::statement('INSERT INTO email_logs_tmp (id, client_id, "to", subject, body, status, created_at, updated_at)
                           SELECT id, client_id, "to", subject, body, status, created_at, updated_at FROM email_logs');

            // Reemplazar tabla original
            DB::statement('DROP TABLE email_logs');
            DB::statement('ALTER TABLE email_logs_tmp RENAME TO email_logs');
            DB::commit();
        } else {
            // MySQL/MariaDB: modificar la columna directamente a VARCHAR
            DB::statement('ALTER TABLE email_logs MODIFY COLUMN status VARCHAR(32) NOT NULL DEFAULT "pending"');
        }
    }

    public function down(): void
    {
        // Reversión opcional: intentar volver a un conjunto cerrado de estados
        // (Si tu base era MySQL con ENUM). En SQLite, recrearíamos con CHECK.
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::beginTransaction();
            DB::statement('CREATE TABLE email_logs_tmp (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                client_id INTEGER NULL,
                "to" VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                body TEXT NULL,
                status VARCHAR(32) NOT NULL DEFAULT "pending" CHECK(status IN ("queued","pending","sent","failed")),
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )');
            DB::statement('INSERT INTO email_logs_tmp (id, client_id, "to", subject, body, status, created_at, updated_at)
                           SELECT id, client_id, "to", subject, body, status, created_at, updated_at FROM email_logs');
            DB::statement('DROP TABLE email_logs');
            DB::statement('ALTER TABLE email_logs_tmp RENAME TO email_logs');
            DB::commit();
        } else {
            DB::statement('ALTER TABLE email_logs MODIFY COLUMN status ENUM("queued","pending","sent","failed") NOT NULL DEFAULT "pending"');
        }
    }
};
