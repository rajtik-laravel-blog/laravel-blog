<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\DestroyCommentRequest;
use App\Http\Requests\Comments\StoreCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(): Response
    {
        $comments = auth()->user()
            ->comments()
            ->with('post')
            ->latest()
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('comments/Index', [
            'comments' => $comments,
        ]);
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $post->comments()->create([
            'content' => $request->validated('content'),
            'user_id' => $request->user()->id,
            'parent_id' => $request->validated('parent_id'),
        ]);

        return back();
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment->update($request->validated());

        return back();
    }

    public function destroy(DestroyCommentRequest $request, Comment $comment)
    {
        $comment->delete();

        return back();
    }
}
