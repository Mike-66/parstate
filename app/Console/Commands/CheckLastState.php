<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\CheckType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Models\User;


class CheckLastState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:laststate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'iterate users to check last state';

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
        Log::debug('check:laststate fired');

        //select now(), CONVERT_TZ( now() , 'UTC', 'Europe/Berlin' );

        CheckType::find(1)->alertable_missing_users()
            ->each(function(User $missing_user){
                Log::debug('user name/id '.$missing_user->name.'/'.$missing_user->id.' is missed by '.env('APP_NAME', 'env app name missing'));
                $alert = new Alert();
                $alert->user_id=$missing_user->id;
                $alert->save();
                $missing_user->alert_id=$alert->id;
                $missing_user->save();
            } )
        ;

        return 0;
    }
}
