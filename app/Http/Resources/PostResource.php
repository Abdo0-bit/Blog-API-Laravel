<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'=> 'post',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'image_url'=>$this->image ? asset('storage/' . $this->image) : null, 
                'image_path' => $this->image, 
                'content' => $this->content,
                'created_at' => $this->created_at->toDateTimeString(),
                'updated_at' => $this->updated_at->toDateTimeString(),
            ],
            'relationships' => [
                'user' => new UserResource($this->whenLoaded('user')),
                'comments' => $this->whenLoaded('comments', function () {
                    return $this->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'content' => $comment->content,
                            'user' => [
                                'id' => $comment->user->id,
                                'name' => $comment->user->name,
                            ],
                            'created_at' => $comment->created_at->toDateTimeString(),
                            'updated_at' => $comment->updated_at->toDateTimeString(),
                        ];
                    });
                }),
            ],

            'controls' => [
                'update_url' => $this->when(
                    Auth::user()?->can('update', $this->resource),
                    route('posts.update', $this->id)
                ),
                'delete_url' => $this->when(
                    Auth::user()?->can('delete', $this->resource),
                    route('posts.destroy', $this->id)
                ),
            ],

            'links' => [
                'self' => route('posts.show', $this->id),
            ],
        ];
    }
}
