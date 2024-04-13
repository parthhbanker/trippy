<?php

namespace App\Console\Commands;

use App\Events\Ack as AppAck;
use App\Models\Ack;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class SendAllAcks extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acks:send-all-acks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all acks when attempts == 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $acks = Ack::where('attempts',0)->get();

        foreach ($acks as $ack) {
            broadcast(new AppAck($ack));
            $ack->attempts += 1;
            $ack->save();
        }

    }
}
