<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use App\Models\Post;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $images = Picture::all();
        $post_relations = DB::table('posts')
    ->join('authors', 'posts.user_id', '=', 'authors.user_id')
    ->select('posts.*', 'authors.*') // Include desired columns
    ->get();

        $posts = Post::query()->paginate(10);
        return view('blog.blog-list',compact('posts', 'images'));
//        return view('blog.blog-list',compact('post_relations', 'images'));
    }


    /**
     * Display a single post of the resource.
     */
    public function singlePost($slug): Factory|View|Application
    {
//        equivalent query 'select * from users join authors on users.id = authors.user_id;'
              $users = DB::table('users')
    ->join('authors', 'users.id', '=', 'authors.user_id')
    ->select('users.*', 'authors.*') // Include desired columns
    ->get();

//        equivalent query 's'
               $post_relations = DB::table('posts')
    ->join('authors', 'posts.user_id', '=', 'authors.user_id')
    ->select('posts.*', 'authors.*') // Include desired columns
    ->get();

//                @dd($users);
//                @dd($post_relations);
//select * from users join author on users.user_id = author.user_id;
         $post = Post::where('slug', $slug)->first();
//        $post = Post::find($id);
//        if (! $post){
//            abort(404);
//        }
//        return $post;
        return view('blog.blog-post', compact('post'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
