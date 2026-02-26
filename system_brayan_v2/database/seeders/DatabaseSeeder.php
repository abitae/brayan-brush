<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Primero crear tablas SUNAT (si existe SqlFileSeeder)
        $this->call([
            SqlFileSeeder::class, // Si existe, cargar tablas SUNAT
        ]);

        // 2. Configuración básica
        $this->call([
            CompanySeeder::class,
            SucursalSeeder::class,
            TipoEntryCajaSeeder::class,
            TipoExitCajaSeeder::class,
        ]);

        // 3. Permisos y usuarios
        $this->call([
            PermisoSeeder::class,
            UserSeeder::class,
        ]);

        // 4. Recursos operativos
        $this->call([
            TransportistaSeeder::class,
            VehiculoSeeder::class,
            CustomerSeeder::class,
            RutaSucursalSeeder::class,
        ]);

        // 5. Datos de prueba (encomiendas)
        $this->call([
            EncomiendaSeeder::class,
            PaqueteSeeder::class,
            ManifiestoSeeder::class,
        ]);

        // 6. Facturacion (depende de encomiendas y clientes)
        $this->call([
            DespatcheSeeder::class,
            DespatcheDetailSeeder::class,
            TicketSeeder::class,
            TicketDetailSeeder::class,
            InvoiceSeeder::class,
            InvoiceDetailSeeder::class,
            NoteSeeder::class,
            NoteDetailSeeder::class,
        ]);

        // 7. Cajas (al final porque depende de usuarios y tipos)
        $this->call([
            CajaSeeder::class,
            EntryCajaSeeder::class,
            ExitCajaSeeder::class,
        ]);

        // 8. Frontend (formularios publicos)
        $this->call([
            MessageSeeder::class,
            ReclamacionSeeder::class,
        ]);
    }
}

