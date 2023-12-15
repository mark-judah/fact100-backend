<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeToggle(Request $request)
    {
        $this->validate($request, [
            'liked_by' => 'required|string',
            'blog_slug' => 'required|string',
        ]);
        $array = explode('/', $request->blog_slug);
        $array_rev = array_reverse($array);
        $post = Post::where('slug', '=', $array_rev[0])->first();
        $like = Like::where('liked_by', '=', $request->liked_by)
            ->where('post_id', '=', $post->id)->get();
        if (!$like->isEmpty()) {
            $like->each->delete();
            return response()->json([
                'table' => 'likes',
                'action' => 'delete',
                'result' => 'success'
            ], 200);
        }
        $post->likes()->create([
            'liked_by' => $request->get('liked_by'),
            'blog_slug' => $request->get('blog_slug')
        ]);
        return response()->json([
            'table' => 'likes',
            'action' => 'create',
            'result' => 'success'
        ], 201);


    }

    public function likeAvailable(Request $request)
    {
        $this->validate($request, [
            'liked_by' => 'required|string',
            'blog_slug' => 'required|string',
        ]);
        $array = explode('/', $request->blog_slug);
        $array_rev = array_reverse($array);
        $post = Post::where('slug', '=', $array_rev[0])->first();
        $likesCount = Like::where('blog_slug', '=', $request->blog_slug)->count();


        $like = Like::where('liked_by', '=', $request->liked_by)
            ->where('post_id', '=', $post->id)->get();
        if (!$like->isEmpty()) {
            return response()->json([
                'result' => 'like found',
                'likesCount' => $likesCount
            ], 200);
        } else {
            return response()->json([
                'result' => 'like not found',
                'likesCount' => $likesCount
            ], 200);
        }

    }

    public function getAllLikes(Request $request)
    {
        $this->validate($request, [
            'request_type' => 'required|string',
        ]);
        if ($request->request_type == 'all_likes') {
            $likes = Like::orderBy('created_at', 'desc')->get();
            return json_encode($likes);
        }
        if ($request->request_type == 'specific_users_likes') {
            $this->validate($request, [
                'liked_by' => 'required|string',
            ]);
            $likes = Like::where('user_id', $request->user_id)->get();
            return json_encode($likes);
        }
    }

    public function deleteLike(Request $request)
    {
        $this->validate($request, [
            'request_type' => 'required|string',
        ]);
        if ($request->request_type == 'all_likes') {
            $like = Like::all();
            $like->each->delete();
            return response()->json([
                'table' => 'likes',
                'action' => 'delete all likes',
                'result' => 'success'
            ], 204);
        }
    }


}
