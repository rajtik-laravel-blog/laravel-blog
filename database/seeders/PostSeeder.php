<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::factory(10)->create();
        $user = User::factory()->create([
            'name' => 'Laravel Author',
            'email' => 'author@laravel.com',
        ]);

        Post::factory(30)
            ->recycle($user)
            ->published()
            ->create(['image_url' => fn (): string => 'https://picsum.photos/seed/'.Str::random(10).'/1200/600'])
            ->each(function ($post) use ($tags) {
                $post->tags()->attach(
                    $tags->random(rand(2, 4))->pluck('id')->toArray()
                );
            });
    }
}
