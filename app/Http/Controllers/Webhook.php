<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Webhook extends Controller
{

    public function pusher(Request $request){

        $event = $request->input('event') ;
        $channel = $request->input('channel');
        $data = json_decode($request->input('data'));

        // TODO: ack handel

        // TODO: new message handel

    }

}
