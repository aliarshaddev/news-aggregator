<?php

namespace Tests\Unit\Api;

use App\Models\Author;
use App\Models\Source;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthorControllerTest extends TestCase
{
    protected $source;
    protected function setUp(): void
    {
        parent::setUp();
        $this->source = Source::factory()->create();
        Author::factory()->count(15)->create([
            'source_id' => $this->source->id,
        ]);
    }

    public function testGetAuthorsSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->authorsRoute);
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
    public function testGetAuthorsNotAuthenticated()
    {
        $response = $this->getJson($this->authorsRoute);
        $response->assertStatus(401);
    }

    public function testGetAuthorsNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson($this->authorsRoute);
        $response->assertStatus(403);
    }
}
