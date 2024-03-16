<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{

    public function index()
    {

        $posts = Post::all();
        return response()->json($posts);
    }


    public function store(StorePostRequest $request)
    {
        $post = new Post([
            'title' => $request->input('title'),
            'content' => $request->input('content')
        ]);
        $post->save();

        return response()->json(new PostResource($post), 201);
    }

    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найдена'], 404);
        }
        $postResource = new PostResource($post);
        return response()->json($postResource);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], 404);
        }

        $post->update([
            'title' => $request->has('title') ? $request->input('title') : $post->title,
            'content' => $request->has('content') ? $request->input('content') : $post->content,
        ]);

        return new PostResource($post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], 404);
        }
        $post->delete();

        return response()->json(['message' => 'Пост успешно удален']);
    }
}