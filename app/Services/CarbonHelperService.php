<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CarbonHelperService {

    public static $TIMEFORMAT = 'Y-m-d H:i:s';
    public static $DAYFORMAT = 'Y-m-d';

    public function __construct() {

    }
}
