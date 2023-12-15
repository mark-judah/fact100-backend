<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;

class NewSubscriberEmail extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $email;

    public function __construct($email)
    {
        $this->email=$email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        $name=strstr($this->email, '@', true);
//        request()->session()->put('sub-name',$name);
//        //fetch session in email view file to send email with users name
        $input['email']=$this->email;

        Mail::send('newSubscriberEmail', [], function($message) use($input){
            $message->to($input['email'])
                ->subject("Fact 100 Subscription");
        });


    }
}
