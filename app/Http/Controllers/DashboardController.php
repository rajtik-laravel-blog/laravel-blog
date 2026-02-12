<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $stats = [
            'total_articles' => $user->posts()->count(),
            'total_views' => $user->posts()->sum('views_count'),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
