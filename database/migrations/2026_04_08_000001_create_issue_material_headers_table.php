<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_material_headers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_num', 30)->unique();
            $table->string('job_num', 50)->index();
            $table->string('job_part_num', 100)->nullable();
            $table->date('doc_date');
            $table->decimal('total_required_qty', 18, 4)->default(0);
            $table->decimal('total_issued_qty', 18, 4)->default(0);
            $table->decimal('issue_percent', 6, 2)->default(0);
            $table->string('status', 30)->default('NOT_ISSUED')->index();
            $table->string('api_sync_status', 30)->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_material_headers');
    }
};
