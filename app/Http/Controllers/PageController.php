<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{

    public function blogIndex() { 
        return inertia('Blog/Index', [
            'posts' => Post::with('user:id,name')->orderBy('created_at', 'desc')->get()
        ]);
    }
    
    public function blogCreate() {
        return inertia('Blog/PostCreate', [
            'users' => User::select('id', 'name')->get()
        ]);
    }

    public function blogShow(Post $post) {
        $post->load(['user:id,name', 'comments.user:id,name']);
        return inertia('Blog/PostShow', ['post' => $post]);
    }

    public function blogEdit(Post $post) {
        if (auth()->id() !== $post->user_id && !auth()->user()->is_admin) {
            return redirect()->route('blog.index')->with('error', 'Unauthorized to edit this post');
        }
        return inertia('Blog/PostEdit', [
            'post' => $post,
            'users' => User::select('id', 'name')->get()
        ]);
    }
}
