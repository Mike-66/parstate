<?php

namespace App\Models;

use App\Traits\CarbonHelperTrait;
use Illuminate\Database\Eloquent\Model;

class Parstate extends Model
{
    use CarbonHelperTrait;

    public $fillable = [
        'user_id',
        'parstate_define_id',
        'created_at'
    ];
}
