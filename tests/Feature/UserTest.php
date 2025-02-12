<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class UserTest extends TestCase
{
    public function testRegisterSuccess() 
    {
        $this->post('/api/users', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'Test User',
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "Test User",
                ]
            ]);
    }

    public function testRegisterFailed() 
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                ]
            ]);

    }

    public function testRegisterUsernameAlreadyExists() 
    {
        $this->testRegisterSuccess();

        $this->post('/api/users', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'Test User',
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "Username already exists"
                    ],
                ]
            ]);
    }

    public function testLoginSuccess() 
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "Test User",
                ]
            ]);
            
        
        $user = User::where('username', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailed() 
    {

    }
}
