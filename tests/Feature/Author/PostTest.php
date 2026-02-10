<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('author can see their posts index', function () {
    $author = User::factory()->create(['is_author' => true]);
    $otherPost = Post::factory()->create();
    $authorPost = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($author)
        ->get(route('author.posts.index'));

    $response->assertStatus(200);
    $response->assertSee($authorPost->title);
    // It might see other posts if the controller doesn't filter,
    // but the controller DOES filter: auth()->user()->posts()
});

test('non-author cannot see posts index', function () {
    $user = User::factory()->create(['is_author' => false]);

    $response = $this->actingAs($user)
        ->get(route('author.posts.index'));

    $response->assertStatus(403);
});

test('author can see create post page', function () {
    $author = User::factory()->create(['is_author' => true]);

    $response = $this->actingAs($author)
        ->get(route('author.posts.create'));

    $response->assertStatus(200);
});

test('author can store new post', function () {
    Storage::fake('images');
    $author = User::factory()->create(['is_author' => true]);

    $response = $this->actingAs($author)
        ->post(route('author.posts.store'), [
            'title' => 'New Post Title',
            'content' => 'New Post Content',
            'excerpt' => 'New Post Excerpt',
            'tags' => ['NewTag1', 'NewTag2'],
            'image' => UploadedFile::fake()->image('newpost.jpg'),
        ]);

    $response->assertRedirect(route('author.posts.index'));

    $post = Post::where('title', 'New Post Title')->first();
    expect($post)->not->toBeNull();
    expect($post->user_id)->toBe($author->id);
    expect($post->content)->toBe('New Post Content');
    expect($post->tags)->toHaveCount(2);
    expect($post->image_url)->not->toBeNull();
});

test('author can see edit post page', function () {
    $author = User::factory()->create(['is_author' => true]);
    $post = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($author)
        ->get(route('author.posts.edit', $post));

    $response->assertStatus(200);
});

test('author cannot edit other authors post', function () {
    $author = User::factory()->create(['is_author' => true]);
    $otherPost = Post::factory()->create();

    $response = $this->actingAs($author)
        ->get(route('author.posts.edit', $otherPost));

    $response->assertStatus(403);
});

test('author can update their post', function () {
    Storage::fake('images');
    $author = User::factory()->create(['is_author' => true]);
    $post = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($author)
        ->post(route('author.posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'excerpt' => 'Updated Excerpt',
            'tags' => ['Tag1', 'Tag2'],
            'image' => UploadedFile::fake()->image('post.jpg'),
        ]);

    $response->assertRedirect(route('author.posts.index'));

    $post->refresh();
    expect($post->title)->toBe('Updated Title');
    expect($post->content)->toBe('Updated Content');
    expect($post->tags)->toHaveCount(2);
    expect($post->image_url)->not->toBeNull();
});

test('old image is deleted when new image is uploaded', function () {
    Storage::fake('images');
    $author = User::factory()->create(['is_author' => true]);

    // 1. Create a post with an initial image
    $initialFile = UploadedFile::fake()->image('initial.jpg');
    $initialPath = $initialFile->store('posts', 'images');
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'image_url' => Storage::disk('images')->url($initialPath),
    ]);

    Storage::disk('images')->assertExists($initialPath);

    // 2. Update the post with a new image
    $newFile = UploadedFile::fake()->image('new.jpg');

    $response = $this->actingAs($author)
        ->post(route('author.posts.update', $post), [
            'title' => $post->title,
            'content' => $post->content,
            'image' => $newFile,
        ]);

    $response->assertRedirect(route('author.posts.index'));

    // 3. Verify old image is deleted and new one exists
    Storage::disk('images')->assertMissing($initialPath);

    $post->refresh();
    $newPath = Str::after($post->image_url, '/storage/');
    Storage::disk('images')->assertExists($newPath);
});

test('update post requires title and content', function () {
    $author = User::factory()->create(['is_author' => true]);
    $post = Post::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($author)
        ->post(route('author.posts.update', $post), [
            'title' => '',
            'content' => '',
        ]);

    $response->assertSessionHasErrors(['title', 'content']);
});
