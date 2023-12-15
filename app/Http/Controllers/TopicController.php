<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function createTopic(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|string|unique:topics',
        ]);
        if (Topic::where('category', $request->category)->first()) {
            return response()->json([
                'result' => "Category already exists",
            ], 403);
        }
        Topic::create([
            'category' => $request->get('category'),
        ]);
        return response()->json([
            'result' => "Category added successfully"
        ], 201);
    }

    public function getAllCategories()
    {
        $categories = Topic::orderBy('created_at', 'desc')->get();
        return json_encode($categories);
    }



    public function changePostCategory(Request $request)
    {
        $post = Post::find($request->get('postId'));
        $post->where("id", $request->get('postId'))->update([
            'category' => $request->get('new_category'),
        ]);
        return response()->json([
            'result' => "The category for the post '$post->title' has been changed successfully"
        ], 201);

    }

    public function deleteCategory(Request $request)
    {
        $count=Post::where('category','=',$request->get('old_category'))->count();

        if ($count>0){
            return response()->json([
                'result' => "Failed! Move posts to a new category first."
            ], 201);
        }else{
            $topic=Topic::where('category','=',$request->get('old_category'))->get();
            $topic->each->delete();
            return response()->json([
                'result' => "The category has been deleted successfully."
            ], 201);
        }

    }

    public function getCategoryDescription(Request $request)
    {
        $description = Topic::where("category", $request->get('category'))->get(['description']);
        return json_encode($description);

    }

    public function editCategoryDescription(Request $request)
    {
        Topic::where("category", $request->get('category'))->update([
            'description' => $request->get('new_description'),
        ]);
        return response()->json([
            'result' => "The category for the category '$request->get('category')' has been changed successfully"
        ], 201);

    }
}
