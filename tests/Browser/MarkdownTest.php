<?php

use App\Models\Post;
use App\Models\User;

it('correctly renders various markdown headings', function () {
    $user = User::factory()->create();

    $markdownContent = <<<'MARKDOWN'
# This is title with one space
#This is title with no space

## This is h2 title with space
##This is h2 title with no space

### This is h3 title with space
###This is h3 title with no space

#### H4 title
####H4 title

##### H5 title
#####H5 title

###### H6 title
######H6 title
MARKDOWN;

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'content' => $markdownContent,
    ]);

    visit("/posts/{$post->slug}")
        ->assertPresent('h1.md-h1')
        ->assertSee('This is title with one space')
        ->assertSee('This is title with no space')
        ->assertPresent('h2.md-h2')
        ->assertSee('This is h2 title with space')
        ->assertSee('This is h2 title with no space')
        ->assertPresent('h3.md-h3')
        ->assertSee('This is h3 title with space')
        ->assertSee('This is h3 title with no space')
        ->assertPresent('h4.md-h4')
        ->assertSee('H4 title')
        ->assertPresent('h5.md-h5')
        ->assertSee('H5 title')
        ->assertPresent('h6.md-h6')
        ->assertSee('H6 title')
        // Check that no hash symbols are leaking into the text
        ->assertDontSee('# This is h2 title with space')
        ->assertDontSee('# This is h3 title with space');
});

it('correctly renders horizontal rules, links, and code', function () {
    $user = User::factory()->create();

    $markdownContent = <<<'MARKDOWN'
    ---
    This is a [link to Laravel](https://laravel.com)
    Here is some `inline code`
    And code with ``double backticks``
    MARKDOWN;

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'content' => $markdownContent,
    ]);

    visit("/posts/{$post->slug}")
        ->assertPresent('hr.md-hr')
        ->assertPresent('a.md-a[href="https://laravel.com"]')
        ->assertSee('link to Laravel')
        ->assertPresent('code.md-code')
        ->assertSee('inline code')
        ->assertSee('double backticks');
});

it('correctly renders horizontal rules, inside text', function () {
    $user = User::factory()->create();

    $markdownContent = <<<'MARKDOWN'
This is a [link to Laravel](https://laravel.com)

---

Here is some `inline code`
And code with ``double backticks``
MARKDOWN;

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'content' => $markdownContent,
    ]);

    visit("/posts/{$post->slug}")
        ->assertPresent('hr.md-hr');
});

it('correctly renders line breaks for new line', function () {
    $user = User::factory()->create();

    // Two spaces at the end of the line should produce a <br>
    $markdownContent = "Line one\nLine two";

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'content' => $markdownContent,
    ]);

    visit("/posts/{$post->slug}")
        ->assertPresent('article .md-p br');
});
