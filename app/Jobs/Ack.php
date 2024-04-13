<?php

namespace App\Jobs;

use App\Events\Ack as AppAck;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Ack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message ;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message ;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // $acks = json_decode($this->message->acks);

        broadcast(new AppAck($this->message));

    }

}
