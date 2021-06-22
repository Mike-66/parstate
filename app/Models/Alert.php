<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Alert extends Model
{
    use HasFactory;

    protected  static  function  boot()
    {
        parent::boot();

        static::creating(function  ($model)  {
            $model->uuid = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'id',
        'uuid',
        'user_id',
        'handled',
        'handled_by',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
