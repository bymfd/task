<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Devices extends Migration
{
    /**
     * Run the migrations.
     * uid and app_id column lenght base - https://firebase.google.com/docs/auth/admin/manage-users
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 128);
            $table->string('app_id', 128);
            $table->string("language");
            $table->string("os");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *integer
     * @return void
     */
    public function down()
    {
        //
    }
}
