<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

use App\Models\Post;
use App\Models\User;

beforeEach(function() {
    $this->validPostData = [
        'title' => 'some title',
        'body' => 'some body',
    ];
});

it('requires authentication', function () {
    post(route('posts.store'))->assertRedirectToRoute('login');
});

it('stores a post', function () {
    /** @var App\Models\User */
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), $this->validPostData);

    assertDatabaseHas(Post::class, [
        ...$this->validPostData,
        'user_id' => $user->id,
    ]);
});

it('redirects to the post show page', function () {
    /** @var App\Models\User */
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), $this->validPostData)
        ->assertRedirect(Post::query()->latest('id')->first()->showRoute());
});

it('requires valid data', function (array $badData, array|string $errors) {
    /** @var App\Models\User */
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), [
            ...$this->validPostData,
            ...$badData,
        ])
        ->assertInvalid($errors);
})->with([
    [['title' => ''], 'title'],
    [['title' => null], 'title'],
    [['title' => 1], 'title'],
    [['title' => str_repeat('a', 121)], 'title'],

    [['body' => ''], 'body'],
    [['body' => null], 'body'],
    [['body' => 1], 'body'],
    [['body' => str_repeat('a', 65_536)], 'body'],
]);
