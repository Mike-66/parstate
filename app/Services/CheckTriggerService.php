<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckTriggerService {

    private    $now;
    private    $timezone;
    /** @var Carbon */
    private    $checktime;
    private    $checktime_limit;

    public function __construct()
    {
        $this->now= CarbonImmutable::now();
    }

    /**
     * @return Carbon
     */
    public function getChecktime(): Carbon
    {
        return $this->checktime->copy();
    }

    /**
     * @return mixed
     */
    public function getChecktimeLimit()
    {
        return $this->checktime_limit->copy();
    }


    /**
     * set class members depending on the users timezone needed for further processing
     *
     * @param $timezone
     */

    public function Prepare( $timezone ) {

        Log::Info('CheckTriggerService::Prepare timezone : '.$timezone);
        $this->timezone=$timezone;

    }

    /**
     * build time string 'Y-m-d H:i:s' with H set to $hour and i set to $min
     *
     * @param $hour
     * @param int $hour
     * @param int $minunte
     * @return void
     */

    public function Set( $hour, $minute, $interval ) {

        $checktime_tz = Carbon::today($this->timezone)->setTime($hour,$minute)->toImmutable();
        Log::Info(sprintf("CheckTriggerService::Set check should start@earliest %s %s",
                    $checktime_tz->format(CarbonHelperService::$TIMEFORMAT),
                    $checktime_tz->timezoneName));

        //calculate the UTC representation
        $this->checktime = $checktime_tz->setTimezone('UTC')->toImmutable();
        Log::Info('CheckTriggerService::Set checktime_utc is  '.$this->checktime);

        //subtract the interval, to have time point, after which the user had to send his keepalive
        $this->checktime_limit = $this->checktime->subSeconds($interval);
        Log::Info('CheckTriggerService::Limit checktime_limit '.$this->checktime_limit .' UTC');

    }

    public function Execute( $last_trigger ) {

        Log::Info('CheckTriggerService::Execute now           : '.$this->now);
        Log::Info('CheckTriggerService::Execute last_trigger  : '.$last_trigger);
        Log::Info('CheckTriggerService::Execute checktime_utc : '.$this->checktime);

        if ( $this->checktime->greaterThan($last_trigger) &&
            $this->checktime->lessThanOrEqualTo($this->now) ) {
            return(true);
        }

        return(false);
    }


}
