<?php

namespace App\Http\Controllers;

use App\Jobs\SendSubscribersEmailJob;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{

    public function createPost(Request $request)
    {

        \Tinify\setKey("1VzK915ZyBgpW4YSZwKtvccYLZ6F1sMp");

        //todo compress thumbnail with tinyPNG api before upload
        $validator = Validator::make($request->all(), [
            'data.*.posted_by' => 'string|required',
            'data.*.title' => 'string|required',
            'data.*.active' => 'string|required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'data.*.blog_body' => 'string',
            'data.*.category' => 'string|required',
            'data.*.tags' => 'string',
        ]);
        $data = json_decode($request->data, true);


        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'result' => "An error occurred, $error",
            ], 403);
        }
        if ($data['blog_body'] == null) {
            return response()->json([
                'result' => "Blog content cannot be empty",
            ], 403);
        }
        if (Post::where('title', $data['title'])->first()) {
            return response()->json([
                'result' => "Title already exists",
            ], 403);
        }

        $files = $request->file('thumbnail');
        // Define upload path
        $destinationPath = public_path('/uploads/blog_thumbnails'); // upload path
        // Upload Orginal Image
        $postThumbnail = date('YmdHis') . "." . $files->getClientOriginalExtension();
        $files->move($destinationPath, $postThumbnail);
        //compress image
        $source=\Tinify\fromFile("$destinationPath/".$postThumbnail);
        $source->toFile("$destinationPath/".$postThumbnail);
        $insert['image'] = "$postThumbnail";

        $user = User::where('name', $data['posted_by'])->first();
        if ($user) {
            error_log('4');
            $user->posts()->create([
                'title' => $data['title'],
                'posted_by' => $data['posted_by'],
                'slug' => Str::slug($data['title']),
                'thumbnail' => "$postThumbnail",
                'active' => $data['active'],
                'blog_body' => $data['blog_body'],
                'category' => $data['category'],
                'tags' => $data['tags']
            ]);

            if ($data['active'] == 'Live') {
                $subscribers = Subscriber::orderBy('created_at', 'desc')->get();
                $details = [
                    'blog_title' => $data['title']
                ];
                $job = (new SendSubscribersEmailJob($details, $subscribers))
                    ->delay(Carbon::now()->addSeconds(2));
                dispatch($job);
            }

            if ($data['active'] == 'Live') {
                return response()->json([
                    'result' => 'Post published successfully, Sending emails to subscribers in the background.This might take a while'
                ], 201);
            }

            if ($data['active'] == 'Draft') {
                return response()->json([
                    'result' => 'Post drafted successfully, it will be hidden from users until you set it to live.',
                ], 201);
            }
        }
    }

    public function getAllPosts(Request $request)
    {
        $this->validate($request, [
            'request_type' => 'required|string',
            'paginate' => 'string',

        ]);
        if ($request->request_type == 'all_posts_admin') {
                $posts = Post::orderBy('created_at', 'desc')->get();
                return json_encode($posts);

        }
        if ($request->request_type == 'all_posts') {
            $cached_posts=Cache::get('all_posts');
            if ($cached_posts){
                return json_encode($cached_posts);
            }else{
                $posts = Post::orderBy('created_at', 'desc')->where('active', 'Live')->Paginate($request->paginate);
                Cache::put('all_posts', $posts, 300);
                return json_encode($posts);
            }
        }
        if ($request->request_type == 'home_posts') {
            $cached_posts=Cache::get('home_posts');
            if ($cached_posts){
                return json_encode($cached_posts);
            }else{
                $posts = Post::orderBy('created_at', 'desc')->where('active', 'Live')->take(6)->get(['slug','thumbnail','title']);
                Cache::put('home_posts', $posts, 300);
                return json_encode($posts);
            }
        }
        if ($request->request_type == 'all_posts_no_pagination') {
            if ($request->cache == 'no_cache') {
                $posts = Post::orderBy('created_at', 'desc')->where('active', 'Live')->get(['slug','thumbnail','title']);
                Cache::put('all_posts_no_pagination', $posts, 300);
                return json_encode($posts);
            }else{
                $cached_posts=Cache::get('all_posts_no_pagination');
                if ($cached_posts){
                    return json_encode($cached_posts);
                }else{
                    $posts = Post::orderBy('created_at', 'desc')->where('active', 'Live')->get(['slug','thumbnail','title']);
                    Cache::put('all_posts_no_pagination', $posts, 300);
                    return json_encode($posts);
                }
            }

        }
    }

    public function getPostsAndCategories(Request $request)
    {
        $cached_posts=Cache::get('all_posts_and_categories');
        $cached_categories=Cache::get('all_categories_and_posts');

        if ($cached_posts && $cached_categories){
            return([
                'posts'=>$cached_posts,
                'categories'=>$cached_categories
            ]);
        }else{
            $posts = Post::orderBy('created_at', 'desc')->take(2)->where('active', 'Live')->get(['slug','title']);
            $categories = Topic::orderBy('created_at', 'desc')->pluck('category');
            Cache::put('all_posts_and_categories', $posts, 300);
            Cache::put('all_categories_and_posts', $categories, 300);
            return([
                'posts'=>$posts,
                'categories'=>$categories
            ]);
        }
    }

    public function getPostsByCategory(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|string',
        ]);
        if ($request->request_type == 'all_posts') {
            $posts = Post::orderBy('created_at', 'desc')->where('category', $request->category)->get(['id','title','category']);
            $categories = Topic::orderBy('created_at', 'desc')->pluck('category');

            return([
                'posts'=>$posts,
                'categories'=>$categories
            ]);
        }

        $posts = Post::orderBy('created_at', 'desc')->where('category', $request->category)->Paginate(10);
        return json_encode($posts);
    }

    public function getPostsByAuthor(Request $request)
    {
        $this->validate($request, [
            'author' => 'required|string',
        ]);
        $posts = Post::orderBy('created_at', 'desc')->where('posted_by', $request->author)->get();
        return json_encode($posts);
    }

    public function getSinglePost(Request $request)
    {
        $this->validate($request, [
            'slug' => 'string|required',
        ]);
        //get previous and next blog
        $post = Post::where('slug', $request->slug)->first();
        $avatar = User::where('id', $post->user_id)->value('avatar');
        $previousTimeStamp = Post::where('created_at', '<', $post->created_at)->max('created_at');
        $previousPost = Post::where('created_at', $previousTimeStamp)->value('slug');
        $previousPostPostedBy = Post::where('created_at', $previousTimeStamp)->value('posted_by');

        $nextTimeStamp = Post::where('created_at', '>', $post->created_at)->min('created_at');
        $nextPost = Post::where('created_at', $nextTimeStamp)->value('slug');
        $nextPostPostedBy = Post::where('created_at', $nextTimeStamp)->value('posted_by');

        if ($post) {
            return json_encode([
                'avatar' => $avatar,
                'currentPost' => $post,
                'previousPost' => $previousPost,
                'nextPost' => $nextPost,
                'previousPostPostedBy' => $previousPostPostedBy,
                'nextPostPostedBy' => $nextPostPostedBy
            ]);
        } else {
            return response()->json([
                'table' => 'posts',
                'action' => 'get single post',
                'result' => 'post found'
            ]);
        }
    }


    public function updatePost(Request $request)
    {
        \Tinify\setKey("1VzK915ZyBgpW4YSZwKtvccYLZ6F1sMp");
        //todo compreess thumbnail with tinyPNG api before upload

        $validator = Validator::make($request->all(), [
            'data.*.logged_in_userId' => 'string|required',
            'data.*.post_id' => 'string|required',
            'data.*.posted_by_id' => 'string|required',
            'data.*.posted_by' => 'string|required',
            'data.*.title' => 'string|required|unique:posts',
            'data.*.active' => 'string|required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'data.*.blog_body' => 'required',
            'data.*.category' => 'string|required',
            'data.*.tags' => 'string',

        ]);
        $data = json_decode($request->data, true);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'result' => "An error occurred, $error",
            ], 403);
        }
        if ($data['blog_body'] == null) {
            return response()->json([
                'result' => "Blog content cannot be empty",
            ], 403);
        }

        $files = $request->file('thumbnail');
        // Define upload path
        $destinationPath = public_path('/uploads/blog_thumbnails'); // upload path
        // Upload Orginal Image
        $postThumbnail = date('YmdHis') . "." . $files->getClientOriginalExtension();
        $files->move($destinationPath, $postThumbnail);
        $source=\Tinify\fromFile("$destinationPath/".$postThumbnail);
        $source->toFile("$destinationPath/".$postThumbnail);
        $insert['image'] = "$postThumbnail";

        $user = User::find($data['posted_by_id']);
        if ($user) {
            if ($data['logged_in_userId'] == $data['posted_by_id']) {
                $user->posts()->where("id", $data['post_id'])->update([
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'thumbnail' => "$postThumbnail",
                    'active' => $data['active'],
                    'blog_body' => $data['blog_body'],
                    'category' => $data['category'],
                    'tags' => $data['tags']

                ]);

                if ($data['active'] == 'Live') {
                    return response()->json([
                        'result' => 'Post edited successfully.'
                    ], 201);
                }

                if ($data['active'] == 'Draft') {
                    return response()->json([
                        'result' => 'Post drafted successfully, it will be hidden from users until you set it to live.',
                    ], 201);
                }
            } else {
                return response()->json([
                    'result' => 'Failed, only the owner of the post can update the post'
                ], 403);
            }
        }
    }

    public function searchForBlog(Request $request)
    {
        $this->validate($request, [
            'search_item' => 'required|string',
        ]);
        $blog = Post::select('*')
            ->whereRaw("title like '%" . $request->search_item . "%' ")
            ->where('active', 'Live')
            ->cursorPaginate($request->paginate);

        return json_encode($blog);

    }

    public function deletePost(Request $request)
    {
        $this->validate($request, [
            'logged_in_userId' => 'string|required',
            'request_type' => 'required|string',
            'post_id' => 'required|string',
            'user_id' => 'required|string',
        ]);
        if ($request->request_type == 'single_post') {
            $post = Post::where('id', '=', $request->post_id)
                ->where('user_id', '=', $request->user_id)->get();

            if ($request->logged_in_userId==null){
                return response()->json([
                    'result' => 'Token Expired, Login'
                ], 401);
            }
            if ($request->logged_in_userId == $request->user_id && !$post->isEmpty()) {
                $post->each->delete();
            } else {
                return response()->json([
                    'result' => 'Failed, only the owner of the post can delete the post'
                ], 403);
            }
        }

    }

    public function uploadBlogImage(Request $request)
    {
        \Tinify\setKey("1VzK915ZyBgpW4YSZwKtvccYLZ6F1sMp");

        $files = $request->file('blog_image');
        // Define upload path
        $destinationPath = public_path('uploads/tinymce/blog_images'); // upload path
        // Upload Orginal Image
        $blogImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
        $files->move($destinationPath, $blogImage);
        $source=\Tinify\fromFile("$destinationPath/".$blogImage);
        $source->toFile("$destinationPath/".$blogImage);
        $insert['image'] = "$blogImage";
        return json_encode(['location' => "uploads/tinymce/blog_images/$blogImage"]);
    }
}
