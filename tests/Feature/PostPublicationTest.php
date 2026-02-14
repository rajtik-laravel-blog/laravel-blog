<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

test('newly created posts are unpublished by default', function () {
    $author = User::factory()->create();

    $this->actingAs($author)
        ->post(route('author.posts.store'), [
            'title' => 'Unpublished Post',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
        ]);

    $post = Post::where('title', 'Unpublished Post')->first();
    expect($post->is_published)->toBeFalse();
});

test('unpublished posts are not visible on home page', function () {
    $publishedPost = Post::factory()->create(['title' => 'Published', 'is_published' => true]);
    $unpublishedPost = Post::factory()->create(['title' => 'Unpublished', 'is_published' => false]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Published');
    $response->assertDontSee('Unpublished');
});

test('unpublished posts are not accessible via direct link', function () {
    $unpublishedPost = Post::factory()->create(['is_published' => false]);

    $response = $this->get(route('posts.show', $unpublishedPost->slug));

    $response->assertStatus(404);
});

test('author can toggle publication status from dashboard', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id, 'is_published' => false]);

    $response = $this->actingAs($author)
        ->patch(route('author.posts.toggle-publish', $post));

    $response->assertRedirect();
    expect($post->fresh()->is_published)->toBeTrue();

    $this->actingAs($author)
        ->patch(route('author.posts.toggle-publish', $post));

    expect($post->fresh()->is_published)->toBeFalse();
});

test('other users cannot toggle publication status', function () {
    $author = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id, 'is_published' => false]);

    $response = $this->actingAs($otherUser)
        ->patch(route('author.posts.toggle-publish', $post));

    $response->assertStatus(403);
    expect($post->fresh()->is_published)->toBeFalse();
});

test('tags count only published posts', function () {
    $tag = Tag::factory()->create();
    Post::factory()->create(['is_published' => true])->tags()->attach($tag);
    Post::factory()->create(['is_published' => false])->tags()->attach($tag);

    $response = $this->get(route('tags'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('tags/Index')
        ->where('tags.0.posts_count', 1)
    );
});
