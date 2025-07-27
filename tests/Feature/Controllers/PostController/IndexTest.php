<?php

use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\get;
use App\Models\Post;
use App\Http\Resources\PostResource;

it('should return the correct component', function () {
    $this->withoutExceptionHandling();

    get(route('posts.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->component('Posts/Index', true)
        );
});

it('passes posts to the view', function () {
    $posts = Post::factory(3)->create();

    get(route('posts.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->hasCollection('posts', $posts)
            ->hasResource('posts', PostResource::make($posts->first()))
        );
});
