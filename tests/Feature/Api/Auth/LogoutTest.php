<?php

use App\Models\User;

use function Pest\Laravel\postJson;

it('user authenticated should can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test_e2e')->plainTextToken;

    postJson(route('auth.logout'), [], [
        'Authorization' => "Bearer {$token}"
    ])->assertNoContent();
});


it('user authenticated cannot logout logout', function () {
    postJson(route('auth.logout'), [])
        ->assertJson([
            'message' => 'Unauthenticated.'
        ])
        ->assertUnauthorized();
});
