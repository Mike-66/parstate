<?php

namespace App\Console\Commands;

use App\Models\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Models\Parstate;
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

        $condition='TIMESTAMPDIFF(SECOND, updated_at, NOW()) > 30';
        $condition=$condition.' AND parstate_id IS NOT NULL';
        $condition=$condition.' AND alert_id IS NULL';
        $missing_users=User::whereRaw( $condition )->get();

        foreach ($missing_users as $missing_user) {
            Log::debug('user id '.$missing_user->id.' is missed by '.env('APP_NAME', 'env app name missing'));
            $alert = new Alert();
            $alert->user_id=$missing_user->id;
            $alert->save();
            $missing_user->alert_id=$alert->id;
            $missing_user->save();
        }

        return 0;
    }
}
