<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user:id,name,profile'])->orderBy('id', 'desc')->get();
        return $posts;
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,jpg,png,gif,jfif'
        ]);
        $folder = 'post_images';
        $image = $request->file('image');
        $imageName = time() . "_" . $image->getClientOriginalName();
        $image->move($folder, $imageName);

        return Post::create([
            'user_id' => auth()->user()->id,
            'image' => $imageName,
            'caption' => $request->caption,
            'category' => $request->category,
            'location' => $request->location
        ]);
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function update(Request $request, Post $post)
    {
        $post->update([
            'caption' => $request->caption,
            'category' => $request->category,
            'location' => $request->location
        ]);
        return response([
            'message' => 'Post edited successfully'
        ]);
    }

    public function destroy(Post $post)
    {
        $path = 'post_images/' . $post->getRawOriginal('image');
        if (File::exists($path))
            File::delete($path);
        $post->delete();
        return response([
            'message' => 'Post deleted successfully'
        ]);
    }

    public function userPosts()
    {
        return auth()->user()->posts;
    }
}
