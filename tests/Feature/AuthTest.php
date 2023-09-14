<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    public function test_user_registration()
    {
        $userData = [
            'first_name'            => $this->faker->firstName,
            'last_name'             => $this->faker->lastName,
            'username'              => $this->faker->userName,
            'email'                 => $this->faker->unique()->safeEmail,
            'password'              => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'authorization' => [
                    'token',
                    'type',
                ],
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_can_not_registr_with_invalid_data()
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'email'      => 'invalid_email',
            'password'   => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email','username']);
    }

    public function test_user_login()
    {
        $user = User::factory()->create([
            'password' => 'PASWORD'
        ]);

        $credentials = [
            'username' => $user->username,
            'password' => 'PASWORD',
        ];

        $response = $this->postJson('/api/login', $credentials);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'authorization' => [
                    'token',
                    'type',
                ],
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_user_can_not_login_with_invalid_data()
    {
        $credentials = [
            'username' => 'invalid_email',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }
}
