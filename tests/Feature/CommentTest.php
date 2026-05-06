<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('anyone can get comments for a post', function () {
    $post = Post::factory()->create();
    Comment::factory()->count(3)->create(['post_id' => $post->id]);

    $response = $this->getJson("/api/posts/{$post->id}/comments");

    $response->assertStatus(200)
             ->assertJsonStructure(['success', 'message', 'data']);
});

test('authenticated user can create a comment', function () {
    $user  = User::factory()->create();
    $post  = Post::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->postJson("/api/posts/{$post->id}/comments", [
                         'content' => 'Nice post!',
                     ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true]);
});

test('unauthenticated user cannot create a comment', function () {
    $post = Post::factory()->create();

    $response = $this->postJson("/api/posts/{$post->id}/comments", [
        'content' => 'Nice post!',
    ]);

    $response->assertStatus(401);
});

test('user can reply to a comment', function () {
    $user    = User::factory()->create();
    $post    = Post::factory()->create();
    $token   = $user->createToken('auth_token')->plainTextToken;
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->postJson("/api/posts/{$post->id}/comments", [
                         'content'   => 'This is a reply!',
                         'parent_id' => $comment->id,
                     ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true]);
});

test('owner can delete their comment', function () {
    $user    = User::factory()->create();
    $post    = Post::factory()->create();
    $token   = $user->createToken('auth_token')->plainTextToken;
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->deleteJson("/api/comments/{$comment->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
});

test('non-owner cannot delete a comment', function () {
    $user    = User::factory()->create();
    $other   = User::factory()->create();
    $post    = Post::factory()->create();
    $token   = $other->createToken('auth_token')->plainTextToken;
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->deleteJson("/api/comments/{$comment->id}");

    $response->assertStatus(403);
});
