<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_author_can_see_dashboard_with_stats(): void
    {
        $user = User::factory()->create(['is_author' => false]);
        $post = Post::factory()->create();

        // 2 comments from user
        Comment::factory()->count(2)->create(['user_id' => $user->id, 'post_id' => $post->id]);

        // 1 reply to one of user's comments
        $userComment = $user->comments()->first();
        Comment::factory()->create([
            'parent_id' => $userComment->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats.total_comments')
            ->where('stats.total_comments', 2)
            ->has('stats.total_replies')
            ->where('stats.total_replies', 1)
            ->missing('stats.total_articles')
            ->has('latestReplies', 1)
        );
    }

    public function test_author_can_see_dashboard_with_stats_including_articles(): void
    {
        $author = User::factory()->create(['is_author' => true]);
        Post::factory()->count(3)->create(['user_id' => $author->id]);

        $response = $this->actingAs($author)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('stats.total_articles', 3)
        );
    }

    public function test_user_can_see_their_comments_index(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => 'My test comment',
        ]);

        $response = $this->actingAs($user)->get(route('comments.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('comments/Index')
            ->has('comments.data', 1)
            ->where('comments.data.0.content', 'My test comment')
        );
    }
}
