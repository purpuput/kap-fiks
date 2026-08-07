<?php

use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;

test('login screen can be rendered', function () {
    $response = get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $response = post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $response->assertSessionHas('success', 'Login berhasil! Selamat datang kembali.');
});

test('users can not authenticate with invalid password', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    assertGuest();
});

test('users can logout', function () {
    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    actingAs($user);
    $response = post('/logout');

    assertGuest();
    $response->assertRedirect('/');
});
