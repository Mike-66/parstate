<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParstateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::debug('ParstateMail::build type: '.$this->details['type'].', message: '.$this->details['message']);
        switch( $this->details['type'] ) {
            case 'usermissing': {
                return $this->subject(env('APP_NAME', 'env app name missing').' Benutzer vermisst')
                    ->view('emails.usermissing');
            }
            case 'userback':{
                return $this->subject(env('APP_NAME', 'env app name missing').' Benutzer zurück')
                    ->view('emails.default');
            }
            case 'handledalert':{
                return $this->subject(env('APP_NAME', 'env app name missing').' Aufgabe übernommen')
                    ->view('emails.default');
            }
            case 'useralertinfo':{
                return $this->subject(env('APP_NAME', 'env app name missing').' Alarm Meldungen versichickt')
                    ->view('emails.default');
            }



            default:{
                Log::debug('ParstateMail::build unknown type');
            }

        }
    }
}
