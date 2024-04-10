<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Webhook extends Controller
{

    public function pusher(Request $request){

        $event = $request->input('event') ;
        $channel = $request->input('channel');
        $data = json_decode($request->input('data'));
        Log::error("****************Pusher se aaya**************");
        // TODO: ack handel

        // TODO: new message handel

    }

}
