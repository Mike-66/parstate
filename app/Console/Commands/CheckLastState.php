<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\CheckType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CheckTypeTimezone;
use App\Models\Check;
use App\Services\CheckTriggerService;



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
        Log::Info('check:laststate fired, sql_debug = '.config('parstate.sql_debug'));

        //select now(), CONVERT_TZ( now() , 'UTC', 'Europe/Berlin' );

        //QueryLog

        if ( config('parstate.sql_debug') == 'ON' ) {
            DB::connection()->enableQueryLog();
        }

        $checktriggerservice = new CheckTriggerService();

        //Basic idea is to have multiple check types in multiple timezones.
        // Here, just checktype.id 1 is handled

        $checktype=CheckType::find(1);
        foreach ($checktype->checktypetimezones as $checktypetimezone ) {
            $checktriggerservice->Prepare($checktypetimezone->timezone);
            foreach ($checktype->checks()->orderBy('hour')->orderBy('minute')->get() as $check) {
                $checktriggerservice->Set( $check->hour, $check->minute );
                $checktriggerservice->Limit( $check->interval );
                if ( $checktriggerservice->Execute($checktypetimezone->last_trigger) ) {
                    Log::Info('CheckLastState:: Yeah, we are triggering');
                    CheckType::find(1)->alertable_missing_users( $checktriggerservice->checktime_limit )
                        ->each(function(User $missing_user){
                            Log::Info('CheckLastState:: user name/id '.$missing_user->name.'/'.$missing_user->id.' is missed by '.env('APP_NAME', 'env app name missing'));
                            $alert = new Alert();
                            $alert->user_id=$missing_user->id;
                            $alert->save();
                            $missing_user->alert_id=$alert->id;
                            $missing_user->save();
                        } )
                    ;
                    //remark the check as done
                    $checktypetimezone->last_trigger=$checktriggerservice->checktime;
                    $checktypetimezone->last_checked_at=Carbon::now()->toDateTimeString();
                    $checktypetimezone->touch();
                    break;
                }

            }
        }

        //QueryLog
        if ( config('parstate.sql_debug')  == 'ON' ) {
           $queries = DB::getQueryLog();
            foreach ($queries as $query) {
                Log::Debug($query);
            }
        }

        return 0;
    }
}
