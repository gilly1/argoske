<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class sendMails extends Mailable implements ShouldQueue
{
    
    public $fromEmail;
    public $name;
    public $subject;
    public $body;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($from,$name, $subject, $body,$route = null,$pdf = null)
    {
        $this->fromEmail = $from;
        $this->name = $name;
        $this->subject = $subject;
        $this->body = $body;
        $this->route = $route;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->pdf){
            return $this->from($this->fromEmail,$this->name)->view('send-mails.email')
                                                    ->subject($this->subject)
                                                    ->with('body',$this->body)
                                                    ->with('route',$this->route)
                                                    ->attach(storage_path('app/public/emails/'.$this->pdf.'.pdf'), [
                                                        'as' => 'invoice.pdf',
                                                        'mime' => 'application/pdf'
                                                    ]);

        }else{
            return $this->from($this->fromEmail,$this->name)->view('send-mails.email')
                                                    ->subject($this->subject)
                                                    ->with('body',$this->body)
                                                    ->with('route',$this->route);            
        }
        
    }
}

