<?php

namespace App\Console\Commands;

use App\Events\Ack as AppAck;
use App\Models\Ack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendUserAcks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acks:send-user-acks {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send User specific Acks (typically called when a user connects)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = $this->argument('user');

        Log::error('Commanddddd user : ' . $user );

        $acks = Ack::where('user_id',$user)->get();

        foreach ($acks as $ack) {
            broadcast(new AppAck());
            $ack->attempts += 1;
            $ack->save();
        }

    }
}
