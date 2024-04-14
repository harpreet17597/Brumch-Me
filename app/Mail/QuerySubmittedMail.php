<?php

namespace App\Mail;

use App\Models\Query;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
class QuerySubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
    */
    public Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Build the message.
     *
     * @return $this
    */
    public function build()
    { 
        return $this->from($this->query->user->email,$this->query->user->name)
                    ->replyTo($this->query->user->email,$this->query->user->name)
                    ->subject('Help & Support')
                    ->markdown('email.query');
    }
}

