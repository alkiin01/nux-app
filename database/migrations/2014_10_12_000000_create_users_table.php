<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{ 
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 100)->unique();
            $table->string('full_name', 150)->nullable();
            $table->string('call_name', 50)->nullable();
            $table->string('email', 150)->unique();
            $table->smallInteger('gender_id')->default(1);  
            $table->string('phone_num', 15)->nullable(); 
            $table->string('password', 255)->default('default.png');   
            $table->string('signature', 255)->default('blank.png'); 
            $table->string('avatar', 255)->default('blank.png');     
            $table->smallInteger('role_id')->nullable(); 
            $table->smallInteger('created_by')->nullable(); 
            $table->smallInteger('updated_by')->nullable();
            $table->smallInteger('status_id')->default(3);
            $table->smallInteger('partner_id')->nullable(); 
            $table->rememberToken();
            $table->timestamps();
        });
    }
 
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
