<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ApiResponse;
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request , Post $post)
    {
        $validatedData = $request->validated();

        try{
            $comment = $post->comments()->create([
                'content' => $validatedData['content'],
                'user_id' => $request->user()->id,
            ]);

            $comment->load('user', 'post');
            return $this->success(new CommentResource($comment), 'Comment created successfully', 201);
          
        }catch (\Exception $e) {
            return $this->error('Failed to create comment', $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $validatedData = $request->validated();

        try{
            $comment->update([
                'content' => $validatedData['content'],
            ]);

            $comment->load('user', 'post');
            return $this->success(new CommentResource($comment), 'Comment updated successfully', 200);

        }catch(\Exception $e) {
            return $this->error('Failed to update comment', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        try{
            $comment->delete();

            return $this->success(null, 'Comment deleted successfully', 200);
            
        } catch (\Exception $e) {
            return $this->error('Failed to delete comment', $e->getMessage(), 500);
        }
    }
}
