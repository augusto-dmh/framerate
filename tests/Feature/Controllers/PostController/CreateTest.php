<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

use App\Models\User;
use App\Models\Topic;
use App\Http\Resources\TopicResource;

it('requires authentication', function () {
    get(route('posts.create'))->assertRedirectToRoute('login');
});

it('returns the correct component', function () {
    /** @var App\Models\User */
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('posts.create'))
        ->assertComponent('Posts/Create');
});

it('passes topics to the view', function () {
    /** @var App\Models\User */
    $user = User::factory()->create();
    $topics = Topic::factory(2)->create();

    actingAs($user)
        ->get(route('posts.create'))
        ->assertHasResource('topics', TopicResource::collection($topics));
});
