<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Alert;
use App\Jobs\SendEmail;

class CheckSendAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:sendalert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks alert table and queues emails to user watchers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::debug('checksendalert fired');
        $alerts=Alert::whereNull("handled")->get();
        foreach ($alerts as $alert) {
            //eloquent alert loads user
            Log::debug('found eloquent alert for user id '.$alert->user->id.', scanning for watchers manually');
            foreach ($alert->user->userwatchers as $userwatcher) {
                $watcher=User::find($userwatcher->watcher_id);
                Log::debug('user id '.$alert->user->id.' mail to watcher '.$watcher->id);
                $details = [
                    'watcher' => $watcher,
                    'title' => 'Hallo '.$watcher->name,
                    'message' => $alert->user->name.' hat sich am '.$alert->user->updated_at.' letzmalig gemeldet.',
                    'ackowledge' => ' Durch Klick auf den Link übernimmst du die Aufgabe:',
                    'ackowledge_url' => env('APP_URL', 'env app url missing').'/acknowledgealert/'.$alert->uuid,
                ];
                SendEmail::dispatch($details)->onQueue('emails');
            }
        }

        /*

    Log::debug('userwatchers');
    foreach ($missing_user->userwatchers as $userwatcher) {
        $watcher=User::find($userwatcher->watcher_id);
        Log::debug('user id '.$missing_user->id.' mail to watcher '.$userwatcher->watcher_id);
        $details = [
            'watcher' => $watcher,
            'title' => 'Hallo '.$watcher->name,
            'message' => $missing_user->name.' hat sich am '.$missing_user->updated_at.' letzmalig gemeldet.',
            'ackowledge' => ' Durch Klick auf den Link übernimmst du die Aufgabe:',
            'ackowledge_url' => env('APP_URL', 'env app url missing').'/acknowledge',
        ];
        SendEmail::dispatch($details)->onQueue('emails');
    }
}
*/

        return 0;
    }
}
