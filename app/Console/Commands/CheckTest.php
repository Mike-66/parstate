<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\CheckType;
use App\Models\Check;
use App\Models\Alert;


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

        $alert=Alert::find(3);
        Log::debug('test:check alert->handled='.$alert->handled_by);
        if( $alert->handled_by > 0  ) {
            Log::debug('test:check alert->handled_by='.$alert->handled_by);
        }
        else {
            Log::debug('test:check alert->handled_by is NULL');
        }



        return 0;
    }
}
