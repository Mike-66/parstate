<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait CarbonHelperTrait {

    public function CreatedAtTZ( $timezone ) {

        return ($this->created_at->toImmutable()->setTimezone($timezone));

    }

    public function UpdatedAtTZ( $timezone ) {

        return ($this->updated_at->toImmutable()->setTimezone($timezone));

    }

}
