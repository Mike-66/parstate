<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parstate extends Model
{
    public $fillable = [
        'user_id',
        'parstate_define_id',
        'created_at'
    ];
}
