<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Dashboard Tests

test('admin can access dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    // Create some data
    User::factory()->count(5)->create();
    Post::factory()->count(10)->create(['status' => 'approved']);
    Post::factory()->count(3)->create(['status' => 'pending']);
    Post::factory()->count(2)->create(['status' => 'rejected']);
    Comment::factory()->count(15)->create();
    Category::factory()->count(4)->create();
    Tag::factory()->count(8)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/admin/dashboard');

    $response->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'total_users',
                     'total_posts',
                     'total_comments',
                     'total_categories',
                     'total_tags',
                     'pending_posts',
                     'approved_posts',
                     'rejected_posts',
                 ],
             ]);
});

test('non-admin cannot access dashboard', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/admin/dashboard');

    $response->assertStatus(403);
});

// Post Management Tests

test('admin can get all posts including pending', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    Post::factory()->count(5)->create(['status' => 'approved']);
    Post::factory()->count(3)->create(['status' => 'pending']);
    Post::factory()->count(2)->create(['status' => 'rejected']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/admin/posts');

    $response->assertStatus(200)
             ->assertJsonCount(10, 'data');
});

test('admin can filter posts by status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    Post::factory()->count(5)->create(['status' => 'approved']);
    Post::factory()->count(3)->create(['status' => 'pending']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/admin/posts?status=pending');

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
});

test('admin can approve a post', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $post = Post::factory()->create(['status' => 'pending']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/posts/{$post->id}/approve");

    $response->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonPath('data.status', 'approved');

    expect($post->fresh()->status)->toBe('approved');
});

test('admin can reject a post', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $post = Post::factory()->create(['status' => 'pending']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/posts/{$post->id}/reject");

    $response->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonPath('data.status', 'rejected');

    expect($post->fresh()->status)->toBe('rejected');
});

test('admin can delete any post', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $post = Post::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/posts/{$post->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true]);

    expect(Post::find($post->id))->toBeNull();
});

test('non-admin cannot approve posts', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('auth_token')->plainTextToken;
    $post = Post::factory()->create(['status' => 'pending']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/posts/{$post->id}/approve");

    $response->assertStatus(403);
});

// User Management Tests

test('admin can get all users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    User::factory()->count(10)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/admin/users');

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
});

test('admin can get single user details', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $user = User::factory()->create();

    // Create some posts and comments for the user
    Post::factory()->count(3)->create(['user_id' => $user->id]);
    Comment::factory()->count(5)->create(['user_id' => $user->id]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson("/api/admin/users/{$user->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonPath('data.posts_count', 3)
             ->assertJsonPath('data.comments_count', 5);
});

test('admin can change user role', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/users/{$user->id}/role", [
                         'role' => 'author',
                     ]);

    $response->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonPath('data.role', 'author');

    expect($user->fresh()->role)->toBe('author');
});

test('admin cannot change role to invalid value', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->putJson("/api/admin/users/{$user->id}/role", [
                         'role' => 'superadmin',
                     ]);

    $response->assertStatus(422);
});

test('admin can delete user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $user = User::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/users/{$user->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true]);

    expect(User::find($user->id))->toBeNull();
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/users/{$admin->id}");

    $response->assertStatus(403)
             ->assertJson(['success' => false]);

    expect(User::find($admin->id))->not->toBeNull();
});

// Comment Management Tests

test('admin can get all comments', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    Comment::factory()->count(15)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/admin/comments');

    $response->assertStatus(200)
             ->assertJson(['success' => true]);
});

test('admin can delete any comment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;
    $comment = Comment::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/comments/{$comment->id}");

    $response->assertStatus(200)
             ->assertJson(['success' => true]);

    expect(Comment::find($comment->id))->toBeNull();
});

test('non-admin cannot delete comments via admin route', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('auth_token')->plainTextToken;
    $comment = Comment::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->deleteJson("/api/admin/comments/{$comment->id}");

    $response->assertStatus(403);
});

// Post Status Tests

test('only approved posts are visible to public', function () {
    Post::factory()->count(5)->create(['status' => 'approved']);
    Post::factory()->count(3)->create(['status' => 'pending']);
    Post::factory()->count(2)->create(['status' => 'rejected']);

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
             ->assertJsonCount(5, 'data');
});

test('admin can see all posts in public endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('auth_token')->plainTextToken;

    Post::factory()->count(5)->create(['status' => 'approved']);
    Post::factory()->count(3)->create(['status' => 'pending']);
    Post::factory()->count(2)->create(['status' => 'rejected']);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/posts');

    $response->assertStatus(200)
             ->assertJsonCount(10, 'data');
});

test('new posts default to pending status', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->postJson('/api/posts', [
                         'title' => 'Test Post',
                         'content' => 'Test content',
                     ]);

    $response->assertStatus(201);

    $post = Post::latest()->first();
    expect($post->status)->toBe('pending');
});
