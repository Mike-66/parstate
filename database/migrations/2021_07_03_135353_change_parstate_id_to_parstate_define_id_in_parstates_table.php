<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeParstateIdToParstateDefineIdInParstatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parstates', function (Blueprint $table) {
            $table->renameColumn('parstate_id', 'parstate_define_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parstates', function (Blueprint $table) {
            $table->renameColumn('parstate_define_id', 'parstate_id');
        });
    }
}
