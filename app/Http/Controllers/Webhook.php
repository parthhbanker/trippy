<?php

namespace App\Http\Controllers;

use App\Events\Ack;
use App\Models\Ack as AppAck;
use App\Models\GroupUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Webhook extends Controller
{

    public function pusher(Request $request){

        $event = $request->input('event') ;
        // $channel = $request->input('channel');
        $data = json_decode($request->input('data'));
        $user = User::find(Auth::id());

        Log::error("***************Web Hook pe aaya**************");

        switch ($event) {
            case 'new-message':

                $message = new Message();
                $message->data = $data->message;
                $message->save();

                if($data->group){

                    $users = GroupUser::where('group_id',$data->group)
                    ->where('user_id','!=',Auth::id())
                    ->get('user_id')->toarray();

                    // TODO: create acks


                    foreach ($users as $usr) {

                        AppAck::create([

                            'user_id' => $usr,
                            'message_id' => $message->id

                        ]);

                    }


                }else{

                    AppAck::create([

                        'user_id' => $data->message->to,
                        'message_id' => $message->id
                    ]);

                }



                // TODO: handel media upload

                // TODO: return ack for message recived

                break;

            case 'ack':

                $message = New Message();
                $message->data = $data->message;
                $message->save();

                // TODO: get the ack-id and delete it from the acks table

                AppAck::create([

                    'message_id' => $message->id,
                    'user_id' => $data->message->from

                ]);

                break;

            case 'ack-status':

                $message = new Message();
                $message->data = $data->message;
                $message->save();

                AppAck::create([

                    'message_id' => $message->id,
                    'user_id' => $data->message->from,

                ]);


                break;

            case 'connected':
                // SHOULD WE MARK CONNECTED TO EVERY EVENT OR JUST TO THE SPECIFIC ??
                $user->connected();
                $user->save();

                // TODO: when a client gets connected
                // send all the messages and acks for that user
                // TODO: call the command for specific user acks
                // channel-user.id

                Artisan::call('acks:send-user-acks ' . $user->id);

                break;

        }


    }

}
