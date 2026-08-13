<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_creates_user_and_student_profile(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Alice Student',
            'email' => 'alice@university.edu',
            'password' => 'secret123',
            'role' => 'student',
            'roll_no' => 'CS2026-001',
            'program' => 'B.Tech',
            'department' => 'Computer Science',
            'year' => '4th Year',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => [
                         'user' => ['id', 'name', 'email', 'role', 'student'],
                         'token',
                     ],
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'alice@university.edu', 'role' => 'student']);
        $this->assertDatabaseHas('students', ['roll_no' => 'CS2026-001', 'program' => 'B.Tech']);
    }

    public function test_company_registration_creates_user_and_company_profile(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Acme Admin',
            'email' => 'recruiter@acme.com',
            'password' => 'secret123',
            'role' => 'company',
            'company_name' => 'Acme Technologies',
            'website' => 'https://acme.com',
            'sector' => 'private',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('companies', ['name' => 'Acme Technologies', 'is_external' => false]);
    }

    public function test_login_returns_valid_bearer_token(): void
    {
        $user = User::create([
            'name' => 'Bob Test',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'bob@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.user.email', 'bob@example.com');

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_rejects_incorrect_password(): void
    {
        User::create([
            'name' => 'Bob Test',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'bob@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJsonPath('success', false);
    }
}
