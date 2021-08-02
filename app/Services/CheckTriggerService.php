<?php

namespace App\Services;

use App\Services\CarbonHelperService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckTriggerService {

    public    $now;
    protected $timezone;
    protected $now_tz;
    protected $now_tz_day;
    public    $checktime;
    protected $checktime_tz;
    public    $checktime_limit;

    public function __construct()
    {
        $this->now=Carbon::now();
    }

    /**
     * set class members depending on the users timezone needed for further processing
     *
     * @param $timezone
     */

    public function Prepare( $timezone ) {

        Log::Info('CheckTriggerService::Prepare timezone : '.$timezone);

        $this->timezone=$timezone;
        $this->now_tz = $this->now->toImmutable()->setTimezone($this->timezone);  //carbon object, cannot be string manipulated.
        Log::Info('CheckTriggerService::Prepare now in tz is '.$this->now_tz);

        //to be used as a time base for Set
        $this->now_tz_day = $this->now_tz->format(CarbonHelperService::$DAYFORMAT);
        Log::Info('CheckTriggerService::Prepare now_user_day in users timezone is is ' .$this->now_tz_day);

    }

    /**
     * build time string 'Y-m-d H:i:s' with H set to $hour and i set to $min
     *
     * @param $hour
     * @param int $hour
     * @param int $minunte
     * @return void
     */

    public function Set( $hour, $minute ) {

        //build time string in users timezone that represents the check time
        $shour = $hour;
        if ($hour < 1) $shour = '00'; else if ($hour < 10) $shour = '0' . $shour;
        $smin = $minute;
        if ($minute < 1) $smin = '00'; else if ($minute < 10) $smin = '0' . $smin;
        Log::Info('CheckTriggerService::Set check coordinates '.$shour .':'.$smin);
        $this->checktime_tz = $this->now_tz_day.' '. $shour.':'.$smin .':00';
        Log::Info('CheckTriggerService::Set check should start@earliest '.$this->checktime_tz .' '.$this->timezone);

        //calculate the UTC representation
        $this->checktime = Carbon::createFromFormat(CarbonHelperService::$TIMEFORMAT, $this->checktime_tz, $this->timezone)->setTimezone('UTC');
        Log::Info('CheckTriggerService::Set checktime_utc is  '.$this->checktime);

    }

    public function Limit( $interval ) {

        //subtract the interval, to have time point, after which the user had to send his keepalive
        $this->checktime_limit = Carbon::createFromFormat(CarbonHelperService::$TIMEFORMAT,$this->checktime);
        $this->checktime_limit->subSeconds($interval);
        Log::Info('CheckTriggerService::Limit checktime_limit '.$this->checktime_limit .' UTC');

    }

    public function Execute( $last_trigger ) {

        Log::Info('CheckTriggerService::Execute now           : '.$this->now);
        Log::Info('CheckTriggerService::Execute last_trigger  : '.$last_trigger);
        Log::Info('CheckTriggerService::Execute checktime_utc : '.$this->checktime);

        if ( $this->checktime->greaterThan($last_trigger) &&
            $this->now->greaterThanOrEqualTo($this->checktime) ) {
            return(true);
        }

        return(false);
    }
}
