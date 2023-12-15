<?php
use Illuminate\Support\Facades\Artisan;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



$router->group(['prefix' => 'api'], function () use ($router)
{
    $router->post('login', 'UserController@login');
    $router->post('createMessage', 'MessageController@createMessage');
    $router->post('searchForBlog', 'PostController@searchForBlog');
    $router->post('getAllPosts', 'PostController@getAllPosts');
    $router->post('getPostsAndCategories', 'PostController@getPostsAndCategories');
    $router->post('getAllPodcasts', 'PodcastController@getAllPodcasts');
    $router->get('getAboutUs', 'AboutController@getAboutUs');
    $router->get('getAllCategories', 'TopicController@getAllCategories');
    $router->post('getPostsByCategory', 'PostController@getPostsByCategory');
    $router->post('getSinglePost', 'PostController@getSinglePost');
    $router->post('likeToggle', 'LikeController@likeToggle');
    $router->post('likeAvailable', 'LikeController@likeAvailable');
    $router->post('createComment', 'CommentController@createComment');
    $router->post('getComments', 'CommentController@getComments');
    $router->post('createSubscriber', 'SubscriberController@createSubscriber');
    $router->post('getPostsByAuthor', 'PostController@getPostsByAuthor');
    $router->post('requestEssayQuote', 'EssayController@createMessage');
    $router->post('register', 'UserController@register');
    $router->post('getCategoryDescription', 'TopicController@getCategoryDescription');


});

$router->group(['middleware' => 'auth','prefix' => 'api'], function ($router)
{
    $router->get('currentUser', 'UserController@currentUser');
    $router->get('allUsers', 'UserController@getAllUsers');
    $router->post('createPost', 'PostController@createPost');
    $router->post('updatePost', 'PostController@updatePost');
    $router->post('deletePost', 'PostController@deletePost');
    $router->post('createPodcast', 'PodcastController@createPodcast');
    $router->post('updatePodcast', 'PodcastController@updatePodcast');
    $router->post('getAllLikes', 'LikeController@getAllLikes');
    $router->post('deleteComment', 'CommentController@deleteComment');
    $router->post('deleteLikes', 'LikeController@deleteLike');
    $router->get('getMessages', 'MessageController@getMessages');
    $router->post('respondToMessage', 'MessageController@respondToMessage');
    $router->post('createAboutUs', 'AboutController@createAboutUs');
    $router->post('editAboutUs', 'AboutController@editAboutUs');
    $router->post('createCategory', 'TopicController@createTopic');
    $router->post('uploadBlogImage', 'PostController@uploadBlogImage');
    $router->post('uploadAboutUsImage', 'AboutController@uploadAboutUsImage');
    $router->post('statsCount', 'DashController@statsCount');
    $router->get('getSubscribers', 'SubscriberController@getSubscribers');
    $router->post('deleteSubscriber', 'SubscriberController@deleteSubscriber');
    $router->get('getProfiles', 'UserController@getProfiles');
    $router->post('updateProfile', 'UserController@UpdateProfile');
    $router->get('getEssayRequests', 'EssayController@getEssayRequests');
    $router->post('changePostCategory', 'TopicController@changePostCategory');
    $router->post('deleteCategory', 'TopicController@deleteCategory');
    $router->post('editCategoryDescription', 'TopicController@editCategoryDescription');
    $router->get('getAllComments', 'CommentController@getAllComments');
});
