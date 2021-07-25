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

        $now=Carbon::now();
        $now_format=$now->toDateTimeString();    // now seems to be something like a reference. I need a String
        // to create other instances of the same time

        $checktype=CheckType::find(1);
        foreach ($checktype->checktypetimezones as $checktypetimezone ) {
            Log::Info('checktypetimezone : ' . $checktypetimezone->timezone);

            $now_user = Carbon::createFromFormat('Y-m-d H:i:s',$now_format);
            $actualtime_user = $now_user->setTimezone($checktypetimezone->timezone);  //carbon object, cannot be string manipulated.
            $actualtime_user_format = $actualtime_user->format('Y-m-d H:i:s'); //string
            Log::Info("actualtime in users timezone is is " . $actualtime_user_format);

            $actualtime_user_day = $actualtime_user->format('Y-m-d');
            Log::Info("actualtime_user_day in users timezone is is " . $actualtime_user_day);

            foreach ($checktype->checks as $check) {

                //build time string in users timezone that represents the check time
                $shour = $check->hour;
                if ($check->hour < 1) $shour = '00'; else if ($check->hour < 10) $shour = '0' . $shour;
                $smin = $check->minute;
                if ($check->minute < 1) $smin = '00'; else if ($check->minute < 10) $smin = '0' . $smin;
                Log::Info('in CheckLastState check coordinates ' . $shour . ':' . $smin);
                $checktime_user = $actualtime_user_day . ' ' . $shour . ':' . $smin . ':00';
                Log::Info('in CheckLastState check should start@earliest ' . $checktime_user . ' ' .$checktypetimezone->timezone);

                //calculate the UTC representation
                $checktime_utc = Carbon::createFromFormat('Y-m-d H:i:s', $checktime_user, $checktypetimezone->timezone)->setTimezone('UTC');
                $checktime_utc_string = $checktime_utc->toDateTimeString();
                Log::Info('in CheckLastState checktime_utc is  ' . $checktime_utc_string);

                //subtract the interval, to have time point, after which the user had to send his keepalive
                $checktime_utc_min = Carbon::createFromFormat('Y-m-d H:i:s',$checktime_utc);
                $checktime_utc_min->subSeconds($check->interval);
                Log::Info('in CheckLastState checktime_utc_min '.$checktime_utc_min . ' UTC');

                Log::Info('in CheckLastState now           : '.$now);
                Log::Info('in CheckLastState last_trigger  : '.$checktypetimezone->last_trigger);
                Log::Info('in CheckLastState checktime_utc : '.$checktime_utc);

                if ( $checktime_utc->greaterThan($checktypetimezone->last_trigger) &&
                    $now->greaterThanOrEqualTo($checktime_utc) )  {

                    Log::Info('Yeah, we are triggering');

                    CheckType::find(1)->alertable_missing_users( $checktime_utc_min )
                        ->each(function(User $missing_user){
                            Log::Info('user name/id '.$missing_user->name.'/'.$missing_user->id.' is missed by '.env('APP_NAME', 'env app name missing'));
                            $alert = new Alert();
                            $alert->user_id=$missing_user->id;
                            $alert->save();
                            $missing_user->alert_id=$alert->id;
                            $missing_user->save();
                        } )
                    ;

                    //remark the check as done
                    $checktypetimezone->last_trigger=$checktime_utc;
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
