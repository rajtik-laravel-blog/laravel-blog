<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        $content = '# '.$title."\n\n";
        $content .= "## Introduction\n\n".$this->faker->paragraph()."\n\n";
        $content .= "### Core Features\n\n";
        $content .= "Here is what we are going to cover:\n\n";
        $content .= "1. **Installation** - How to get started.\n";
        $content .= "2. **Configuration** - Customizing your setup.\n";
        $content .= "3. **Deployment** - Going live.\n\n";
        $content .= "#### Why use this?\n\n";
        $content .= "- It is fast\n- It is reliable\n- It is built with Laravel 12\n\n";
        $content .= "### Implementation Example\n\n";
        $content .= "Below is a sample code snippet:\n\n";
        $content .= "```php\n<?php\n\nnamespace App\Http\Controllers;\n\nclass PostController extends Controller\n{\n    public function index()\n    {\n        return view('posts.index');\n    }\n}\n```\n\n";
        $content .= "> \"Laravel has changed the way I build web applications. It's truly a breath of fresh air.\"\n\n";
        $content .= "## Conclusion\n\n".$this->faker->paragraph();

        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'excerpt' => $this->faker->paragraph(),
            'content' => $content,
            'is_published' => true,
            'image_url' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function published(): Factory
    {
        return $this->state(fn (array $attributes) => ['is_published' => true]);
    }
}
