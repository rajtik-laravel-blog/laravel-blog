<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = \App\Models\Post::all();
        $users = \App\Models\User::all();

        if ($posts->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($posts as $post) {
            // Create root comments
            $comments = \App\Models\Comment::factory(rand(2, 5))->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ]);

            // Create some replies
            foreach ($comments as $comment) {
                if (rand(0, 1)) {
                    \App\Models\Comment::factory(rand(1, 3))->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                        'parent_id' => $comment->id,
                    ]);
                }
            }
        }
    }
}
