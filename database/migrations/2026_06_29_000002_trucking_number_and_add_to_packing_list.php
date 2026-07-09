<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ── Create TruckingNumber master table ───────────────────────────────
        if (!Schema::hasTable('TruckingNumber')) Schema::create('TruckingNumber', function (Blueprint $table) {
            $table->id();
            $table->string('Nopol', 20);
            $table->string('Jenis', 50)->nullable();
            $table->string('Driver', 100)->nullable();
            $table->string('NoTlp', 20)->nullable();
            $table->string('DriverCadangan', 100)->nullable();
            $table->string('NoTlpCadangan', 20)->nullable();
        });

        // ── Add TruckingID FK to MasterPackShipments ────────────────────────
        if (!Schema::hasColumn('MasterPackShipments', 'TruckingID')) {
            Schema::table('MasterPackShipments', function (Blueprint $table) {
                $table->unsignedBigInteger('TruckingID')->nullable()->after('TrackingNumber');
            });
        }
    }

    public function down()
    {
        Schema::table('MasterPackShipments', function (Blueprint $table) {
            $table->dropColumn('TruckingID');
        });

        Schema::dropIfExists('TruckingNumber');
    }
};
