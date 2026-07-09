<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\DocumentApprovalMail ;
use Illuminate\Support\Facades\Mail;


class SendApprovalEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $recipient;
    public $name;
    public $document_id;
    public $approval_url;
    public $cc;  
    public $approve_msg;  

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recipient, $name, $document_id, $approval_url, $cc, $approve_msg)
    {
        $this->recipient = $recipient;
        $this->name = $name;
        $this->document_id = $document_id;
        $this->approval_url = $approval_url;
        $this->cc = $cc;  
        $this->approve_msg = $approve_msg;  
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = Mail::to($this->recipient)
            ->cc($this->cc) 
            ->send(new DocumentApprovalMail($this->name, $this->document_id, $this->approval_url, $this->approve_msg));  
    }


}
