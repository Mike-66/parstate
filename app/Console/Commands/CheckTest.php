<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Models\CheckType;
use App\Models\CheckTypeTimezone;
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
        //QueryLog
        //DB::connection()->enableQueryLog();

        $now=Carbon::now();
        $now_format=$now->toDateTimeString();    // now seems to be something like a reference. I need a String
                                                 // to create other instances of the same time

        Log::debug('invoked : ' . $now_format.'-----------------------------------------------');

        $checktype=CheckType::find(1);
        foreach ($checktype->checktypetimezones as $checktypetimezone ) {
            Log::debug('checktypetimezone : ' . $checktypetimezone->timezone);

            $now_user = Carbon::createFromFormat('Y-m-d H:i:s',$now_format);
            $actualtime_user = $now_user->setTimezone($checktypetimezone->timezone);  //carbon object, cannot be string manipulated.
            $actualtime_user_format = $actualtime_user->format('Y-m-d H:i:s'); //string
            Log::debug("actualtime in users timezone is is " . $actualtime_user_format);

            $actualtime_user_day = $actualtime_user->format('Y-m-d');
            Log::debug("actualtime_user_day in users timezone is is " . $actualtime_user_day);

            foreach ($checktype->checks as $check) {

                //build time string in users timezone that represents the check time
                $shour = $check->hour;
                if ($check->hour < 1) $shour = '00'; else if ($check->hour < 10) $shour = '0' . $shour;
                $smin = $check->minute;
                if ($check->minute < 1) $smin = '00'; else if ($check->minute < 10) $smin = '0' . $smin;
                Log::debug('in CheckTest check coordinates ' . $shour . ':' . $smin);
                $checktime_user = $actualtime_user_day . ' ' . $shour . ':' . $smin . ':00';
                Log::debug('in CheckTest check should start@earliest ' . $checktime_user . ' ' .$checktypetimezone->timezone);

                //calculate the UTC representation
                $checktime_utc = Carbon::createFromFormat('Y-m-d H:i:s', $checktime_user, $checktypetimezone->timezone)->setTimezone('UTC');
                $checktime_utc_string = $checktime_utc->toDateTimeString();
                Log::debug('in CheckTest checktime_utc is  ' . $checktime_utc_string);

                //subtract the interval, to have time point, after which the user had to send his keepalive
                $checktime_utc_min = Carbon::createFromFormat('Y-m-d H:i:s',$checktime_utc);
                $checktime_utc_min->subSeconds($check->interval);
                Log::debug('in CheckTest checktime_utc_min '.$checktime_utc_min . ' UTC');

                Log::debug('in CheckTest now           : '.$now);
                Log::debug('in CheckTest last_trigger  : '.$checktypetimezone->last_trigger);
                Log::debug('in CheckTest checktime_utc : '.$checktime_utc);

                if ( $checktime_utc->greaterThan($checktypetimezone->last_trigger) &&
                    $now->greaterThanOrEqualTo($checktime_utc) )  {

                    Log::debug('Yeah, we are triggering');

                    $checktypetimezone->last_trigger=$checktime_utc;
                    $checktypetimezone->last_checked_at=Carbon::now()->toDateTimeString();
                    $checktypetimezone->touch();

                    break;
                }

            }
        }

        //QueryLog
        //$queries = DB::getQueryLog();
        //foreach ($queries as $query) {
        //    Log::debug($query);
        //}

        return 0;
    }
}
