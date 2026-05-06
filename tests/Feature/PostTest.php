<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('anyone can get all posts', function () {
    Post::factory()->count(3)->create();

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success', 'message', 'data',
             ]);
});

test('authenticated user can create a post', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->postJson('/api/posts', [
                         'title'   => 'Test Post',
                         'content' => 'Test content',
                     ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true]);
});

test('unauthenticated user cannot create a post', function () {
    $response = $this->postJson('/api/posts', [
        'title'   => 'Test Post',
        'content' => 'Test content',
    ]);

    $response->assertStatus(401);
});

test('owner can update their post', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->putJson("/api/posts/{$post->id}", [
                         'title'   => 'Updated Title',
                         'content' => 'Updated content',
                     ]);

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
});

test('non-owner cannot update a post', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();
    $token = $other->createToken('auth_token')->plainTextToken;
    $post  = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->putJson("/api/posts/{$post->id}", [
                         'title'   => 'Hacked Title',
                         'content' => 'Hacked content',
                     ]);

    $response->assertStatus(403);
});

test('owner can delete their post', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $post  = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->deleteJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
});

test('post can be created with category and tags', function () {
    $user     = User::factory()->create();
    $token    = $user->createToken('auth_token')->plainTextToken;
    $category = Category::factory()->create();
    $tag      = Tag::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->postJson('/api/posts', [
                         'title'       => 'Post with category',
                         'content'     => 'Content here',
                         'category_id' => $category->id,
                         'tags'        => [$tag->id],
                     ]);

    $response->assertStatus(201)
             ->assertJsonPath('data.category.id', $category->id);
});


test('authenticated user can create a post with image', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    // Create a fake file
    $file = \Illuminate\Http\UploadedFile::fake()->create('test.jpg', 100);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->post('/api/posts', [
                         'title'   => 'Post with image',
                         'content' => 'Test content',
                         'image'   => $file,
                     ]);

    $response->assertStatus(201)
             ->assertJson(['success' => true])
             ->assertJsonStructure(['data' => ['image', 'image_url']]);

    // Verify image was stored
    $post = \App\Models\Post::latest()->first();
    expect($post->image)->not->toBeNull();
    \Storage::disk('public')->assertExists($post->image);
});

test('post image is deleted when post is deleted', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $file = \Illuminate\Http\UploadedFile::fake()->create('test.jpg', 100);

    // Create post with image
    $response = $this->withHeader('Authorization', 'Bearer '.$token)
                     ->post('/api/posts', [
                         'title'   => 'Post with image',
                         'content' => 'Test content',
                         'image'   => $file,
                     ]);

    $post = \App\Models\Post::latest()->first();
    $imagePath = $post->image;

    // Delete post
    $this->withHeader('Authorization', 'Bearer '.$token)
         ->deleteJson("/api/posts/{$post->id}");

    // Verify image was deleted
    \Storage::disk('public')->assertMissing($imagePath);
});
