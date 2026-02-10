<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('paginates root comments on post detail page', function () {
    $author = User::factory()->author()->create();
    $post = Post::factory()->for($author, 'author')->create();

    // Create 15 root-level comments for this post
    Comment::factory()->count(15)->for($post)->create();

    $this->get(route('posts.show', $post->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('posts/Show')
            ->has('comments', fn (Assert $comments) => $comments
                ->has('data', 5)
                ->where('total', 15)
                ->where('prev_page_url', null)
                ->where('current_page', 1)
                ->etc()
            )
        );
});

it('paginates user comments on dashboard', function () {
    $user = User::factory()->author()->create();
    $post = Post::factory()->for($user, 'author')->create();

    // Create 15 comments authored by the user
    Comment::factory()->count(15)->for($user)->for($post)->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('comments', fn (Assert $comments) => $comments
                ->where('total', 15)
                ->has('data', 10)
                ->etc()
            )
        );
});
