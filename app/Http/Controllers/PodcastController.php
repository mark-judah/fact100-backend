<?php

namespace App\Http\Controllers;

use App\Jobs\SendSubscribersEmailJob;
use App\Models\Podcast;
use App\Models\Subscriber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PodcastController extends Controller
{
    public function createPodcast(Request $request)
    {
        //todo compreess thumbnail with tinyPNG api before upload
        $validator = Validator::make($request->all(), [
            'data.*.posted_by' => 'string|required',
            'data.*.title' => 'string|required|unique:podcast',
            'data.*.about' => 'string|required',
            'data.*.season' => 'string|required|unique:podcast',
            'data.*.episode' => 'required|unique:podcast',
            'data.*.category' => 'string|required',
            'data.*status' => 'string',
            'podcast_thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'podcast_audio' => 'required', function ($attribute, $value, $fail) {
                if ($value->getClientOriginalExtension() != 'mp3') {
                    $fail(':attribute must be .mp3!');
                }
            }
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'action' => 'validate',
                'result' => 'failed',
                'errors'=>$error
            ], 403);
        }

        $files1 = $request->file('podcast_thumbnail');
        // Define upload path
        $destinationPath = public_path('/uploads/podcast_covers'); // upload path
        // Upload Orginal Image
        $podcastCover = date('YmdHis') . "." . $files1->getClientOriginalExtension();
        $files1->move($destinationPath, $podcastCover);
        $insert['image'] = "$podcastCover";

        $files2 = $request->file('podcast_audio');
        // Define upload path
        $destinationPath = public_path('/uploads/podcast_audio'); // upload path
        // Upload Orginal Image
        $podcastAudio = date('YmdHis') . "." . $files2->getClientOriginalExtension();
        $files2->move($destinationPath, $podcastAudio);
        $insert['audio'] = "$podcastAudio";

        $data = json_decode($request->data,true);

        $user = User::where('name',$data['posted_by']) -> first();
        if ($user) {
            $user->podcasts()->create([
                'title' => $data['title'],
                'about' => $data['about'],
                'season' =>  $data['season'],
                'episode' =>  $data['episode'],
                'category' =>  $data['category'],
                'status' =>  $data['status'],
                'cover_photo' => "$podcastCover",
                'audio' => "$podcastAudio",
                'posted_by' => $data['posted_by'],
            ]);

            if ($request->get('status') == 1) {
                $subscribers = Subscriber::orderBy('created_at', 'desc')->get();
                $details = [
                    'blog_title' => $request->get('title')
                ];
                $job = (new SendSubscribersEmailJob($details, $subscribers))
                    ->delay(Carbon::now()->addSeconds(2));
                dispatch($job);
            }

            if ($request->get('status') == 1) {
                return response()->json([
                    'table' => 'podcasts',
                    'action' => 'publish',
                    'result' => 'success',
                    'emails' => 'Sending emails to subscribers in the background.This might take a while'
                ], 201);
            }

            if ($request->get('status') == 0) {
                return response()->json([
                    'table' => 'podcasts',
                    'action' => 'save draft',
                    'result' => 'success'
                ], 201);
            }

        }
    }

    public function getAllPodcasts(Request $request)
    {
        $this->validate($request, [
            'request_type' => 'required|string',
            'paginate' => 'string',

        ]);
        if ($request->request_type == 'all_podcasts') {
            $podcast = Podcast::orderBy('created_at', 'desc')->where('status','Live')->cursorPaginate($request->paginate);
            return json_encode($podcast);
        }
        if ($request->request_type == 'all_podcasts_no_pagination') {
            $podcast = Podcast::orderBy('created_at', 'desc')->get();
            return json_encode($podcast);
        }
    }

    public function updatePodcast(Request $request)
    {
        //todo compreess thumbnail with tinyPNG api before upload
        $validator = Validator::make($request->all(), [
            'data.*.podcast_id' => 'string|required',
            'data.*.posted_by' => 'string|required',
            'data.*.title' => 'string|required|unique:podcast',
            'data.*.about' => 'string|required',
            'data.*.season' => 'string|required|unique:podcast',
            'data.*.episode' => 'required|unique:podcast',
            'data.*.category' => 'string|required',
            'data.*status' => 'string',
            'podcast_thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'podcast_audio' => 'required', function ($attribute, $value, $fail) {
                if ($value->getClientOriginalExtension() != 'mp3') {
                    $fail(':attribute must be .mp3!');
                }
            }
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'action' => 'validate',
                'result' => 'failed',
                'errors'=>$error
            ], 403);
        }

        $files1 = $request->file('podcast_thumbnail');
        // Define upload path
        $destinationPath = public_path('/uploads/podcast_covers'); // upload path
        // Upload Orginal Image
        $podcastCover = date('YmdHis') . "." . $files1->getClientOriginalExtension();
        $files1->move($destinationPath, $podcastCover);
        $insert['image'] = "$podcastCover";

        $files2 = $request->file('podcast_audio');
        // Define upload path
        $destinationPath = public_path('/uploads/podcast_audio'); // upload path
        // Upload Orginal Image
        $podcastAudio = date('YmdHis') . "." . $files2->getClientOriginalExtension();
        $files2->move($destinationPath, $podcastAudio);
        $insert['audio'] = "$podcastAudio";

        $data = json_decode($request->data,true);

        $user = User::where('name',$data['posted_by']) -> first();
        if ($user) {
            $user->podcasts()->where("id", $data['podcast_id'])->update([
                'title' => $data['title'],
                'about' => $data['about'],
                'season' =>  $data['season'],
                'episode' =>  $data['episode'],
                'category' =>  $data['category'],
                'status' =>  $data['status'],
                'cover_photo' => "$podcastCover",
                'audio' => "$podcastAudio",
                'posted_by' => $data['posted_by'],
            ]);

            if ($data['status'] == 'Live') {
                return response()->json([
                    'table' => 'podcasts',
                    'action' => 'update & publish',
                    'result' => 'success'
                ], 201);
            }

            if ($data['status'] == 'Pending') {
                return response()->json([
                    'table' => 'podcasts',
                    'action' => 'update & save draft',
                    'result' => 'success'
                ], 201);
            }

        }else {
            return response()->json([
                'table' => 'podcast',
                'action' => 'update',
                'result' => 'access denied, only the owner of the podcast can update the podcast'
            ], 403);
        }

    }

}
