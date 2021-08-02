<?php

namespace App\Models;

use App\Jobs\SendEmail;
use App\Traits\CarbonHelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Alert extends Model
{
    use HasFactory;
    use CarbonHelperTrait;

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
        'updated_at',
    ];

    public function HandleByUser($user_id) {
        // for now, this function is called by_
        // a) AcknowledgeAlertController::ackowledge (userwatcher acknowledges)
        // b) ParstatesController::create (user submits state)
        // to prevent further email generation by set handled_at=1
        // does inform users about a) user is back b) acknowledgement of alert

        Log::debug('Alert:: HandleByUser called for user_id '.$user_id);
        Log::debug('Alert:: HandleByUser missing user is '.$this->user_id);

        $user_user=User::find($user_id);

        $userinfo=0;

        foreach ($this->user->userwatchers as $userwatcher) {

            $watcher = User::find($userwatcher->watcher_id);

            if ($this->user_id === $user_id) {

                //userback
                Log::debug('preparing back info mail of ' . $this->user_id . ' for ' . $userwatcher->watcher_id);
                $details = [
                    'type' => 'userback',    //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
                    'to_address' => $watcher,
                    'title' => 'Hallo ' . $watcher->name,
                    'message' => $this->user->name . ' hat sich zurück gemeldet.',
                    'greetings' => 'Dein ' . env('APP_NAME', 'env app name missing') . ' Team',
                ];
                SendEmail::dispatch($details)->onQueue('emails');

                if ($userinfo==0) {
                    Log::debug('preparing back info mail of ' . $this->user_id . ' for himself');
                    $details = [
                        'type' => 'userselfback',    //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
                        'to_address' => $this->user,
                        'title' => 'Hallo ' . $this->user->name,
                        'message' => 'Du hast hat dich zurück gemeldet.',
                        'greetings' => 'Dein ' . env('APP_NAME', 'env app name missing') . ' Team',
                    ];
                    $userinfo++;
                    SendEmail::dispatch($details)->onQueue('emails');
                }

            } else {
                //handledalert
                Log::debug('preparing acknowledge alert info mail of ' . $this->user_id . ' for ' . $userwatcher->watcher_id);

                $details = [
                    'type' => 'handledalert',    //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
                    'to_address' => $watcher,
                    'title' => 'Hallo ' . $watcher->name,
                    'message' => $user_user->name . ' kümmert sich um '.$this->user->name,
                    'greetings' => 'Dein ' . env('APP_NAME', 'env app name missing') . ' Team',
                ];
                SendEmail::dispatch($details)->onQueue('emails');

                if ($watcher->id==$user_id) {
                    Log::debug('preparing info mail of ' . $user_id . ' for himself');
                    $details = [
                        'type' => 'userhandled',    //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
                        'to_address' => $this->user,
                        'title' => 'Hallo ' . $this->user->name,
                        'message' => $watcher->name.' kümmert sich um dich.',
                        'greetings' => 'Dein ' . env('APP_NAME', 'env app name missing') . ' Team',
                    ];
                    SendEmail::dispatch($details)->onQueue('emails');
                }

            }
        }

        if( $this->handled == 0 ) {
            $this->handled = 1;
            $this->handled_by = $user_id;
            $this->touch();
        }
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
