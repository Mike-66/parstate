<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Parstate;
use App\Models\User;
use App\Models\Alert;


use Illuminate\Support\Facades\DB;

class AcknowledgeAlertController extends Controller
{
    public function acknowledge(Request $request, Alert $alert) {
        Log::debug('AcknowledgeAlertController:acknowledge fired');

        $greeting="Hallo";
        $user=User::find($request->user()->id);
        $caption=$greeting." ".$user->name.", ";

        $missing_user=User::find($alert->user_id);
        $Info1='Du hast die Aufgabe, dich um '.$missing_user->name.' zu kümmern übernommen.';
        $Info2='Vielen Dank';
        $Info3='Dein '.env('APP_NAME', 'env app name missing')." Team";

        if ($alert->handled === 1) {
            if( $alert->handled_by === $request->user()->id ) {
                $Info1='Du hast ';
            }
            else {
                $handled_user=User::find($alert->handled_by);
                $Info1=$handled_user->name.' hat ';
            }
            $Info1=$Info1.' die Aufgabe bereits am '.$alert->updated_at.' übernommen.' ;
        }
        else {
            $alert->HandleByUser($request->user()->id);
        }

        $infoarray=array($caption);
        array_push($infoarray, $Info1);
        array_push($infoarray, $Info2);
        array_push($infoarray, $Info3);

        return view('acknowledgealert',  compact('infoarray'));
    }
}
