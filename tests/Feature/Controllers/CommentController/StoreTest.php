<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\withExceptionHandling;

use App\Models\Comment;

it('can store a post', function () {
    $user = User::factory()->create(); /** @var \App\Models\User $user */
    $post = Post::factory()->create();

    $request = actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'This is a comment',
    ]);

    assertDatabaseHas(Comment::class, [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'body' => 'This is a comment',
    ]);
});

it('redirects to the post show page', function () {
    $user = User::factory()->create(); /** @var \App\Models\User $user */
    $post = Post::factory()->create();

    $request = actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'This is a comment',
    ]);

    $request->assertRedirect(route('posts.show', $post));
});

it('requires a valid body', function ($value) {
    withExceptionHandling();
    $user = User::factory()->create(); /** @var \App\Models\User $user */
    $post = Post::factory()->create();

    $request = actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => $value,
    ]);

    $request->assertInvalid('body');
})->with([
        null,
        1,
        1.5,
        true,
        str_repeat('a', 2501),
    ]);
