<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLastTriggerToLastChecktimeInCheckTypeTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_type_timezones', function (Blueprint $table) {
            $table->renameColumn('last_trigger', 'last_checktime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_type_timezones', function (Blueprint $table) {
            $table->renameColumn('last_checktime', 'last_trigger');
        });
    }
}
