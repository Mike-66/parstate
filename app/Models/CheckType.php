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

    /*
    public function missing_users( $parstate_id ) {

        $condition='TIMESTAMPDIFF(SECOND, updated_at, NOW()) > 30';
        $condition=$condition.' AND parstate_id IS NOT NULL';
        $condition=$condition.' AND alert_id IS NULL';
        return $this->users()->whereRaw( $condition )->get();

        //return $this->users->where('parstate_id','=',$parstate_id);
    }
    */

    public function missing_users()
    {
        return $this->users()  //missing_users
            ->whereHas('parstate',function($sql){
                $sql->where('created_at','<',now()->subseconds(30));
            })
            ->get();

    }


}
