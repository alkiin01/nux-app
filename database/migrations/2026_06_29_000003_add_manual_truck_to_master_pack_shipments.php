<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddManualTruckToMasterPackShipments extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('MasterPackShipments', 'ManualNopol')) {
            DB::statement("ALTER TABLE MasterPackShipments ADD ManualNopol varchar(100) NULL");
        }
        if (!Schema::hasColumn('MasterPackShipments', 'ManualDriver')) {
            DB::statement("ALTER TABLE MasterPackShipments ADD ManualDriver varchar(200) NULL");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('MasterPackShipments', 'ManualDriver')) {
            DB::statement("ALTER TABLE MasterPackShipments DROP COLUMN ManualDriver");
        }
        if (Schema::hasColumn('MasterPackShipments', 'ManualNopol')) {
            DB::statement("ALTER TABLE MasterPackShipments DROP COLUMN ManualNopol");
        }
    }
}
