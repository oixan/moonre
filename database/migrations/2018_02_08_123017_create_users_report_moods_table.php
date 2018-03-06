<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersReportMoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_report_moods', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->string('report_id');
            $table->uuid('mood_id');
            $table->timestamps();

            $table->primary(['user_id', 'mood_id','report_id']);
            $table->unique(['user_id', 'mood_id','report_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_report_moods');
    }
}
