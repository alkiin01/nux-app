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
        Schema::create('t100_user_menus', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('user_id', 36) ;
            $table->smallInteger('menu_id') ;      
            $table->smallInteger('as_create')->default(1);    
            $table->smallInteger('as_read')->default(1) ;      
            $table->smallInteger('as_update')->default(1) ;      
            $table->smallInteger('as_delete')->default(1) ;      
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t100_user_menus');
    }
};
