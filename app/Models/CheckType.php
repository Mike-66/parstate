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

    public function users() {
        return $this->hasMany(User::class);
    }

    public function missing_users( $parstate_id ) {
        return $this->users->where('parstate_id','=',$parstate_id);
    }
}
