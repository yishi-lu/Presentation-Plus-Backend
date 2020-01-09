<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth router
Route::group(['prefix' => 'auth', 'name' => 'api.auth.'], function () {
    Route::post("/register", ["name" => "register", "uses" => "Api\\AuthController@register"]);
    Route::post("/login", ["name" => "login", "uses" => "Api\\AuthController@login"]);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth', 'name' => 'api.auth.'], function(){
    Route::get("/profile", ["name" => "profile", "uses" => "Api\\AuthController@profile"]);
    Route::get("/logout", ["name" => "logout", "uses" => "Api\\AuthController@logout"]);

});

//Post router
Route::group(['prefix' => 'post', 'name' => 'api.post.'], function () {
    Route::get("/fetchUserPost/{id}", ["name" => "fetchUserPost", "uses" => "Api\\PostsController@fetchUserPosts"]);
    Route::get("/all", ["name" => "all", "uses" => "Api\\PostsController@fetchAllPosts"]);
    Route::get("/detail/{id}", ["name" => "detail", "uses" => "Api\\PostsController@detail"]);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'post', 'name' => 'api.post.'], function(){
    Route::post("/create", ["name" => "create", "uses" => "Api\\PostsController@create"]);
    Route::post("/edit", ["name" => "edit", "uses" => "Api\\PostsController@edit"]);
    Route::post("/delete", ["name" => "delete", "uses" => "Api\\PostsController@delete"]);
});

//Profile Router
Route::group(['prefix' => 'profile', 'name' => 'api.profile.'], function () {
    Route::post("/detail", ["name" => "detail", "uses" => "Api\\ProfileController@show"]);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'profile', 'name' => 'api.profile.'], function () {
    Route::post("/follow_unfollow", ["name" => "follow_unfollow", "uses" => "Api\\ProfileController@follow_unfollow"]);
    Route::post("/edit", ["name" => "edit", "uses" => "Api\\ProfileController@edit"]);
});

//Comment Router
Route::group(['middleware' => 'auth:api', 'prefix' => 'comment', 'name' => 'api.comment.'], function () {
    Route::post("/create", ["name" => "create", "uses" => "Api\\CommentsController@create"]);
    Route::post("/edit", ["name" => "edit", "uses" => "Api\\CommentsController@edit"]);
});

Route::group(['prefix' => 'comment', 'name' => 'api.comment.'], function () {
    Route::get("/postComments/{id}", ["name" => "postComments", "uses" => "Api\\CommentsController@fetchPostComments"]);
    Route::get("/userComments/{id}", ["name" => "userComments", "uses" => "Api\\CommentsController@fetchUserComments"]);
});