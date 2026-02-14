<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('landing page is rendered correctly', function () {
    $post = Post::factory()->create(['title' => 'Landing Page Test Post']);
    $tag = Tag::factory()->create(['name' => 'TestTag']);
    $post->tags()->attach($tag);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('featuredPost', fn (Assert $page) => $page
            ->where('title', 'Landing Page Test Post')
            ->has('tags', 1)
            ->where('tags.0.name', 'TestTag')
            ->etc()
        )
        ->has('posts.data', 0)
    );
});

test('landing page with multiple posts is rendered correctly', function () {
    $posts = Post::factory()->count(3)->create()->sortByDesc('created_at')->values();
    $latestPost = $posts[0];

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('featuredPost', fn (Assert $page) => $page
            ->where('id', $latestPost->id)
            ->where('title', $latestPost->title)
            ->etc()
        )
        ->has('posts.data', 2)
    );
});

test('privacy policy page is rendered correctly', function () {
    $response = $this->get(route('privacy-policy'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('PrivacyPolicy')
    );
});

test('blog article page is rendered correctly', function () {
    $author = User::factory()->create(['name' => 'Author Name']);
    $post = Post::factory()->create([
        'title' => 'Article Page Test',
        'slug' => 'article-page-test',
        'content' => 'Test Content',
        'user_id' => $author->id,
    ]);

    $response = $this->get(route('posts.show', $post->slug));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('posts/Show')
        ->has('post', fn (Assert $page) => $page
            ->where('title', 'Article Page Test')
            ->where('content', 'Test Content')
            ->etc()
        )
    );
});

test('full text search works correctly', function () {
    Post::factory()->create(['title' => 'Laravel Framework', 'content' => 'The best PHP framework']);
    Post::factory()->create(['title' => 'Vue.js', 'content' => 'Progressive JavaScript Framework']);
    Post::factory()->create(['title' => 'Tailwind CSS', 'content' => 'Utility-first CSS framework']);

    // Search for "Laravel"
    $response = $this->get(route('home', ['search' => 'Laravel']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('posts.data', 1)
        ->where('posts.data.0.title', 'Laravel Framework')
    );

    // Search for "framework"
    $response = $this->get(route('home', ['search' => 'framework']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('posts.data', 3)
    );
});

test('blog tags filtering works correctly', function () {
    $tagLaravel = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
    $tagVue = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue']);

    $post1 = Post::factory()->create(['title' => 'Laravel Post']);
    $post1->tags()->attach($tagLaravel);

    $post2 = Post::factory()->create(['title' => 'Vue Post']);
    $post2->tags()->attach($tagVue);

    $post3 = Post::factory()->create(['title' => 'Shared Post']);
    $post3->tags()->attach($tagLaravel);
    $post3->tags()->attach($tagVue);

    // Filter by "laravel" tag
    $response = $this->get(route('home', ['tag' => 'laravel']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('posts.data', 2)
        ->where('selectedTag.slug', 'laravel')
        ->where('posts.data', function ($posts) {
            $titles = collect($posts)->pluck('title')->sort()->values()->all();
            expect($titles)->toEqual(collect(['Shared Post', 'Laravel Post'])->sort()->values()->all());

            return true;
        })
    );

    // Filter by "vue" tag
    $response = $this->get(route('home', ['tag' => 'vue']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Home')
        ->has('posts.data', 2)
        ->where('selectedTag.slug', 'vue')
        ->where('posts.data', function ($posts) {
            $titles = collect($posts)->pluck('title')->sort()->values()->all();
            expect($titles)->toEqual(collect(['Shared Post', 'Vue Post'])->sort()->values()->all());

            return true;
        })
    );
});
