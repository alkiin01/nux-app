<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── MasterPackShipments ──────────────────────────────────────────────
        // 1. Ubah MasterPackID dari int ke varchar(50) untuk menampung nomor SAI-PCH-...
        DB::statement("ALTER TABLE MasterPackShipments ALTER COLUMN MasterPackID varchar(50) NULL");
        // 2. Rename MasterPackID → PackingListNum
        DB::statement("EXEC sp_rename 'MasterPackShipments.MasterPackID', 'PackingListNum', 'COLUMN'");
        // 3. Hapus kolom yang tidak dipakai
        DB::statement("ALTER TABLE MasterPackShipments DROP COLUMN ShipmentType");
        DB::statement("ALTER TABLE MasterPackShipments DROP COLUMN ShipDate");
        // 4. Tambah kolom customer
        DB::statement("ALTER TABLE MasterPackShipments ADD CustID varchar(50) NULL");
        DB::statement("ALTER TABLE MasterPackShipments ADD CustomerName varchar(200) NULL");

        // ── DtlPackShipment ──────────────────────────────────────────────────
        // 1. Rename DtlPackNum → PackNum
        DB::statement("EXEC sp_rename 'DtlPackShipment.DtlPackNum', 'PackNum', 'COLUMN'");
        // 2. Tambah PONum
        DB::statement("ALTER TABLE DtlPackShipment ADD PONum varchar(100) NULL");
    }

    public function down(): void
    {
        // Revert DtlPackShipment
        DB::statement("ALTER TABLE DtlPackShipment DROP COLUMN PONum");
        DB::statement("EXEC sp_rename 'DtlPackShipment.PackNum', 'DtlPackNum', 'COLUMN'");

        // Revert MasterPackShipments
        DB::statement("ALTER TABLE MasterPackShipments DROP COLUMN CustomerName");
        DB::statement("ALTER TABLE MasterPackShipments DROP COLUMN CustID");
        DB::statement("ALTER TABLE MasterPackShipments ADD ShipDate datetime NULL");
        DB::statement("ALTER TABLE MasterPackShipments ADD ShipmentType varchar(30) NULL");
        DB::statement("EXEC sp_rename 'MasterPackShipments.PackingListNum', 'MasterPackID', 'COLUMN'");
        DB::statement("ALTER TABLE MasterPackShipments ALTER COLUMN MasterPackID int NULL");
    }
};
