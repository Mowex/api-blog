<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with(['user', 'categories'])->get();
        return response()->json(['posts' => $posts], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'text' => 'required|string',
            'categories' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $post = Post::create(array_merge(
            $validator->validate(),
            ['user_id' => Auth::id()]
        ));

        if (!!$request->categories) {
            $post->categories()->sync($request->categories);
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'text' => 'required|string',
            'categories' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $post = Post::findOrFail($id);

        if ($post->user->id != Auth::id()) {
            return response()->json([
                'message' => 'Only the owner user can edit the post',
            ], 400);
        }

        $post->title = $request->title;
        $post->text = $request->text;

        if (!!$request->categories) {
            $post->categories()->sync($request->categories);
        }

        $post->save();

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
            'post' => $post
        ], 200);
    }
}
