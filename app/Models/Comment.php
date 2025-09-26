<?php

namespace App\Models;

use App\Models\Concerns\ConvertsMarkdownToHtml;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    use ConvertsMarkdownToHtml;

    public function post(): BelongsTo {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
