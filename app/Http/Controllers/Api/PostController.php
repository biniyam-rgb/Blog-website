<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // GET /api/posts — public, return all posts with search & filters
    public function index(Request $request): JsonResponse
    {
        $query = Post::query();

        // Search by title or content
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->input('tag'));
            });
        }

        // Eager load relationships and paginate
        $posts = $query->with(['user:id,name,email', 'category', 'tags'])
                       ->latest()
                       ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Posts retrieved successfully',
            'data'    => $posts->items(),
            'pagination' => [
                'total'        => $posts->total(),
                'per_page'     => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'from'         => $posts->firstItem(),
                'to'           => $posts->lastItem(),
            ],
        ]);
    }

    // GET /api/posts/{id} — public, return single post
    public function show(int $id): JsonResponse
    {
        $post = Post::with(['user:id,name,email', 'category', 'tags'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Post retrieved successfully',
            'data'    => $post,
        ]);
    }

    // POST /api/posts — authenticated users only
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'title'       => $validated['title'],
            'content'     => $validated['content'],
            'user_id'     => auth()->id(),
            'category_id' => $validated['category_id'] ?? null,
            'image'       => $imagePath,
        ]);

        if (!empty($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data'    => $post->load(['user:id,name,email', 'category', 'tags']),
        ], 201);
    }

    // PUT /api/posts/{id} — only the post owner can update
    public function update(Request $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - you can only update your own posts',
            ], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image) {
                \Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update([
            'title'       => $validated['title'],
            'content'     => $validated['content'],
            'category_id' => $validated['category_id'] ?? null,
            'image'       => $validated['image'] ?? $post->image,
        ]);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data'    => $post->load(['user:id,name,email', 'category', 'tags']),
        ]);
    }

    // DELETE /api/posts/{id} — owner or admin
    public function destroy(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $user = auth()->user();

        if ($post->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - only the owner or admin can delete this post',
            ], 403);
        }

        // Delete image if exists
        if ($post->image) {
            \Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
            'data'    => null,
        ]);
    }
}
