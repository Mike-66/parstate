<?php

namespace App\Http\Controllers;

use App\Models\ParstateDefine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Parstate;
use App\Models\User;
use App\Models\Alert;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParstatesController extends Controller
{
    public function create(Request $request)
    {
        DB::update('SET time_zone = ?', [$request->user()->timezone]);

        //QueryLog
        DB::connection()->enableQueryLog();

        $greeting="Hallo";

        $user=User::find($request->user()->id);

        $caption=$greeting." ".$user->name.", ";

        if ( $user->parstate_id == '' ) {
            $caption=$caption."melde bitte deine Lage";
            $laststate=0;
        }
        else {
            $caption=$caption."deine letzte Meldung war am " . $user->parstate->created_at;
            $laststate=$user->parstate->parstate_define_id;
        }

        //QueryLog
        $queries = DB::getQueryLog();
        foreach ($queries as $query) {
            Log::debug($query);
        }

        $question="Wie ist die Lage ?";

        $infoarray=array($caption);
        array_push($infoarray, $question);
        array_push($infoarray, $laststate);
        array_push($infoarray, route('parstatepost'));

        try {
            $parstatedefines = ParstateDefine::orderBy('id','asc')->pluck('description', 'id');
        }

        catch (ErrorException $e) {
            $parstatedefines = NULL;
        }

        return view('submitparstate',  compact('parstatedefines'), compact('infoarray'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
                        'parstate_define_id' => 'required|integer|between:1,6',
        ]);

        $parstate=new Parstate($data);
        $parstate->user_id=$request->user()->id;
        $parstate = tap($parstate)->save();

        $user=User::find($request->user()->id);
        if( $user->alert_id > 0 ){
            $alert=Alert::find($user->alert_id);
            $alert->HandleByUser($user->id);
            $user->alert_id=null;
            Log::debug('missing user '.$user->name.' is back');
        }
        $user->parstate_id=$parstate->id;
        $user->touch();

        //once dashboard is more populated, redirect to home could make sense
        //return redirect('/home');
        //since submit parstate is the only task, we will stay here
        return redirect(route('parstateget'));
    }
}
