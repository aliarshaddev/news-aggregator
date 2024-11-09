<?php

namespace Tests\Unit\Api;

use App\Models\Category;
use App\Models\Source;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CategoryControllerTest extends TestCase
{
    protected $source;
    protected function setUp(): void
    {
        parent::setUp();
        $this->source = Source::factory()->create();
        Category::factory()->count(15)->create([
            'source_id' => $this->source->id,
        ]);
    }

    public function testGetCategoriesSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->categoriesRoute);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => [
                            'id', 'name', 'source_id'
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
    public function testGetCategoriesNotAuthenticated()
    {
        $response = $this->getJson($this->categoriesRoute);
        $response->assertStatus(401);
    }

    public function testGetCategoriesNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson($this->categoriesRoute);
        $response->assertStatus(403);
    }
}
