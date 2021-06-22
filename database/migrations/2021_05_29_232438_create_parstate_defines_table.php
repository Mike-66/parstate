<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ParstateDefine;

class CreateParstateDefinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parstate_defines', function (Blueprint $table) {
            $table->id();
            $table->string('description')->unique();
            $table->timestamps();
        });

        $description =  array(
            [
                'description' => 'juhu !!!',
            ],
            [
                'description' => 'geht',
            ],
            [
                'description' => 'soso lala',
            ],
            [
                'description' => 'war schon besser',
            ],
            [
                'description' => 'ned so doll',
            ],
            [
                'description' => 'schlecht',
            ],
        );
        foreach ($description as $desc){
            $parstates = new ParstateDefine(); //The Category is the model for your migration
            $parstates->description =$desc['description'];
            $parstates->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parstate_id');
    }
}
