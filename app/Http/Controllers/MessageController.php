<?php

namespace App\Http\Controllers;

use App\Mail\ContactEmailResponse;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function createMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'result' => "An error occurred, $error",
            ], 403);
        }
        $message = new Message();
        $message->name = $request->input('name');
        $message->email = $request->input('email');
        $message->subject = $request->input('subject');
        $message->message = $request->input('message');
        $message->save();
        return response()->json([
            'result' => 'Your message has been sent successfully, we will respond shortly.'
        ], 201);
    }

    public function getMessages()
    {
        $messages = Message::orderBy('created_at', 'desc')->get();
        return json_encode($messages);
    }

    public function respondToMessage(Request $request)
    {
        $this->validate($request, [
            'receiver_name' => 'required|string',
            'receiver_email' => 'required|string',
            'response_message' => 'required|string',
        ]);

        $to_name = $request->get('receiver_name');
        $to_email = $request->get('receiver_email');

        $details=[
            'name' => $to_name,
            'subject'=>'Intrinsic Thoughts',
            'message'=>$request->get('response_message'),
        ];
        Mail::to($to_email)->send(new ContactEmailResponse($details));
    }

    public function deleteMessage(Request $request)
    {
        $this->validate($request, [
            'message_id' => 'required|string',
        ]);
            $message = Message::where('id', '=', $request->message);
            $message->each->delete();
            return response()->json([
                'table' => 'messages',
                'action' => 'delete message',
                'result' => 'success'
            ], 204);

    }

}
