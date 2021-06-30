<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\CheckType;
use App\Models\Check;


class CheckTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        printf("%s\n","test:check called");

        /*
        $users=User::where('id',4)->get();

        foreach ($users as $user) {
            foreach ($user->checktype->checks as $check) {
                printf("%s,%s,%s\n",$user->name,$user->checktype->name,$user->checktype->check->hour);
            }
        }
        */

        $checktypes=CheckType::where('id',1)->get();
        foreach ($checktypes as $checktype) {
            printf("%s%s\n", "name:", $checktype->name);
            foreach ($checktype->checks as $check) {
                printf("%s%s\n", "name:", $check->hour);
            }
        }


        $checks=CheckType::find(1)->checks;
        foreach ($checks as $check) {
            printf("%s%s\n", "name:", $check->hour);
        }



        //foreach ($checktypes as $checktype) {
        //    printf("%s%s\n","name:",$checktype->name);
        //}




        return 0;
    }
}
