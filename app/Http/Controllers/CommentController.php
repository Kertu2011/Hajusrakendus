<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return to_route('blog.posts.show', ['post' => $post->id])
            ->with('message', 'Comment added successfully');
    }

    // Optional: Admin can delete any comment
    public function destroy(Comment $comment)
    {
        if (!Auth::user()->is_admin) {
             // Or check if comment user_id matches Auth::id() for users to delete their own comments
            return response()->json(['message' => 'Unauthorized to delete this comment'], 403);
        }
        $comment->delete();

        return inertia('Blog/PostShow', [
            'message' => 'Comment deleted successfully',
        ]);
    }
}