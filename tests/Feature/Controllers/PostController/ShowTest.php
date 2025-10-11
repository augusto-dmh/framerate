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

    get($post->showRoute())
        ->assertComponent('Posts/Show');
});

it('passes a post to the view', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    Comment::factory(2)->for($post)->create();

    $post->load('user', 'topic');

    get($post->showRoute())
        ->assertHasResource('post', PostResource::make($post));
});

it('passes comments to the view', function () {
    $post = Post::factory()->create();
    $comments = Comment::factory(2)->for($post)->create()->reverse();

    $comments->load('user');

    get($post->showRoute())
        ->assertOk()
        ->assertHasPaginatedResource('comments', CommentResource::collection($comments));
});

it('will redirect if the slug is incorrect', function () {
    $post = Post::factory()->create(['title' => 'Some Title']);

    get(route('posts.show', ['post' => $post, 'slug' => 'some-tit;r', 'some-arg' => 1]))
        ->assertRedirect($post->showRoute(['some-arg' => 1]));
});
