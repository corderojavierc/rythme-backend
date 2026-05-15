<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can register a user', function (): void {
    $data = [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = postJson('/api/register', $data);

    $response->assertCreated()
        ->assertJsonStructure(['user', 'token']);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'username' => 'johndoe',
    ]);
});

it('can login a user', function (): void {
    $user = User::factory()->create([
        'username' => 'jane',
        'password' => Hash::make('password123'),
    ]);

    $response = postJson('/api/login', [
        'username' => 'jane',
        'password' => 'password123',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['user', 'token']);
});

it('cannot login with invalid credentials', function (): void {
    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => Hash::make('password123'),
    ]);

    $response = postJson('/api/login', [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['credentials']);
});

it('cannot register with duplicate username', function (): void {
    User::factory()->create(['username' => 'duplicated']);

    $response = postJson('/api/register', [
        'name' => 'Other User',
        'username' => 'duplicated',
        'email' => 'other@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username']);
});

it('cannot register with duplicate email', function (): void {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = postJson('/api/register', [
        'name' => 'Test User',
        'username' => 'newuser',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('cannot register when password confirmation does not match', function (): void {
    $response = postJson('/api/register', [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('cannot register with missing required fields', function (string $field): void {
    $data = [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    unset($data[$field]);

    $response = postJson('/api/register', $data);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'username', 'email', 'password']);

it('cannot login with missing credentials', function (): void {
    $response = postJson('/api/login', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['username', 'password']);
});

it('can logout a user', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $response = $this->actingAs($user)->postJson('/api/logout');

    $response->assertSuccessful()
        ->assertJson(['message' => 'Sesión cerrada correctamente.']);
});
