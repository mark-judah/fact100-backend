<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;

class SendSubscribersEmailJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $details;
    public $subscribers;

    public function __construct($details,$subscribers)
    {
        $this->details=$details;
        $this->subscribers=$subscribers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $input['subject'] = $this->details['blog_title'];

        foreach($this->subscribers as $key=>$value){
            $input['email'] = $value->email;
            Mail::send('newPostAlert', [], function($message) use($input){
                $message->to($input['email'])
                    ->subject($input['subject']);
            });
        }
    }
}
