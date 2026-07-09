<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issue_material_details', function (Blueprint $table) {
            $table->string('lot_num', 100)->nullable()->after('qty_issue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_material_details', function (Blueprint $table) {
            $table->dropColumn('lot_num');
        });
    }
};
