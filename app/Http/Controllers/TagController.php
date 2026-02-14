<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        return Inertia::render('tags/Index', [
            'tags' => Tag::withCount(['posts' => function ($query) {
                $query->published();
            }])->orderBy('posts_count', 'desc')->get(),
        ]);
    }
}
