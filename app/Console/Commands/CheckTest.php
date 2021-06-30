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

        $checktype=CheckType::find(1);
        printf("%s,%s\n",'CheckType',$checktype->name);
        foreach ($checktype->checks as $check)
        {
             printf("%s%s\n", "check at ", $check->hour);
             foreach ($checktype->missing_users(1) as $user) {
                 printf("%s,%s\n",'checking user ',$user->name);
            }
        }

        return 0;
    }
}
