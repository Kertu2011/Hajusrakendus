<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function blogPost(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        
        $post = $request->user()->posts()->create($request->all());
        
        return redirect()->route('blog.index')->with('success', 'Post created successfully');
    }

    public function blogEdit(Request $request, Post $post) {
        if (Auth::id() !== $post->user_id && !Auth::user()->is_admin) {
            return redirect()->route('blog.index')->with('error', 'Unauthorized to edit this post');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post->update($request->all());

        return redirect()->route('blog.posts.show', ['post' => $post->id])->with('success', 'Post updated successfully');
    }

    public function destroy(Post $post) {
        if (Auth::id() !== $post->user_id && !Auth::user()->is_admin) {
            return redirect()->route('blog.index')->with('error', 'Unauthorized to delete this post');
        }

        $post->delete();

        return redirect()->route('blog.index')->with('success', 'Post deleted successfully');
    }
}