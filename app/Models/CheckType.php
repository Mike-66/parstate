<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckType extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
    ];
    /*
     * @var mixed
     */

    public function checks() {
        return $this->hasMany(Check::class);
    }

    public function checktypetimezones() {
        return $this->hasMany(CheckTypeTimezone::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    /*
    public function missing_users( $parstate_id ) {

        $condition='TIMESTAMPDIFF(SECOND, updated_at, NOW()) > 30';
        $condition=$condition.' AND parstate_id IS NOT NULL';
        $condition=$condition.' AND alert_id IS NULL';
        return $this->users()->whereRaw( $condition )->get();

        //return $this->users->where('parstate_id','=',$parstate_id);
    }
    */

    public function alertable_missing_users(  $checktime_utc_limit )
    {
        Log::Info('in CheckType::alertable_missing_users ' );
        Log::Info('scanning for users last state before '.$checktime_utc_limit->toDateTimeString());

        return $this->users()
            ->whereNull('alert_id')  //missing_users
            ->whereHas('parstate',function($sql) use ($checktime_utc_limit)  {
                $sql->where('created_at','<', $checktime_utc_limit );
            })
            ->get();
    }


}
