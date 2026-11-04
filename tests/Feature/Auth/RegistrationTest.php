<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'nama_user' => 'testbuyer',
        'account_type' => 'buyer',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('buyer.dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'nama_user' => 'testbuyer',
        'account_type' => 'buyer',
        'role' => 'owner',
    ]);
});

test('new sellers can register and are sent to their product dashboard', function () {
    $response = $this->post('/register', [
        'nama_user' => 'testseller',
        'account_type' => 'seller',
        'age' => 28,
        'gender' => 2,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('seller.dashboard', absolute: false));
    $this->assertDatabaseHas('users', [
        'nama_user' => 'testseller',
        'account_type' => 'seller',
        'role' => 'owner',
    ]);
});
