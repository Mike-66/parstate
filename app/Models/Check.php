<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    public $fillable = [
        'check_type_id',
        'hour',
        'minute',
        'interval'
    ];

    public function checktype() {
        return $this->belongsTo(CheckType::class);
    }
}
