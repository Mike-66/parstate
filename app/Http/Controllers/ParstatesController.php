<?php

namespace App\Http\Controllers;

use App\Models\ParstateDefine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Parstate;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class ParstatesController extends Controller
{
    public function create(Request $request)
    {
        $greeting="Hallo";

        $user=User::find($request->user()->id);

        $caption=$greeting." ".$user->name.", ";

        if ( $user->parstate_id == '' ) {
            $caption=$caption."melde bitte deine Lage";
            $laststate=0;
        }
        else {
            $caption=$caption."deine letzte Meldung war am " . $user->updated_at;
            $laststate=$user->parstate_id;
        }

        $todo="Wie ist die Lage ?";

        $infoarray=array($caption);
        array_push($infoarray, $todo);
        array_push($infoarray, $laststate);

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
                        'parstate_id' => 'required|integer|between:1,6',
        ]);

        $parstate=new Parstate($data);
        $parstate->user_id=$request->user()->id;
        $parstate = tap($parstate)->save();

        $user=User::find($request->user()->id);
        $user->parstate_id=$parstate->parstate_id;
        $user->save();

        //once dashboard is more populated, redirect to home could make sense
        //return redirect('/home');
        //since submit parstate is the only task, we will stay here
        return redirect('/parstate/submit');
    }
}
