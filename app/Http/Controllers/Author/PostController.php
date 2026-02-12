<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = auth()->user()->posts()
            ->with(['author', 'tags'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('author/posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('author/posts/Create', [
            'tags' => Tag::all(),
        ]);
    }

    public function store(\App\Http\Requests\StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $post = new Post;
        $post->title = $validated['title'];
        $post->slug = Str::slug($validated['title']).'-'.Str::random(5);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];
        $post->user_id = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'images');
            $post->image_url = Storage::url($path);
        }

        $post->save();

        if (! empty($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('author.posts.index')->with('success', 'Článek byl úspěšně vytvořen.');
    }

    public function edit(Post $post): Response
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('author/posts/Edit', [
            'post' => $post->load('tags'),
            'tags' => Tag::all(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $post->title = $validated['title'];
        $post->excerpt = $validated['excerpt'] ?? null;
        $post->content = $validated['content'];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image_url) {
                $oldPath = Str::after($post->image_url, '/storage/');
                Storage::disk('images')->delete($oldPath);
            }

            $path = $request->file('image')->store('posts', 'images');
            $post->image_url = Storage::url($path);
        }

        $post->save();

        if (isset($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('author.posts.index')->with('success', 'Článek byl úspěšně aktualizován.');
    }
}
