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
use mysql_xdevapi\Exception;
use Throwable;


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

        $checktriggerservice = new CheckTriggerService();

        //QueryLog
        if ( config('parstate.sql_debug') == 'ON' ) {
            DB::connection()->enableQueryLog();
        }

        $check_type_old=0;
        //Basic idea is to have multiple check types in multiple timezones.
        //this loopset manages check of checktype/timezone combinations as defined in table checktypetimezones
        $checktypetimezones=CheckTypeTimezone::orderBy('check_type_id')->orderBy('timezone')->get();
        foreach( $checktypetimezones as $checktypetimezone ) {
            /** @var CheckType $checktype */
            if($check_type_old!=$checktypetimezone->check_type_id) {
                $checktype = CheckType::find($checktypetimezone->check_type_id); //TODO: bolongsTo in CheckTypeTimezone not working
                $checks=$checktype->checks()->orderBy('hour')->orderBy('minute')->get();
                $check_type_old=$checktypetimezone->check_type_id;
            }
            $checktriggerservice->Prepare($checktypetimezone->timezone);
            foreach ($checks as $check) {
                $checktriggerservice->Set( $check->hour, $check->minute, $check->interval );
                if ( $checktriggerservice->Execute($checktypetimezone->last_checktime) ) {
                    Log::Info('CheckLastState:: Yeah, we are triggering for timezone '.$checktypetimezone->timezone);
                    $checktype->alertable_missing_users( $checktypetimezone->timezone, $checktriggerservice->getChecktimeLimit() )
                        ->each(function(User $missing_user){
                            Log::Info('CheckLastState:: user name/id ' . $missing_user->name . '/' . $missing_user->id . ' is missed by ' . env('APP_NAME', 'env app name missing'));
                            $alert = new Alert();
                            $alert->user_id=$missing_user->id;
                            $alert->save();
                            $missing_user->alert_id=$alert->id;
                            $missing_user->save();
                        } )
                    ;
                    //remark the check as done
                    $checktypetimezone->last_checktime=$checktriggerservice->getChecktime();
                    $checktypetimezone->last_checked_at=Carbon::now()->toDateTimeString();
                    $checktypetimezone->save();
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
