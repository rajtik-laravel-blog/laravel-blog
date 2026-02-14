<?php

use App\Models\Post;
use App\Models\User;

test('viewing a post increments its views_count', function () {
    $post = Post::factory()->create(['views_count' => 0]);

    expect($post->views_count)->toBe(0);

    $this->get(route('posts.show', $post->slug));

    expect($post->fresh()->views_count)->toBe(1);

    $this->get(route('posts.show', $post->slug));

    expect($post->fresh()->views_count)->toBe(2);
});

test('dashboard displays total views for authenticated users', function () {
    $user = User::factory()->create([]);
    Post::factory()->create([
        'user_id' => $user->id,
        'views_count' => 30,
        'created_at' => now(),
    ]);
    Post::factory()->create([
        'user_id' => $user->id,
        'views_count' => 10,
        'created_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
        ->component('Dashboard')
        ->where('posts.data.0.views_count', 30)
    );
});

test('dashboard displays 0 views for user with no posts', function () {
    $user = User::factory()->create([]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
        ->component('Dashboard')
        ->has('posts.data', 0)
    );
});
