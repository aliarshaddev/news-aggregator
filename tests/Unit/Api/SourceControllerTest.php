<?php

namespace Tests\Unit\Api;

use App\Models\Source;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SourceControllerTest extends TestCase
{
    protected $source;
    protected $data;
    protected function setUp(): void
    {
        parent::setUp();
        $this->source = Source::factory()->create();
        $this->data = [
            'name' => fake()->name(),
            'title' => fake()->title(),
            'rss_feed_link' => fake()->url(),
        ];
    }

    public function testCreateSourceSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->sourcesRoute, $this->data);
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'success',
                     'data' => [
                         'name' => $this->data['name'],
                         'title' =>  $this->data['title'],
                         'rss_feed_link' =>  $this->data['rss_feed_link'],
                     ],
                 ]);
    }

    public function testCreateSourceInvalidName()
    {
        $data = [
            'title' =>  $this->data['title'],
            'rss_feed_link' =>  $this->data['rss_feed_link'],
        ];
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->sourcesRoute, $data);
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'name' => [],
                    ],
                ]);  
    }

    public function testCreateSourceInvalidTitle()
    {
        $data = [
            'name' =>  $this->data['name'],
            'rss_feed_link' =>  $this->data['rss_feed_link'],
        ];
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->sourcesRoute, $data);
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'title' => [],
                    ],
                ]);  
    }

    public function testCreateSourceInvalidRssFeedLink()
    {
        $data = [
            'name' =>  $this->data['name'],
            'title' =>  $this->data['title'],
        ];
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->sourcesRoute, $data);
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'rss_feed_link' => [],
                    ],
                ]);  
    }
    public function testGetSourcesSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->sourcesRoute);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => [
                            'name', 'title', 'rss_feed_link'
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
    public function testShowSourceSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->sourcesRoute . $this->source->id);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'name', 'title', 'rss_feed_link'
                    ],
                ]);
    }
    public function testCreateSourceSuccessNotAuthenticated()
    {
        $response = $this->postJson($this->sourcesRoute, $this->data);
        $response->assertStatus(401);
    }
    public function testGetSourcesSuccessNotAuthenticated()
    {
        $response = $this->getJson($this->sourcesRoute);
        $response->assertStatus(401);
    }
    public function testShowSourceSuccessNotAuthenticated()
    {
        $response = $this->getJson($this->sourcesRoute . $this->source->id);
        $response->assertStatus(401);
    }
    public function testCreateSourceSuccessNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->postJson($this->sourcesRoute, $this->data);
        $response->assertStatus(403);
    }
    public function testGetSourcesSuccessNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson($this->sourcesRoute);
        $response->assertStatus(403);
    }
    public function testShowSourceSuccessNotNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson($this->sourcesRoute . $this->source->id);
        $response->assertStatus(403);
    }
    public function testShowSourceNotFound()
    {
        Sanctum::actingAs($this->user);
        $nonExistentId = (Source::max('id') ?? 0) + 1;
        $response = $this->getJson($this->sourcesRoute . $nonExistentId);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);
    }
    public function testShowArticleNotFoundNotAuthenticated()
    {
        $nonExistentId = (Source::max('id') ?? 0) + 1;
        $response = $this->getJson($this->sourcesRoute . $nonExistentId);
        $response->assertStatus(401);
    }
    public function testShowArticleNotFoundNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $nonExistentId = (Source::max('id') ?? 0) + 1;
        $response = $this->getJson($this->sourcesRoute . $nonExistentId);
        $response->assertStatus(403);
    }
}
