<?php

use App\Models\Post;
use App\Models\User;

test('authenticated users can access create post page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('author.posts.create'))
        ->assertStatus(200);
});

test('author can create a post', function () {
    $author = User::factory()->create();

    $response = $this->actingAs($author)
        ->post(route('author.posts.store'), [
            'title' => 'Můj nový článek',
            'excerpt' => 'Krátký výtah',
            'content' => 'Obsah článku s **markdownem**',
            'tags' => ['laravel', 'php'],
        ]);

    $post = Post::where('title', 'Můj nový článek')->first();

    expect($post)->not->toBeNull();
    expect($post->user_id)->toBe($author->id);
    expect($post->tags)->toHaveCount(2);

    $response->assertRedirect(route('author.posts.index'));
});

test('validation errors are returned in czech', function () {
    $author = User::factory()->create();

    $response = $this->actingAs($author)
        ->post(route('author.posts.store'), [
            'title' => '',
            'content' => '',
        ]);

    $response->assertSessionHasErrors([
        'title' => 'Nadpis je povinný.',
        'content' => 'Obsah je povinný.',
    ]);
});
