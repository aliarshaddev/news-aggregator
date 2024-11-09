<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\UserPreference;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UserPreferenceControllerControllerTest extends TestCase
{
    protected $user;
    protected $sources;
    protected $categories;
    protected $authors;
    protected $data;
    protected $createPreferenceData;
    protected function setUp(): void
    {
        parent::setUp();
        $this->sources = Source::factory()->count(3)->create();
        $this->categories = Category::factory()->count(3)->create();
        $this->authors = Author::factory()->count(3)->create();
        $this->data = [
            'preferred_sources' => $this->sources->pluck('id')->toArray(),
            'preferred_categories' => $this->categories->pluck('id')->toArray(),
            'preferred_authors' => $this->authors->pluck('id')->toArray(),
        ];
        $this->createPreferenceData = [
            'preferred_sources' => $this->data['preferred_sources'],
            'preferred_categories' => $this->data['preferred_categories'],
            'preferred_authors' => $this->data['preferred_authors'],
            'user_id' =>$this->user->id,
        ];
    }

    public function testSetUserPreferenceSuccess()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->preferencesRoute, $this->data);
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'success',
                     'data' => $this->data,
                 ]);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $this->user->id,
            'preferred_sources' => json_encode($this->data['preferred_sources']),
            'preferred_categories' => json_encode($this->data['preferred_categories']),
            'preferred_authors' => json_encode($this->data['preferred_authors']),
        ]);
    }

    public function testSetUserPreferenceNotAuthenticated()
    {
        $response = $this->postJson($this->preferencesRoute, $this->data);
        $response->assertStatus(401);
    }

    public function testSetUserPreferenceNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->postJson($this->preferencesRoute, $this->data);
        $response->assertStatus(403);
    }

    public function testGetUserPreferenceSuccess()
    {
        UserPreference::factory()->create($this->createPreferenceData);
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->preferencesRoute);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                            'id', 'user_id', 'preferred_sources', 'preferred_categories', 'preferred_authors'
                    ]
                ]);
    }

    public function testGetUserPreferenceEmpty()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->preferencesRoute);
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'success',
                    'data' => null,
                ]);
    }

    public function testGetUserPreferenceNotAuthenticated()
    {
        $response = $this->getJson($this->preferencesRoute);
        $response->assertStatus(401);
    }

    public function testGetUserPreferenceNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson($this->preferencesRoute);
        $response->assertStatus(403);
    }

    public function testGetPersonalizedFeedSuccess()
    {
        UserPreference::factory()->create($this->createPreferenceData);
        Sanctum::actingAs($this->user);
        $response = $this->getJson($this->personalizedFeedRoute);
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

    public function testGetPersonalizedFeedNotAuthenticated()
    {
        $response = $this->getJson($this->personalizedFeedRoute);
        $response->assertStatus(401);
    }

    public function testGetPersonalizedFeedNotAuthorized()
    {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->getJson($this->personalizedFeedRoute);
        $response->assertStatus(403);
    }
}
