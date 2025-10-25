<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Concerns\ConvertsMarkdownToHtml;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    use ConvertsMarkdownToHtml;
    use Searchable;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function title(): Attribute {
        return Attribute::set(fn ($value) => Str::title($value));
    }

    public function topic(): BelongsTo {
        return $this->belongsTo(Topic::class);
    }

    public function showRoute(array $parameters = []) {
        return route('posts.show', [$this, Str::slug($this->title), ...$parameters]);
    }
}
