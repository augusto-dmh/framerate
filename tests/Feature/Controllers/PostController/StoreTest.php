<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

use App\Models\Post;
use App\Models\Topic;
use App\Models\User;

beforeEach(function() {
    $this->validPostData = fn () => [
        'title' => 'some title',
        'topic_id' => Topic::factory()->create()->getKey(),
        'body' => 'some body',
    ];
});

it('requires authentication', function () {
    post(route('posts.store'))->assertRedirectToRoute('login');
});

it('stores a post', function () {
    /** @var App\Models\User */
    $user = User::factory()->create();
    $data = value($this->validPostData);

    actingAs($user)->post(route('posts.store'), $data);

    assertDatabaseHas(Post::class, [
        ...$data,
        'user_id' => $user->id,
    ]);
});

it('redirects to the post show page', function () {
    /** @var App\Models\User */
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), value($this->validPostData))
        ->assertRedirect(Post::query()->latest('id')->first()->showRoute());
});

it('requires valid data', function (array $badData, array|string $errors) {
    /** @var App\Models\User */
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), [
            ...value($this->validPostData),
            ...$badData,
        ])
        ->assertInvalid($errors);
})->with([
    [['title' => ''], 'title'],
    [['title' => null], 'title'],
    [['title' => 1], 'title'],
    [['title' => str_repeat('a', 121)], 'title'],
    [['topic_id' => null], 'topic_id'],
    [['topic_id' => -1], 'topic_id'],
    [['body' => ''], 'body'],
    [['body' => null], 'body'],
    [['body' => 1], 'body'],
    [['body' => str_repeat('a', 65_536)], 'body'],
]);
