<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // GET /api/admin/dashboard - Dashboard statistics
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_users'         => User::count(),
            'total_posts'         => Post::count(),
            'total_comments'      => Comment::count(),
            'total_categories'    => Category::count(),
            'total_tags'          => Tag::count(),
            'pending_posts'       => Post::where('status', 'pending')->count(),
            'approved_posts'      => Post::where('status', 'approved')->count(),
            'rejected_posts'      => Post::where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics retrieved successfully',
            'data'    => $stats,
        ]);
    }

    // GET /api/admin/posts - Get all posts (including pending/rejected)
    public function posts(Request $request): JsonResponse
    {
        $query = Post::query();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

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

    // PUT /api/admin/posts/{id}/approve - Approve a post
    public function approvePost(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        
        $post->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Post approved successfully',
            'data'    => $post->load(['user:id,name,email', 'category', 'tags']),
        ]);
    }

    // PUT /api/admin/posts/{id}/reject - Reject a post
    public function rejectPost(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        
        $post->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Post rejected successfully',
            'data'    => $post->load(['user:id,name,email', 'category', 'tags']),
        ]);
    }

    // DELETE /api/admin/posts/{id} - Delete any post
    public function deletePost(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        // Delete image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
            'data'    => null,
        ]);
    }

    // GET /api/admin/users - Get all users
    public function users(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
                     ->latest()
                     ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data'    => $users->items(),
            'pagination' => [
                'total'        => $users->total(),
                'per_page'     => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'from'         => $users->firstItem(),
                'to'           => $users->lastItem(),
            ],
        ]);
    }

    // GET /api/admin/users/{id} - Get single user
    public function showUser(int $id): JsonResponse
    {
        $user = User::select('id', 'name', 'email', 'role', 'created_at', 'updated_at')
                    ->findOrFail($id);

        // Get user's posts count
        $postsCount = Post::where('user_id', $id)->count();
        $commentsCount = Comment::where('user_id', $id)->count();

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data'    => [
                'user'           => $user,
                'posts_count'    => $postsCount,
                'comments_count' => $commentsCount,
            ],
        ]);
    }

    // PUT /api/admin/users/{id}/role - Change user role
    public function changeUserRole(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,author,user',
        ]);

        $user = User::findOrFail($id);

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully',
            'data'    => $user,
        ]);
    }

    // DELETE /api/admin/users/{id} - Delete user
    public function deleteUser(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
            'data'    => null,
        ]);
    }

    // GET /api/admin/comments - Get all comments
    public function comments(): JsonResponse
    {
        $comments = Comment::with(['user:id,name,email', 'post:id,title'])
                           ->latest()
                           ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Comments retrieved successfully',
            'data'    => $comments->items(),
            'pagination' => [
                'total'        => $comments->total(),
                'per_page'     => $comments->perPage(),
                'current_page' => $comments->currentPage(),
                'last_page'    => $comments->lastPage(),
                'from'         => $comments->firstItem(),
                'to'           => $comments->lastItem(),
            ],
        ]);
    }

    // DELETE /api/admin/comments/{id} - Delete any comment
    public function deleteComment(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
            'data'    => null,
        ]);
    }
}
