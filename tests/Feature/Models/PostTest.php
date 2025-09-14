<?php

use App\Models\Post;

it('uses title case for titles', function () {
    $post = Post::factory()->create(['title' => 'Hello, how are you?']);

    expect($post->title)->toBe('Hello, How Are You?');
});

it('can generate a route to the show page', function () {
    $post = Post::factory()->create(['title' => 'Some title WITH sTrAnGe #formatting']);

    expect($post->showRoute())->toBe(route('posts.show', [$post, 'some-title-with-strange-formatting']));
});

it('can generate additional query parameters on the show route', function () {
    $post = Post::factory()->create(['title' => 'Some title WITH sTrAnGe #formatting']);

    expect($post->showRoute(['some-arg' => 1, 'another-arg' => 2]))
        ->toBe(route('posts.show', [
            'post' => $post,
            'slug' => 'some-title-with-strange-formatting',
            'some-arg' => 1,
            'another-arg' => 2,
        ]));
});
