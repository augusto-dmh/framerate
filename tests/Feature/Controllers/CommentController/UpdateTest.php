<?php

use App\Models\Comment;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\put;
use App\Models\User;

it('requires authentication', function () {
    put(route('comments.update', Comment::factory()->create()))
        ->assertRedirect(route('login'));
});

it('can update a comment', function() {
    $comment = Comment::factory()->create(['body' => 'This is the old body']);
    $newBody = 'This is the new body';

    actingAs($comment->user)
        ->put(route('comments.update', $comment), ['body' => $newBody]);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'body' => $newBody,
    ]);
});

it('redirects to the post show page', function() {
    $comment = Comment::factory()->create();

    actingAs($comment->user)->put(route('comments.update', $comment), ['body' => 'some body'])
        ->assertRedirect($comment->post->showRoute());
});

it('redirects to the correct page of comments', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)->put(route('comments.update', ['comment' => $comment, 'page' => 10, 'body' => 'some body']))
        ->assertRedirect($comment->post->showRoute(['page' => 10]));
});

it('cannot update a comment from another user', function () {
    /** @var \App\Models\User $nonAuthor */
    $nonAuthor = User::factory()->create();
    $comment = Comment::factory()->create();

    actingAs($nonAuthor)->put(route('comments.update', $comment))
        ->assertForbidden();
});

it('requires a valid body', function ($invalidBody) {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', $comment), ['body' => $invalidBody])
        ->assertInvalid('body');
})->with([
    null,
    true,
    1,
    1.5,
    str_repeat('a', 2501),
]);
