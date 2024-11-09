<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Source;
use App\Models\Category;
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),       
            'description' => fake()->paragraph(),
            'published_at' => Carbon::now()->subDays(rand(1, 30)),
            'link' => fake()->url(),
            'source_id' => Source::factory(),
            'category_id' => Category::factory(),
            'author_id' => Author::factory(),   
        ];
    }
}
