<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class DashController extends Controller
{
    public function statsCount(Request $request){
        $postsCount = Post::all()->count();
        $likesCount = Like::all()->count();
        $commentsCount = Comment::all()->count();
        $subscribersCount = Subscriber::all()->count();

        return json_encode([
            'total_posts'=>$postsCount,
            'total_likes'=>$likesCount,
            'total_comments'=>$commentsCount,
            'total_subscribers'=>$subscribersCount,

        ]);
    }
}
