<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("IF COL_LENGTH('t100_sj_general', 'po_num') IS NULL ALTER TABLE [t100_sj_general] ADD [po_num] nvarchar(50) NULL");
        DB::statement("IF COL_LENGTH('t100_sj_general', 'ship_via_code') IS NULL ALTER TABLE [t100_sj_general] ADD [ship_via_code] nvarchar(50) NULL");
    }

    public function down(): void
    {
        DB::statement("IF COL_LENGTH('t100_sj_general', 'ship_via_code') IS NOT NULL ALTER TABLE [t100_sj_general] DROP COLUMN [ship_via_code]");
        DB::statement("IF COL_LENGTH('t100_sj_general', 'po_num') IS NOT NULL ALTER TABLE [t100_sj_general] DROP COLUMN [po_num]");
    }
};
