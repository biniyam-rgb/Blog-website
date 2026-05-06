<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /api/posts/{postId}/comments — public
    public function index(int $postId): JsonResponse
    {
        $comments = Comment::with(['user:id,name,email', 'replies.user'])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Comments retrieved successfully',
            'data'    => $comments,
        ]);
    }

    // POST /api/posts/{postId}/comments — authenticated
    public function store(Request $request, int $postId): JsonResponse
    {
        $validated = $request->validate([
            'content'   => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'content'   => $validated['content'],
            'user_id'   => auth()->id(),
            'post_id'   => $postId,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data'    => $comment->load('user:id,name,email'),
        ], 201);
    }

    // DELETE /api/comments/{id} — owner or admin
    public function destroy(int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $user    = auth()->user();

        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - only the owner or admin can delete this comment',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
            'data'    => null,
        ]);
    }
}
