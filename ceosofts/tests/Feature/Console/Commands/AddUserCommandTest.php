<?php

namespace Tests\Feature\Console\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_new_user()
    {
        $name = 'Test User';
        $email = 'test@example.com';

        $this->artisan('add:user', [
            'name' => $name,
            'email' => $email,
        ])
        ->expectsOutput("✓ User '{$name}' added successfully!")
        ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function test_command_validates_email_format()
    {
        $this->artisan('add:user', [
            'name' => 'Test User',
            'email' => 'invalid-email',
        ])
        ->expectsOutput('Invalid email format!')
        ->assertExitCode(1);

        $this->assertDatabaseMissing('users', [
            'name' => 'Test User',
        ]);
    }

    public function test_command_prevents_duplicate_emails()
    {
        // Create initial user
        User::factory()->create(['email' => 'existing@example.com']);

        $this->artisan('add:user', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
        ])
        ->expectsOutput("User with email 'existing@example.com' already exists!")
        ->assertExitCode(1);
    }

    public function test_command_accepts_custom_password()
    {
        $this->artisan('add:user', [
            'name' => 'Password User',
            'email' => 'password@example.com',
            '--password' => 'custom123',
        ])
        ->expectsOutput("✓ User 'Password User' added successfully!")
        ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'password@example.com',
        ]);
    }
}
