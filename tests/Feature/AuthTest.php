<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register', function () {
    $response = $this->postJson('/api/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'success', 'message',
                 'data' => ['user', 'token'],
             ]);
});

test('user can login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success', 'message',
                 'data' => ['user', 'token'],
             ]);
});

test('user cannot login with wrong password', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422);
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->postJson('/api/logout');

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
});
