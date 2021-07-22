<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

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

        //QueryLog
        //DB::connection()->enableQueryLog();

        $user=User::find(1);
        Log::debug('user name'.$user->name.' timezone '.$user->timezone);

        $actualtime_user = Carbon::now()->setTimezone($user->timezone);
        $actualtime_user_format=$actualtime_user->format('Y-m-d H:i:s');
        Log::debug("actualtime in users timezone is is ".$actualtime_user_format);

        $actualtime_user_day=$actualtime_user->format('Y-m-d');
        Log::debug("actualtime_user_day in users timezone is is ".$actualtime_user_day);

        $checktype=CheckType::find(1);
        Log::debug('in CheckTest checktype,name=' . $checktype->name);
        foreach ($checktype->checks as $check ) {

            Log::debug('in CheckTest check.hour=' . $check->hour);
            Log::debug('in CheckTest check.minute=' . $check->minute);
            $shour=$check->hour;
            if( $check->hour < 1 ) $shour='00'; else if( $check->hour < 10 ) $shour='0'.$shour;
            $smin=$check->minute;
            if( $check->minute < 1 ) $smin='00'; else if ( $check->minute < 10 ) $smin='0'.$smin;
            $checktime_user=$actualtime_user_day.' '.$shour.':'.$smin.':00';
            Log::debug('in CheckTest check in users timezone should start @ earliest '.$checktime_user);

            $checktime_utc = Carbon::createFromFormat('Y-m-d H:i:s', $checktime_user,$user->timezone)->setTimezone('UTC');
            $checktime_utc_string=$checktime_utc->toDateTimeString();
            Log::debug('in CheckTest will be stored in lastcheck and can directly compared');
            Log::debug('with user.last_check. checktime_utc is  '.$checktime_utc_string);

            $checktime_utc_min = $checktime_utc->subSeconds($check->interval);
            Log::debug('this means in utc on server, the user should have');
            Log::debug('messaged last after '.$checktime_utc_min);

        }

        //QueryLog
        //$queries = DB::getQueryLog();
        //foreach ($queries as $query) {
        //    Log::debug($query);
        //}

        /*
        $alert=Alert::find(3);
        Log::debug('test:check alert->handled='.$alert->handled_by);
        if( $alert->handled_by > 0  ) {
            Log::debug('test:check alert->handled_by='.$alert->handled_by);
        }
        else {
            Log::debug('test:check alert->handled_by is NULL');
        }
        */

        /*
        $startDate = Carbon::parse('2021-07-13 16:00:00', 'Europe/Berlin')->setTimezone('UTC');
        $endDate = Carbon::parse('2021-07-13 20:00:00', 'Europe/Berlin')->setTimezone('UTC');
        Log::debug('test:check startDate='.$startDate);
        Log::debug('test:check endDate='.$endDate);
        */

        return 0;
    }
}
