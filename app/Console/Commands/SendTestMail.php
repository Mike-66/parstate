<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test';

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
        $details = [

            'title' => 'Mail from '.env('APP_NAME', 'env app name missing'),

            'body' => 'This is for testing email using smtp'

        ];

        \Mail::to('banane@zitorn.de')->send(new \App\Mail\ParstateAlarm($details));

        return 0;
    }
}
