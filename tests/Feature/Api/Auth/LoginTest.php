<?php

use App\Models\User;
use Illuminate\Http\Response;

use function Pest\Laravel\postJson;

test('should auth user', function () {
    $user = User::factory()->create();
    $data = [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'acl'
    ];
    postJson(route('auth.login'), $data)
        ->assertOk()
        ->assertJsonStructure(['token']);
});

test('should fail - with wrong password', function () {
    $user = User::factory()->create();

    $data = [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'acl'
    ];

    postJson(route('auth.login'), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

test('should fail - with wrong email', function () {
    $user = User::factory()->create();

    $data = [
        'email' => 'fake@gmail.com',
        'password' => 'password',
        'device_name' => 'acl'
    ];

    postJson(route('auth.login'), $data)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

describe('validations', function () {

    it('should required email', function () {
        postJson(route('auth.login'), [
            'password' => 'password',
            'device_name' => 'acl'
        ])->assertJsonValidationErrors([
            'email' => trans('validation.required', ['attribute' => 'email'])
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });


    it('should required password', function () {
        $user = User::factory()->create();
        postJson(route('auth.login'), [
            'email' => $user->email,
            'device_name' => 'acl'
        ])->assertJsonValidationErrors([
            'password' => trans('validation.required', ['attribute' => 'password'])
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });

    it('should required device name', function () {
        $user = User::factory()->create();
        postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password'
        ])->assertJsonValidationErrors([
            'device_name' => trans('validation.required', ['attribute' => 'device name'])
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    });
});
