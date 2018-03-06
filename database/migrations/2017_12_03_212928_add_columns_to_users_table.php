<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('surname')->nullable();
            $table->char('sex', 1)->nullable();
            $table->char('country',2)->nullable();
            $table->mediumInteger('city_id')->nullable();
            $table->string('address')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->change();
            $table->dropColumn('surname');
            $table->dropColumn('sex');
            $table->dropColumn('country');
            $table->dropColumn('city_id');
            $table->dropColumn('address');
            $table->dropSoftDeletes();
        });
    }
}
