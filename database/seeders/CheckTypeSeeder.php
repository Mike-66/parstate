<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CheckType;


class CheckTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $checktype=new CheckType();
        $checktype->name='basic_check';
        $checktype->save();
    }
}
