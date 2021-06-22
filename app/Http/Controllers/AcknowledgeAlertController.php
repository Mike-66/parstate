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

        $Info1='die Aufgabe für '.$alert->user_id;

        $infoarray=array($caption);
        array_push($infoarray, $todo);

        return view('acknowledgealert',  compact('infoarray'));
    }
}
