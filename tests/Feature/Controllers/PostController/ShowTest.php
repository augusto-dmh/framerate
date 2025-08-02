<?php

use App\Models\Post;
use function Pest\Laravel\get;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Inertia\Testing\AssertableInertia;
use App\Models\User;

it('can show a post', function () {
    $post = Post::factory()->create();

    get(route('posts.show', $post))
        ->assertComponent('Posts/Show');
});

it('passes a post to the view', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    Comment::factory(2)->for($post)->create();

    $post->load('user');

    get(route('posts.show', $post))
        ->assertHasResource('post', PostResource::make($post));
});

it('passes comments to the view', function () {
    $post = Post::factory()->create();
    $comments = Comment::factory(2)->for($post)->create()->reverse();

    $comments->load('user');

    get(route('posts.show', $post))
        ->assertOk()
        ->assertHasPaginatedResource('comments', CommentResource::collection($comments));
});
