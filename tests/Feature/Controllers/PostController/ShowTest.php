<?php

use App\Models\Post;
use function Pest\Laravel\get;
use App\Http\Resources\PostResource;

it('can show a post', function () {
    $post = Post::factory()->create();

    get(route('posts.show', $post))
        ->assertComponent('Posts/Show');
});

it('passes a post to the view', function () {
    $post = Post::factory()
        ->create()
        ->load('user');

    get(route('posts.show', $post))
        ->assertHasResource('post', PostResource::make($post));
});
