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
        'last_trigger',
        'last_checked_at',
        'updated_at',
    ];

    public function checktypes() {
        return $this->belogsTo(CheckType::class);
    }

}
