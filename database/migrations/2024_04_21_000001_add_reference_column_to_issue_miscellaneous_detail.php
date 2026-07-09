<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('IssueMiscellaneousDetail', function (Blueprint $table) {
            $table->string('Reference', 255)->nullable()->after('LotNum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('IssueMiscellaneousDetail', function (Blueprint $table) {
            $table->dropColumn('Reference');
        });
    }
};
