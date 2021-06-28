<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Check;

class CheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $check=new Check();
        $check->check_type_id=1;
        $check->hour=12;
        $check->minute=0;
        $check->save();

        $check=new Check();
        $check->check_type_id=1;
        $check->hour=22;
        $check->minute=0;
        $check->save();
    }
}
