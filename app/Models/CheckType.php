<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckType extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
    ];

    public function checks() {
        return $this->hasMany(Check::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
