<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Inertia\Inertia;

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
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['author', 'tags'])
            ->firstOrFail();

        $post->increment('views_count');

        return Inertia::render('posts/Show', [
            'post' => $post,
        ]);
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $tagSlug = $request->query('tag');
        $searchQuery = $request->query('search');

        $featuredPost = Post::published()->with(['author', 'tags'])->latest()->first();

        if ($searchQuery) {
            $query = Post::search($searchQuery)->query(fn ($q) => $q->published()->with(['author', 'tags']));
        } else {
            $query = Post::published()->with(['author', 'tags'])->latest();
        }

        if ($tagSlug && ! $searchQuery) {
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        if ($featuredPost && ! $searchQuery && ! $tagSlug) {
            $query->where('id', '!=', $featuredPost->id);
        }

        // Ensure deterministic ordering
        $query->orderByDesc('created_at')->orderByDesc('id');

        return Inertia::render('Home', [
            'posts' => $query->simplePaginate(9)->withQueryString(),
            'featuredPost' => $featuredPost,
            'selectedTag' => $tagSlug ? Tag::where('slug', $tagSlug)->first() : null,
            'search' => (string) $searchQuery,
        ]);
    }
}
