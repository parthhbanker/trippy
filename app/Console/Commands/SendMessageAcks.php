<?php

namespace App\Console\Commands;

use App\Events\Ack as AppAck;
use App\Models\Ack;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class SendMessageAcks extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acks:send-message-acks {message_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Acks for a perticular message';

    /**
     * Execute the console command.
     */
    public function handle()
    {

       $acks = Ack::where('message_id',$this->argument('message_id'))->get();

        foreach ($acks as $ack) {
            broadcast(new AppAck($ack));
            $ack->attempts += 1;
            $ack->save();
        }


    }

    public function isolatableId(): string{

        return $this->argument('message_id');

    }

}
