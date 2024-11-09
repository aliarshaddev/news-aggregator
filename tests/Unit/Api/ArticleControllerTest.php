<?php

namespace Tests\Unit\Api;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Laravel\Sanctum\Sanctum;

class ArticleControllerTest extends TestCase
{
    protected $source;
    protected $article;
    protected $category;
    protected $author;
    protected function setUp(): void
    {
        parent::setUp();
        $this->source = Source::factory()->create();
        $this->category = Category::factory()->create(['source_id' => $this->source->id]);
        $this->author = Author::factory()->create(['source_id' => $this->source->id]);
        Article::factory()->count(15)->create([
            'source_id' => $this->source->id,
            'category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);
        $this->article = Article::first();
    }
    public function testGetArticlesSuccess()
    {

        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->articlesRoute);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => [
                             'id', 'title', 'description', 'published_at', 'source', 'category', 'author'
                         ]
                     ],
                     'page_context' => [
                         'page',
                         'per_page',
                         'total_pages',
                         'has_more_page',
                         'sort_column',
                         'sort_order'
                     ]
                 ]);
    }
    public function testShowArticleSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->articlesRoute . $this->article->id);
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                             'id', 'title', 'description', 'published_at', 'source', 'category', 'author'
                     ]
                 ]);
    }
    public function testShowArticleNotFound()
    {
        $nonExistentId = (Article::max('id') ?? 0) + 1;
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->articlesRoute . $nonExistentId);
        $response->assertStatus(200)
                 ->assertJson([
                    'success' => true,
                    'message' => 'success',
                    'data' => null,
                 ]);
    }
    public function testGetArticlesNotAuthenticated()
    {
        $response = $this->getJson('/api/articles');
        $response->assertStatus(401);
    }

    public function testGetArticlesNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson('/api/articles');
        $response->assertStatus(403);
    }
    
    public function testShowArticleNotAuthenticated()
    {
        $response = $this->getJson($this->articlesRoute . $this->article->id);
        $response->assertStatus(401);
    }

    public function testShowArticleNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson('/api/articles');
        $response->assertStatus(403);
    }
    public function testShowArticleNotFoundNotAuthenticated()
    {
        $nonExistentId = (Article::max('id') ?? 0) + 1;
        $response = $this->getJson($this->articlesRoute . $nonExistentId);
        $response->assertStatus(401);
    }
    public function testShowArticleNotFoundNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $nonExistentId = (Article::max('id') ?? 0) + 1;
        $response = $this->getJson($this->articlesRoute . $nonExistentId);
        $response->assertStatus(403);
    }
}
