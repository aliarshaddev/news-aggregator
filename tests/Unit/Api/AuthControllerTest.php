<?php

namespace Tests\Unit\Api;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Notification;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testUserRegisterSuccess() {
        $response = $this->postJson($this->registerRoute, $this->registerData);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'token',
                        'name',
                    ],
                ]);        
        $this->assertDatabaseHas('users', [
            'email' => $this->registerData['email'],
            'role_id' => $this->role->id,
        ]);
    } 

    public function testUserLoginSuccess() {
        User::factory()->create([
            'email' => $this->loginData['email'],
            'password' => $this->loginData['password'],
            'role_id' => $this->user->role,
        ]);
        $response = $this->postJson($this->loginRoute, $this->loginData);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'token',
                        'name',
                    ],
                ]);
    }
    public function testUserLogoutSuccess() {
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->logoutRoute);
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);
    }

    public function testUserLogoutNotAuthenticated() {
        $response = $this->postJson($this->logoutRoute);
        $response->assertStatus(401);
    }

    public function testUserLogoutNotAuthorized() {
        Sanctum::actingAs($this->unAuthorizedUser);
        $response = $this->postJson($this->logoutRoute);
        $response->assertStatus(403);
    }
    
    public function testResetPasswordSuccess()
    {
        Sanctum::actingAs($this->user);
        Notification::fake();
        $response = $this->postJson($this->resetPasswordRoute, [
            'email' => $this->user->email
        ]);
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'success',
                 ]);

        Notification::assertSentTo($this->user, \Illuminate\Auth\Notifications\ResetPassword::class);
    }
    
    public function testResetPasswordEmailNotFound()
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson($this->resetPasswordRoute, [
            'email' => fake()->email()
        ]);
        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                    'data' => [
                        'email' => __('passwords.user'),
                    ],
                ]);

    }  
    
    public function testUserRegisterInvalidEmail() {
        $data = [
            "password" => $this->registerData['password'],
            "confirm_password" => $this->registerData['confirm_password'],
            "name" => $this->registerData['name'],
        ];
        $response = $this->postJson($this->registerRoute, $data);
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'email' => [],
                    ],
                ]);     
    } 
    public function testUserRegisterInvalidPassword() {
        $data = [
            "email" => $this->registerData['email'],
            "confirm_password" => $this->registerData['confirm_password'],
            "name" => $this->registerData['name'],
        ];
        $response = $this->postJson($this->registerRoute, $data);
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'password' => [],
                    ],
                ]);     
    } 
    public function testUserRegisterInvalidConfirmPassword() {
        $data = [
            "email" => $this->registerData['email'],
            "password" => $this->registerData['password'],
            "name" => $this->registerData['name'],
        ];
        $response = $this->postJson($this->registerRoute, $data);
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'error',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'confirm_password' => [],
                    ],
                ]);     
    } 
    public function testUserRegisterInvalidName() {
        $data = [
            "email" => $this->registerData['email'],
            "password" => $this->registerData['password'],
            "confirm_password" => $this->registerData['confirm_password'],
        ];
        $response = $this->postJson($this->registerRoute, $data);
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
}
