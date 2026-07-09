<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDepartmentToT100SjGeneral extends Migration
{
    public function up()
    {
        // SQL Server compatible column additions
        DB::statement("ALTER TABLE [t100_sj_general] ADD [department_id] int NULL");
        DB::statement("ALTER TABLE [t100_sj_general] ADD [department_name] nvarchar(100) NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE [t100_sj_general] DROP COLUMN [department_name]");
        DB::statement("ALTER TABLE [t100_sj_general] DROP COLUMN [department_id]");
    }
}
