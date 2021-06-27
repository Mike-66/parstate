<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
        //QueryLog
        DB::connection()->enableQueryLog();

        Log::debug('checksendalert fired');
        //$alerts=Alert::whereNull("handled")
        //            ->where('updated_at', '<', \Carbon\Carbon::now()->subSeconds(env('USER_MISSING_ALARM_REPEAT_DELAY', 1800))->toDateTimeString())

        //the pure eloquent attempt
        //$alerts=Alert::whereNull("handled")
        //            ->where(function($q) {
        //                $q->Where('alerts.updated_at' = 'alerts.created_at') //after creation
        //                ->orWhere('updated_at', '<', \Carbon\Carbon::now() //at any subsequent run
        //                        //->subSeconds(env('USER_MISSING_ALARM_REPEAT_DELAY', 1800))->toDateTimeString());
        //                ->subSeconds(env('USER_MISSING_ALARM_REPEAT_DELAY', 10))->toDateTimeString());
        //           })
        //           ->get();

        //the pure eloquent attempt
        $alerts=Alert::whereNull("handled")
            ->where(function($q) {
                $condition='alerts.updated_at = alerts.created_at';
                $condition=$condition.' OR ';
                $condition=$condition.'TIMESTAMPDIFF(SECOND, updated_at, NOW()) >'.env('USER_MISSING_ALARM_REPEAT_DELAY', 1800);
                $q->WhereRaw($condition);
            })
            ->get();

        //QueryLog
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::debug($last_query);

        foreach ($alerts as $alert) {
            //eloquent alert loads user
            Log::debug('found eloquent alert for user id '.$alert->user->id.', scanning for watchers manually');
            $watchercount=0;
            foreach ($alert->user->userwatchers as $userwatcher) {
                $watchercount++;
                $watcher=User::find($userwatcher->watcher_id);
                Log::debug('user id '.$alert->user->id.' mail to watcher '.$watcher->id);
                $details = [
                    'type' => 'usermissing',    //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
                    'to_address' => $watcher,
                    'title' => 'Hallo '.$watcher->name,
                    'message' => $alert->user->name.' hat sich am '.$alert->user->updated_at.' letzmalig gemeldet.',
                    'ackowledge' => ' Durch Klick auf den Link übernimmst du die Aufgabe:',
                    'ackowledge_url' => route('acknowledge',$alert->uuid),
                    'greetings' => 'Dein '.env('APP_NAME', 'env app name missing').' Team',
                    //'ackowledge_url' => env('APP_URL', 'env app url missing').'/acknowledgealert/'.$alert->uuid,
                ];
                SendEmail::dispatch($details)->onQueue('emails');
            }
            if ($watchercount === 0){
                Log::debug('no watcher found for '.$alert->user->id);
            }
            $alert->touch();
        }

        return 0;
    }
}
