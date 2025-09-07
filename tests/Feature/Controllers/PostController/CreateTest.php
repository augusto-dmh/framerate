<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

use App\Models\User;

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
