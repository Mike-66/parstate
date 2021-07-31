<?php

namespace App\Services;

use App\Services\CarbonHelperService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckTriggerService {

    public    $now;
    protected $timezone;
    protected $actualtime_user;
    protected $actualtime_user_day;
    public    $checktime_utc;
    protected $checktime_user;
    public    $checktime_utc_limit;

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
        $now_user = $this->now->copy();
        $this->actualtime_user = $now_user->setTimezone($this->timezone);  //carbon object, cannot be string manipulated.
        Log::Info('CheckTriggerService::Prepare actualtime in tz is '.$this->actualtime_user);

        //o be used as a time base for Set
        $this->actualtime_user_day = $this->actualtime_user->format(CarbonHelperService::$DAYFORMAT);
        Log::Info('CheckTriggerService::Prepare actualtime_user_day in users timezone is is ' .$this->actualtime_user_day);

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
        $this->checktime_user = $this->actualtime_user_day.' '. $shour.':'.$smin .':00';
        Log::Info('CheckTriggerService::Set check should start@earliest '.$this->checktime_user .' '.$this->timezone);

        //calculate the UTC representation
        $this->checktime_utc = Carbon::createFromFormat(CarbonHelperService::$TIMEFORMAT, $this->checktime_user, $this->timezone)->setTimezone('UTC');
        Log::Info('CheckTriggerService::Set checktime_utc is  '.$this->checktime_utc);

    }

    public function Limit( $interval ) {

        //subtract the interval, to have time point, after which the user had to send his keepalive
        $this->checktime_utc_limit = Carbon::createFromFormat(CarbonHelperService::$TIMEFORMAT,$this->checktime_utc);
        $this->checktime_utc_limit->subSeconds($interval);
        Log::Info('CheckTriggerService::Limit checktime_utc_limit '.$this->checktime_utc_limit .' UTC');

    }

    public function Execute( $last_trigger ) {

        Log::Info('CheckTriggerService::Execute now           : '.$this->now);
        Log::Info('CheckTriggerService::Execute last_trigger  : '.$last_trigger);
        Log::Info('CheckTriggerService::Execute checktime_utc : '.$this->checktime_utc);

        if ( $this->checktime_utc->greaterThan($last_trigger) &&
            $this->now->greaterThanOrEqualTo($this->checktime_utc) ) {
            return(true);
        }

        return(false);
    }
}
