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
        Log::debug('checksendalert fired, user_missing_alarm_repeat_delay = '.config('parstate.user_missing_alarm_repeat_delay'));

        //QueryLog
        if ( config('parstate.sql_debug')  == 'ON' ) {
            DB::connection()->enableQueryLog();
        }

        //$alerts=Alert::whereNull("handled")
        //            ->where('updated_at', '<', \Carbon\Carbon::now()->subSeconds(config('parstate.user_missing_alarm_repeat_delay'))->toDateTimeString())

        //the pure eloquent attempt
        //$alerts=Alert::whereNull("handled")
        //            ->where(function($q) {
        //                $q->Where('alerts.updated_at' = 'alerts.created_at') //after creation
        //                ->orWhere('updated_at', '<', \Carbon\Carbon::now() //at any subsequent run
        //                        //->subSeconds(env('USER_MISSING_ALARM_REPEAT_DELAY', 1800))->toDateTimeString());
        //                ->subSeconds(config('parstate.user_missing_alarm_repeat_delay'))->toDateTimeString());
        //           })
        //           ->get();

        //the eloquent/raw mixed attempt
        $alerts=Alert::whereNull("handled")
            ->where(function($q) {
                $condition='alerts.updated_at = alerts.created_at';
                $condition=$condition.' OR ';
                $condition=$condition.'TIMESTAMPDIFF(SECOND, updated_at, NOW()) >'.config('parstate.user_missing_alarm_repeat_delay');
                $q->WhereRaw($condition);
            })
            ->get();

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
                    'message' => $alert->user->name.' hat sich am '.$alert->user->parstate->CreatedAtTZ($alert->user->timezone).' letzmalig gemeldet.',
                    'ackowledge' => ' Durch Klick auf den Link übernimmst du die Aufgabe:',
                    'ackowledge_url' => route('acknowledge',$alert->uuid),
                    'greetings' => 'Dein '.env('APP_NAME', 'env app name missing').' Team',
                ];
                SendEmail::dispatch($details)->onQueue('emails');
            }
            if ($watchercount === 0){
                Log::debug('no watcher found for '.$alert->user->id);
            }
            else {
                Log::debug('user id '.$alert->user->id.' info mail to user ');
                $details = [
                    'type' => 'useralertinfo', //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
                    'to_address' => $alert->user,
                    'title' => 'Hallo '.$alert->user->name,
                    'message' => 'Es wurden soeben '.$watchercount.' Meldungen an deine Beobachter versickt',
                    'greetings' => 'Dein '.env('APP_NAME', 'env app name missing').' Team',
                ];
                SendEmail::dispatch($details)->onQueue('emails');
            }

            $alert->touch();
        }

        //QueryLog
        if ( config('parstate.sql_debug')  == 'ON' ) {
            $queries = DB::getQueryLog();
            $last_query = end($queries);
            Log::debug($last_query);
        }

        return 0;
    }
}
