<?php

namespace App\Http\Controllers;

use App\Events\Ack;
use App\Models\Ack as AppAck;
use App\Models\GroupUser;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

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
                $message->data = $data;
                $message->save();

                if(isset($data->message->group_id)){

                    $users = GroupUser::where('group_id',$data->message->group)
                    ->where('user_id','!=',Auth::id())
                    ->get('user_id')->toarray();

                    foreach ($users as $usr) {

                        if($usr == $user->id){
                            continue;
                        }

                        AppAck::create([

                            'user_id' => $usr,
                            'message_id' => $message->id

                        ]);

                    }


                }else{

                    AppAck::create([

                        'user_id' => $data->message->receipient_id,
                        'message_id' => $message->id
                    ]);

                }

                // TODO: Media upload
                //
                if($request->hasFile('media')){

                    // TODO: upload the media and generate links
                    //
                    // TODO: set the links in the message

                    $files = $request->file('media');

                    $media = [];

                    foreach ($files as $file) {


                        $file_name = time() . rand(999,9999);
                        $file_name_ext = $file_name . '.' . $file->getClientOriginalExtention();
                        $file->storeAs('public/chat/media',$file_name_ext);

                        $file_type = $file->getMimeType();

                        if (strpos($file_type, 'image') !== false) {
                            $type = 'image';
                        } else if (strpos($file_type, 'video') !== false) {
                            $type = 'video';
                        } else if (strpos($file_type, 'audio') !== false) {
                            $type = 'audio';
                        } else {
                            $type = 'file';
                        }



                        Media::create([
                            'url' => asset(Storage::url('public/chat/media' . $file_name_ext)),
                            'media_type' => $type,
                            'name' => $file->getClientOriginalName(),
                            'sent_by' => Auth::id(),
                            'group' => $data->message->group,
                            'sent_to' => $data->message->receipient_id
                        ]);


                        $media[] = [
                            'type' => $type,
                            'url' => asset(Storage::url('public/chat/media' . $file_name_ext)),
                            'thumbnail' => $this->generateThumbnail('public/chat/media/'.$file_name_ext , $type , $file_name)
                        ];

                    }

                    $data->message->media = $media;

                }


                Artisan::call('acks:send-message-acks ' . $message->id);
                $data->message->message_id = $message->id;
                $data->message->delivered_at = time();
                $message->data = $data;
                $message->save();

                return response()->json([ 'message' => 'message recived' , 'body' => $message->data]);

                break;

            case 'ack':

                $message = New Message();
                $message->data = $data->message;
                $message->save();

                if($user->id == $data->message->sender_id){

                    $ack = Ack::find($data->ack_id);

                    if($ack){
                        $ack->delete();
                    }

                }else{

                    AppAck::create([

                        'message_id' => $message->id,
                        'user_id' => $data->message->sender_id

                    ]);

                }

                break;

            case 'connected':
                // SHOULD WE MARK CONNECTED TO EVERY EVENT OR JUST TO THE SPECIFIC ??
                // $user->connected();
                // $user->save();
                Artisan::call('acks:send-user-acks ' . $user->id);

                break;

        }


    }

     public function generateThumbnail($path , $type , $file_name)
     {
        // TODO: install all the dependencies

         $thumbnailPath = 'chat/media/thumbnails/' . $file_name . '.jpg';

         if($type == 'image'){

            $image = Image::make($path);
            $image->fit(100,100);
            $image->save($thumbnailPath);

         }else{

            $ffmpeg = FFMpeg::fromDisk('public')
                ->open($path)
                ->getFrameFromSeconds(0)
                ->export()
                ->toDisk('public')
                ->save($thumbnailPath);

         }

        return asset(Storage::url($thumbnailPath));
    }


}
