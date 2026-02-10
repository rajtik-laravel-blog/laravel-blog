<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $stats = [
            'total_comments' => $user->comments()->count(),
            'total_replies' => Comment::whereIn('parent_id', $user->comments()->pluck('id'))->count(),
        ];

        if ($user->is_author) {
            $stats['total_articles'] = $user->posts()->count();
        }

        $latestReplies = Comment::whereIn('parent_id', $user->comments()->pluck('id'))
            ->with(['user', 'post'])
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'latestReplies' => $latestReplies,
        ]);
    }
}
