<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $posts = auth()->user()->posts()
            ->with(['tags'])
            ->select('id', 'title', 'slug', 'excerpt', 'image_url', 'views_count', 'is_published', 'created_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dashboard', [
            'posts' => $posts,
        ]);
    }
}
