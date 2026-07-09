<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_material_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('header_id');
            $table->integer('mtl_seq')->nullable();
            $table->string('part_num', 100);
            $table->string('part_name', 255)->nullable();
            $table->string('uom', 20)->nullable();
            $table->decimal('qty_required', 18, 4)->default(0);
            $table->decimal('qty_issue', 18, 4)->default(0);
            $table->string('warehouse_code', 30)->nullable();
            $table->string('bin_num', 50)->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();

            $table->foreign('header_id')
                ->references('id')
                ->on('issue_material_headers')
                ->onDelete('cascade');

            $table->unique(['header_id', 'mtl_seq'], 'uniq_issue_material_header_mtlseq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_material_details');
    }
};
