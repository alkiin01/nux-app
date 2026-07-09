<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentApprovalMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $document_id;
    public $approval_url;
    public $approve_msg;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $document_id, $approval_url, $approve_msg)
    {
        $this->name = $name;
        $this->document_id = $document_id;
        $this->approval_url = $approval_url;
        $this->approve_msg = $approve_msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.document_approval')->subject('Document Approval Required');
    }
}
