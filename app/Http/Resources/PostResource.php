<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use App\Models\Like;

class PostResource extends JsonResource
{
    private bool $withLikePermission = false;

    public function withLikePermission(): self {
        $this->withLikePermission = true;

        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'topic' => TopicResource::make($this->whenLoaded('topic')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'title' => $this->title,
            'body' => $this->body,
            'html' => str($this->html),
            'likes_count' => Number::abbreviate($this->likes_count),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'routes' => [
                'show' => $this->showRoute(),
            ],
            'can' => [
                'like' => $this->when($this->withLikePermission, fn () => $request->user()?->can('create', [Like::class, $this->resource])),
            ],
        ];
    }
}
