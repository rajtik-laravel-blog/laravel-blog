<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = \App\Models\Tag::factory(10)->create();
        $user = \App\Models\User::factory()->create([
            'name' => 'Laravel Author',
            'email' => 'author@laravel.com',
        ]);

        \App\Models\Post::factory(30)
            ->recycle($user)
            ->create()
            ->each(function ($post) use ($tags) {
                $post->tags()->attach(
                    $tags->random(rand(2, 4))->pluck('id')->toArray()
                );
            });
    }
}
