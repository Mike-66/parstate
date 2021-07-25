<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckTypeTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_type_timezones', function (Blueprint $table) {
            $table->id();
            $table->integer('check_type_id');
            $table->string('timezone')->default('Europe/Berlin')->unique();
            $table->dateTime('last_trigger')->nullable();
            $table->dateTime('last_checked_at')->nullable();
            $table->timestamps();
        });

        DB::table('check_type_timezones')->insert([
                'check_type_id' => '1',
                'timezone' => 'Europe/Berlin',
                "created_at" =>  \Carbon\Carbon::now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_type_timezones');
    }
}
