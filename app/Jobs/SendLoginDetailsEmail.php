<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendLoginDetailsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $details;

    public function __construct($user, $details)
    {
        $this->user = $user;
        $this->details = $details;
    }

    public function handle()
    {
        Mail::send('emails.login', ['details' => $this->details], function ($message) {
            $message->to($this->user->email)->subject('Login Details');
        });
    }
}
