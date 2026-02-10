<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

test('registered users can leave a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'content' => 'This is a test comment.',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'content' => 'This is a test comment.',
        'parent_id' => null,
    ]);
});

test('registered users can reply to a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $parentComment = Comment::factory()->create([
        'post_id' => $post->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'content' => 'This is a reply.',
            'parent_id' => $parentComment->id,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'content' => 'This is a reply.',
        'parent_id' => $parentComment->id,
    ]);
});

test('guests cannot leave a comment', function () {
    $post = Post::factory()->create();

    $response = $this->post(route('comments.store', $post), [
        'content' => 'This is a test comment.',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('comments', 0);
});

test('comment content must be at least 3 characters', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'content' => 'Hi',
        ]);

    $response->assertSessionHasErrors('content');
    $this->assertDatabaseCount('comments', 0);
});

test('users can edit their own comment', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'content' => 'Original content',
    ]);

    $response = $this->actingAs($user)
        ->put(route('comments.update', $comment), [
            'content' => 'Updated content',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'content' => 'Updated content',
    ]);
});

test('users cannot edit others comments', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $otherUser->id,
        'content' => 'Original content',
    ]);

    $response = $this->actingAs($user)
        ->put(route('comments.update', $comment), [
            'content' => 'Updated content',
        ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'content' => 'Original content',
    ]);
});

test('users can delete their own comment', function () {
    $user = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('comments.destroy', $comment));

    $response->assertRedirect();
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('users cannot delete others comments', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('comments.destroy', $comment));

    $response->assertForbidden();
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
    ]);
});

test('users cannot reply to their own comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'content' => 'This is a reply to my own comment.',
            'parent_id' => $comment->id,
        ]);

    $response->assertForbidden();
    $this->assertDatabaseCount('comments', 1); // Only the initial comment should exist
});
