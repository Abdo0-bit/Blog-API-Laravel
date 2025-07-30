<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'comment',
            'id' => $this->id,
            'attributes' => [
                'content' => $this->content,
                'created_at' => $this->created_at->toDateTimeString(),
                'updated_at' => $this->updated_at->toDateTimeString(),
            ],
            'relationships' => [
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ],
                'post' => [
                    'id' => $this->post_id,
                    'title' => $this->post->title,
                ],
            ],
        ];
    }
}
