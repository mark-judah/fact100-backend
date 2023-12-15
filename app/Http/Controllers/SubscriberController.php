<?php

namespace App\Http\Controllers;

use App\Jobs\NewSubscriberEmail;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    public function createSubscriber(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
        ]);
        $input['email'] = $request->input('email');

        if (Subscriber::where('email', $request->input('email'))->first()) {
            return response()->json([
                'result' => "You are already a subscriber.",
            ], 403);
        }
        Subscriber::create( $input );
        $job = (new NewSubscriberEmail($request->input('email')));
        dispatch($job);

        return response()->json([
            'result' => 'You have subscribed to Fact 100, you will receive a confirmation email shortly.'
        ], 201);
    }

    public function getSubscribers()
    {
        $subscribers = Subscriber::orderBy('created_at', 'desc')->get();
        return json_encode($subscribers);
    }


    public function deleteSubscriber(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
        ]);
        $subscriber = Subscriber::where('email', '=', $request->email)->get();
        if (!$subscriber->isEmpty()) {
            $subscriber->each->delete();
            return response()->json([
                'result' => 'User has been unsubscribed successfully.'
            ], 204);
        }
    }
}
