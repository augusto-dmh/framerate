<?php

use App\Models\Comment;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use App\Models\User;

it('requires authentication', function () {
    delete(route('comments.destroy', Comment::factory()->create()))
        ->assertRedirect(route('login'));
});

it('deletes a comment', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)->delete(route('comments.destroy', $comment));

    assertModelMissing($comment);
});

it('redirects to the post show page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment))
        ->assertRedirectToRoute('posts.show', $comment->post_id);
});

it('prevents deleting a comment you didnt create', function () {
    $comment = Comment::factory()->create();
    /** @var \App\Models\User $nonAuthor */
    $nonAuthor = User::factory()->create();

    actingAs($nonAuthor)
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
});
