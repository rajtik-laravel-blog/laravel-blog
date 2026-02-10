<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class PostController extends Controller
{
    public function create()
    {
        abort(404);
    }

    public function store(StorePostRequest $request)
    {
        abort(404);
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->with(['author', 'tags'])
            ->firstOrFail();

        // Paginate only root-level comments, including their users and nested replies with users
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('posts/Show', [
            'post' => $post,
            'comments' => $comments,
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $tagSlug = $request->query('tag');
        $searchQuery = $request->query('search');

        if ($searchQuery) {
            $query = Post::search($searchQuery)->query(fn ($q) => $q->with(['author', 'tags']));
        } else {
            $query = Post::with(['author', 'tags'])->latest();
        }

        if ($tagSlug && ! $searchQuery) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        // Ensure deterministic ordering
        $query->orderByDesc('created_at')->orderByDesc('id');

        return Inertia::render('Home', [
            'canRegister' => Features::enabled(Features::registration()),
            'posts' => $query->simplePaginate(9)->withQueryString(),
            'selectedTag' => $tagSlug ? Tag::where('slug', $tagSlug)->first() : null,
            'search' => (string) $searchQuery,
        ]);
    }
}
