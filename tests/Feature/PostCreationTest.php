<?php

use App\Models\Post;
use App\Models\User;

test('only authors can access create post page', function () {
    $user = User::factory()->create(['is_author' => false]);
    $author = User::factory()->author()->create();

    $this->actingAs($user)
        ->get(route('posts.create'))
        ->assertStatus(403);

    $this->actingAs($author)
        ->get(route('posts.create'))
        ->assertStatus(200);
});

test('author can create a post', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)
        ->post(route('posts.store'), [
            'title' => 'Můj nový článek',
            'excerpt' => 'Krátký výtah',
            'content' => 'Obsah článku s **markdownem**',
            'tags' => ['laravel', 'php'],
        ]);

    $post = Post::where('title', 'Můj nový článek')->first();

    expect($post)->not->toBeNull();
    expect($post->user_id)->toBe($author->id);
    expect($post->tags)->toHaveCount(2);

    $response->assertRedirect(route('posts.show', $post->slug));
});

test('validation errors are returned in czech', function () {
    $author = User::factory()->author()->create();

    $response = $this->actingAs($author)
        ->post(route('posts.store'), [
            'title' => '',
            'content' => '',
        ]);

    $response->assertSessionHasErrors([
        'title' => 'Nadpis je povinný.',
        'content' => 'Obsah je povinný.',
    ]);
});
