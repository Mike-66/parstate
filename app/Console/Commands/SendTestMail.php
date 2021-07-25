<?php

namespace App\Console\Commands;

use App\Jobs\SendEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\User;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send test mail';

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
        try {
            $email=$this->option('email');
            $user=User::where('email','like',$email)->first();
        }

        catch (\Exception $e) {
            printf("%s\n","'please provide valid email");
            return;
        }

        $details = [
            'type' => 'test', //will be examined in ParstateMail.php to set subject and redirect to corresponding blade
            'to_address' => $user->email,
            'title' => 'Hallo '.$user->name,
            'message' => 'Das ist eine Test Mail',
            'greetings' => 'Dein '.env('APP_NAME', 'env app name missing').' Team',
        ];
        SendEmail::dispatch($details)->onQueue('emails');

        return 0;
    }
}
