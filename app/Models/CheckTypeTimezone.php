<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckTypeTimezone extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'check_type_id',
        'timezone',
        'last_checktime',
        'last_checked_at',
        'updated_at',
    ];

    public function checktype() {
        return $this->belongsTo(CheckType::class);
    }

}
