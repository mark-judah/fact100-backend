<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function createComment(Request $request)
    {
        $this->validate($request, [
            'post_id' => 'required|string',
            'slug' => 'required|string',
            'comment' => 'required|string',
            'comment_by'=>'required|string',
        ]);
        $post = Post::find($request->post_id);

        $post->comments()->create([
            'slug' => $request->get('slug'),
            'comment' => $request->get('comment'),
            'comment_by'=> $request->get('comment_by'),
        ]);
        return response()->json([
            'table' => 'comments',
            'action' => 'create',
            'result' => 'success'
        ], 201);
    }

//change from slug to id because if a title is edited comments will disappear
    public function getComments(Request $request)
    {
        $this->validate($request, [
            'slug' => 'required|string',
        ]);
        $array = explode('/', $request->slug);
        $array_rev = array_reverse($array);
            $comments = Comment::where('slug', '=', $array_rev[0])->orderBy('created_at', 'desc')->get();
            return json_encode($comments);

    }

    public function getAllComments(Request $request){
        $comments = Comment::orderBy('created_at', 'desc')->get();
        return json_encode($comments);
    }

    public function deleteComment(Request $request)
    {
        $this->validate($request, [
            'request_type' => 'required|string',
            'comment_id' => 'required|string',
            'user_id' => 'required|string',
        ]);
        if ($request->request_type == 'single_comment') {
            $comment = Comment::where('id', '=', $request->comment_id)
                ->where('user_id', '=', $request->user_id)->get();
            $comment->each->delete();
            return response()->json([
                'table' => 'comments',
                'action' => 'delete single comment',
                'result' => 'success'
            ], 204);
        }
        if ($request->request_type == 'all_comments') {

        }
    }

}
