<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    protected $role;
    protected $user;
    protected $unAuthorizedRole;
    protected $unAuthorizedUser;
    protected $apiBasePath = '/api';
    protected $articlesRoute;
    protected $categoriesRoute;
    protected $sourcesRoute;
    protected $authorsRoute;
    protected $registerRoute;
    protected $loginRoute;
    protected $logoutRoute;
    protected $passwordRoute;
    protected $registerData;
    protected $loginData;
    protected $resetPasswordRoute;
    protected $preferencesRoute;
    protected $personalizedFeedRoute;
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerRoute = "{$this->apiBasePath}/register/";
        $this->loginRoute = "{$this->apiBasePath}/login/";
        $this->logoutRoute = "{$this->apiBasePath}/logout/";
        $this->resetPasswordRoute = "{$this->apiBasePath}/reset-password/";
        $this->articlesRoute = "{$this->apiBasePath}/articles/";
        $this->categoriesRoute = "{$this->apiBasePath}/categories/";
        $this->sourcesRoute = "{$this->apiBasePath}/sources/";
        $this->authorsRoute = "{$this->apiBasePath}/authors/";
        $this->preferencesRoute = "{$this->apiBasePath}/user/preferences/";
        $this->personalizedFeedRoute = "{$this->apiBasePath}/user/personalized-feed/";

        $this->role = Role::factory()->create(['name' => 'user']);
        $this->user = User::factory()->create([
            'role_id' => $this->role->id,
        ]);

        $this->unAuthorizedRole = Role::factory()->create(['name' => 'other']);
        $this->unAuthorizedUser = User::factory()->create([
            'role_id' => $this->unAuthorizedRole->id,
        ]);
        $password = fake()->password();
        $this->registerData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => $password,
            'confirm_password' => $password,
        ];
        $this->loginData = [
            "email" => $this->registerData['email'],
            "password" => $this->registerData['password'],
        ];
    }

}
