<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MasterPackShipments', function (Blueprint $table) {
            if (!Schema::hasColumn('MasterPackShipments', 'ShippedAt')) {
                $table->dateTime('ShippedAt')->nullable()->after('PackingListNum');
            }

            if (!Schema::hasColumn('MasterPackShipments', 'ShippedBy')) {
                $table->string('ShippedBy', 100)->nullable()->after('ShippedAt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('MasterPackShipments', function (Blueprint $table) {
            if (Schema::hasColumn('MasterPackShipments', 'ShippedBy')) {
                $table->dropColumn('ShippedBy');
            }

            if (Schema::hasColumn('MasterPackShipments', 'ShippedAt')) {
                $table->dropColumn('ShippedAt');
            }
        });
    }
};
