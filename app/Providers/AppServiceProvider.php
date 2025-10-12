<?php

namespace App\Providers;

use App\Http\Resources\PostResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Post;
use App\Models\Comment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PostResource::withoutWrapping();

        Model::preventLazyLoading();

        Model::unguard();

        Relation::enforceMorphMap([
            'post' => Post::class,
            'comment' => Comment::class,
        ]);
    }
}
